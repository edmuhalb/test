import 'package:auto_route/auto_route.dart';
import 'package:dio/dio.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:get_it/get_it.dart';
import 'package:talker_flutter/talker_flutter.dart';
import 'package:toastification/toastification.dart';

import '../../theme/theme.dart';
import '../../widgets/button/button.dart';
import 'widgets/sign_in_form.dart';
import '../../app_preferences.dart';
import '../../slivers/sliver_logo.dart';
import '../../repositories/team/team.dart';
import '../../widgets/scaffold/scaffold_body.dart';

const logoMaxPaddingTopRatio = 0.27;

@RoutePage()
class SignInScreen extends StatefulWidget {
  final void Function() onSignedIn;

  const SignInScreen({
    super.key,
    required this.onSignedIn,
  });

  @override
  State<SignInScreen> createState() => _SignInScreenState();
}

class _SignInScreenState extends State<SignInScreen>
    with WidgetsBindingObserver {
  final _formKey = GlobalKey<SignInFormState>();
  final _scrollController = ScrollController();
  bool _showSignInFailed = false;
  bool _submitting = false;

  bool get signInButtonEnabled => !_submitting;

  Dio get dio => GetIt.I<Dio>();

  TeamRepository get teamRepo => GetIt.I<TeamRepository>();

  AppPreferences get appPrefs => GetIt.I<AppPreferences>();

  double getScreenHeight(BuildContext context) =>
      MediaQuery.sizeOf(context).height;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeMetrics() {
    _ensureFormIsNotOverlappedWithKeyboard();
    super.didChangeMetrics();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: ScaffoldBody(
        color: Theme.of(context).cardColor,
        child: CustomScrollView(
          controller: _scrollController,
          physics: NeverScrollableScrollPhysics(),
          slivers: [
            SliverLogo(
              maxPaddingTop: getScreenHeight(context) * logoMaxPaddingTopRatio,
            ),
            SliverToBoxAdapter(
              child: Column(
                children: [
                  Gaps.h32,
                  SignInForm(key: _formKey),
                  Gaps.h16,
                  if (_showSignInFailed) ...[
                    Text(
                      AppPhrases.signInFailed,
                      style: Theme.of(context).inputDecorationTheme.errorStyle,
                    ),
                    Gaps.h8,
                  ],
                  Button(
                    label: Text(AppPhrases.signIn),
                    style: primaryButtonStyle,
                    isLoading: _submitting,
                    onPressed:
                        signInButtonEnabled ? _onSignInButtonPressed : null,
                  ),
                  Gaps.h16,
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _onSignInButtonPressed() async {
    final signInForm = _formKey.currentState!;
    final valid = _validate(signInForm);

    if (valid) {
      _submit(signInForm).then(_onSignedIn).onError(_onSignInFailed);
    }
  }

  bool _validate(SignInFormState signInForm) {
    final valid = signInForm.validate();

    if (!valid) {
      WidgetsBinding.instance.addPostFrameCallback(
        (_) => _animateScrollToMaxScrollExtent(),
      );
    }

    return valid;
  }

  Future<void> _submit(SignInFormState signInForm) async {
    FocusScope.of(context).unfocus();

    setState(() {
      _showSignInFailed = false;
      _submitting = true;
    });

    final phone = signInForm.phoneNumber;
    final pass = signInForm.password;

    try {
      await _signIn(phone, pass);
    } finally {
      setState(() => _submitting = false);
    }
  }

  Future<void> _signIn(String phone, String password) async {
    final response = await dio.post(
      "login_check",
      data: {"phone": phone, "password": password},
    );

    final authToken = response.data["token"];
    dio.options.headers["Authorization"] = "Bearer $authToken";
    appPrefs.authToken = authToken;
  }

  void _onSignedIn(void _) async {
    await _enablePushNotifications();

    widget.onSignedIn();
  }

  void _onSignInFailed(Object error, StackTrace stack) {
    GetIt.I<Talker>().debug('Sign in failed', error, stack);

    setState(() => _showSignInFailed = true);

    final signInForm = _formKey.currentState;
    signInForm!.passwordFocusNode.requestFocus();
    signInForm.selectAllPasswordFormFieldText();

    WidgetsBinding.instance.addPostFrameCallback(
      (_) => _animateScrollToMaxScrollExtent(),
    );
  }

  Future<void> _enablePushNotifications() async {
    try {
      final fcmToken = await FirebaseMessaging.instance.getToken();

      if (fcmToken == null) {
        throw Exception('FCM token is missing');
      }

      await dio.post('devices/$fcmToken', data: {});
    } catch (error, stack) {
      _onEnablePushNotificationsFailed(error, stack);
    }
  }

  void _onEnablePushNotificationsFailed(
    Object error,
    StackTrace stack,
  ) {
    GetIt.I<Talker>().debug('Enable push notifications failed', error, stack);
    FirebaseCrashlytics.instance.recordError(error, stack);
    toastification.show(
      title: Text(AppPhrases.enablePushNotificationsError),
      type: ToastificationType.error,
      style: ToastificationStyle.fillColored,
      backgroundColor: AppColors.errorToastBackground,
      primaryColor: AppColors.errorToastBackground,
      foregroundColor: AppColors.errorToastText,
      showIcon: false,
      showProgressBar: false,
      autoCloseDuration: const Duration(seconds: 5),
    );
  }

  void _ensureFormIsNotOverlappedWithKeyboard() {
    final keyboardHeight = MediaQuery.viewInsetsOf(context).bottom > 0
        ? MediaQuery.viewInsetsOf(context).bottom
        : MediaQuery.viewPaddingOf(context).bottom;
    if (keyboardHeight > 0) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        _scrollToMaxScrollExtent();
      });
    }
  }

  void _scrollToMaxScrollExtent() {
    if (!_scrollController.hasClients) return;

    final maxScrollExtent = _scrollController.position.maxScrollExtent;
    _scrollController.jumpTo(maxScrollExtent);
  }

  void _animateScrollToMaxScrollExtent({
    Duration duration = kThemeAnimationDuration,
    Curve curve = Curves.easeInOut,
  }) {
    final maxScrollExtent = _scrollController.position.maxScrollExtent;
    _scrollController.animateTo(
      maxScrollExtent,
      duration: duration,
      curve: curve,
    );
  }
}

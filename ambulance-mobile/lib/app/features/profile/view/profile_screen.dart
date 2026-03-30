import 'dart:async';

import 'package:ambulance/app/features/profile/block/profile_bloc.dart';
import 'package:ambulance/app/repositories/user/abstract_user_repository.dart';
import 'package:ambulance/app/widgets/scaffold/scaffold_body.dart';
import 'package:ambulance/app/widgets/widgets.dart';
import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:mask_text_input_formatter/mask_text_input_formatter.dart';

import '../../../theme/theme.dart';

@RoutePage()
class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _profile = ProfileBloc(GetIt.I<AbstractUserRepository>());
  final phoneMask = MaskTextInputFormatter(
    mask: '+# (###) ###-##-##',
    filter: {"#": RegExp(r'[0-9]')},
    type: MaskAutoCompletionType.lazy,
  );

  @override
  void initState() {
    _profile.add(LoadProfile());
    super.initState();
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
  }

  Future<void> _onRefresh() async {
    final completer = Completer();
    return completer.future;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(AppPhrases.profile),
      ),
      endDrawer: const AppDrawer(),
      body: ScaffoldBody(
        child: RefreshIndicator(
          onRefresh: _onRefresh,
          child: BlocBuilder<ProfileBloc, ProfileState>(
              bloc: _profile,
              builder: (context, state) {
                if (state is ProfileLoaded) {
                  final user = state.user;

                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      Gaps.wInfinity,
                      Gaps.h24,
                      Image(
                        image: AssetImage(AppImages.avatar),
                        width: 64,
                        height: 64,
                      ),
                      Gaps.h16,
                      Text(
                        user.name,
                        style: Theme.of(context).textTheme.headlineMedium,
                      ),
                      Gaps.h4,
                      Text(
                        phoneMask.maskText(user.phone),
                        style: Theme.of(context)
                            .textTheme
                            .bodyMedium
                            ?.copyWith(color: AppColors.primary),
                      ),
                    ],
                  );
                }

                if (state is ProfileLoadFailed) {
                  return const Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        Text('Что-то пошло не так'),
                        Text('Пожалуйста, повторите попытку позже'),
                        SizedBox(height: 30),
                      ],
                    ),
                  );
                }

                return const Center(child: CircularProgressIndicator());
              }),
        ),
      ),
    );
  }
}

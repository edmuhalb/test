import 'package:ambulance/app/theme/theme.dart';
import 'package:flutter/material.dart';
import 'package:mask_text_input_formatter/mask_text_input_formatter.dart';

import '../../../helpers/helpers.dart';

const phoneNumberCode = '7';
const phoneNumberRegExp = r'^\d{10}$';
const phoneNumberHintText = '(000) 000-00-00';
const phoneNumberMask = '(###) ###-##-##';

class SignInForm extends StatefulWidget {
  const SignInForm({super.key});

  @override
  State<SignInForm> createState() => SignInFormState();
}

// TODO: reset errors on focus
class SignInFormState extends State<SignInForm> {
  final _formKey = GlobalKey<FormState>();
  final _passwordEditingController = TextEditingController();
  final _phoneNumberMaskFormatter = MaskTextInputFormatter(
    mask: phoneNumberMask,
    filter: {"#": RegExp(r'[0-9]')},
    type: MaskAutoCompletionType.lazy,
  );
  final passwordFocusNode = FocusNode();

  String get rawPhoneNumber => _phoneNumberMaskFormatter.getUnmaskedText();

  String get phoneNumber => '$phoneNumberCode$rawPhoneNumber';

  String get password => _passwordEditingController.text;

  @override
  void dispose() {
    _passwordEditingController.dispose();
    super.dispose();
  }

  bool validate() => _formKey.currentState!.validate();

  void selectAllPasswordFormFieldText() {
    _passwordEditingController.selection = TextSelection(
      baseOffset: 0,
      extentOffset: _passwordEditingController.text.length,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Theme(
      data: Theme.of(context).copyWith(
        inputDecorationTheme: signInFormInputDecorationTheme,
      ),
      child: Form(
        key: _formKey,
        autovalidateMode: AutovalidateMode.onUnfocus,
        child: Column(
          children: [
            Text(
              AppPhrases.enterPhoneNumber,
              style: Theme.of(context).textTheme.labelSmall?.copyWith(
                    color: AppColors.text,
                  ),
            ),
            Gaps.h8,
            TextFormField(
              inputFormatters: [_phoneNumberMaskFormatter],
              validator: Validators.createPhoneNumberValidator(
                _phoneNumberMaskFormatter,
                phoneNumberRegExp,
              ),
              keyboardType: TextInputType.number,
              autocorrect: false,
              enableSuggestions: false,
              style: Theme.of(context).textTheme.bodyMedium,
              decoration: InputDecoration(
                prefixText: '+$phoneNumberCode ',
                hintText: phoneNumberHintText,
              ),
            ),
            Gaps.h16,
            Text(
              AppPhrases.enterPassword,
              style: Theme.of(context).textTheme.labelSmall?.copyWith(
                    color: AppColors.text,
                  ),
            ),
            Gaps.h8,
            TextFormField(
              focusNode: passwordFocusNode,
              controller: _passwordEditingController,
              validator: Validators.password,
              obscureText: true,
              autocorrect: false,
              enableSuggestions: false,
              style: Theme.of(context).textTheme.bodyMedium,
            ),
          ],
        ),
      ),
    );
  }
}

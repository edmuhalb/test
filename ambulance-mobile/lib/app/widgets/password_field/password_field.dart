import 'package:flutter/material.dart';
import 'package:ambulance/app/widgets/input_field/input_field.dart';

class PasswordField extends StatefulWidget {
  const PasswordField({
    super.key,
    this.label,
    this.hintText,
    this.validator,
    this.controller,
    this.obscureText = false,
  });

  final bool obscureText;
  final String? label;
  final String? hintText;
  final String? Function(String?)? validator;
  final TextEditingController? controller;

  @override
  State<PasswordField> createState() => _PasswordFieldState();
}

class _PasswordFieldState extends State<PasswordField> {
  bool _passwordVisible = true;

  _toggleVisibilityPassword() {
    setState(() {
      _passwordVisible = !_passwordVisible;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        InputField(
          label: widget.label,
          hintText: widget.hintText,
          controller: widget.controller,
          validator: widget.validator,
          keyboardType: TextInputType.visiblePassword,
          obscureText: _passwordVisible,
        ),
        Positioned(
          top: 0,
          right: 0,
          child: IconButton(
            icon: Icon(
              _passwordVisible ? Icons.visibility : Icons.visibility_off,
            ),
            onPressed: _toggleVisibilityPassword,
          ),
        )
      ],
    );
  }
}

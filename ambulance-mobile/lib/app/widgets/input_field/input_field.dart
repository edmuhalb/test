import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

class InputField extends StatelessWidget {
  const InputField({
    super.key,
    this.label,
    this.hintText,
    this.validator,
    this.keyboardType = TextInputType.text,
    this.controller,
    this.inputFormatters,
    this.minLines = 1,
    this.maxLines = 1,
    this.readOnly = false,
    this.onTap,
    this.textAlign = TextAlign.start,
    this.textCapitalization = TextCapitalization.none,
    this.obscureText = false,
  });

  final TextCapitalization textCapitalization;
  final TextAlign textAlign;
  final Function()? onTap;
  final bool readOnly;
  final bool obscureText;
  final String? label;
  final String? hintText;
  final int minLines;
  final int maxLines;
  final String? Function(String?)? validator;
  final TextInputType? keyboardType;
  final TextEditingController? controller;
  final List<TextInputFormatter>? inputFormatters;

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      obscureText: obscureText,
      textCapitalization: textCapitalization,
      autovalidateMode: AutovalidateMode.onUserInteraction,
      textAlign: textAlign,
      onTap: onTap,
      readOnly: readOnly,
      minLines: minLines,
      maxLines: maxLines,
      inputFormatters: inputFormatters,
      controller: controller,
      validator: validator,
      keyboardType: keyboardType,
      decoration: InputDecoration(
        contentPadding: const EdgeInsets.all(10),
        labelText: label,
        hintText: hintText,
        border: const OutlineInputBorder(
          borderRadius: BorderRadius.all(
            Radius.circular(5),
          ),
        ),
      ),
    );
  }
}

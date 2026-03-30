import 'package:flutter/material.dart';

class ModalWindow extends StatelessWidget {
  final String title;
  final String? content;
  final List<Widget>? actions;

  const ModalWindow({
    super.key,
    required this.title,
    this.content,
    this.actions,
  });

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: Text(title),
      content: content != null ? Text(content!) : null,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(5),
      ),
      actions: actions,
    );
  }
}

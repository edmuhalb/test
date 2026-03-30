import 'package:ambulance/app/theme/theme.dart';
import 'package:flutter/material.dart';

import '../button/button.dart';

DateTime _getInitialDate(String initialDate) {
  if (initialDate.isEmpty) {
    return DateTime.now();
  }
  return DateTime.parse(initialDate);
}

class DatePicker extends StatefulWidget {
  const DatePicker({
    super.key,
    this.hintText,
    this.validator,
    this.controller,
  });

  final String? hintText;
  final String? Function(String?)? validator;
  final TextEditingController? controller;

  @override
  State<DatePicker> createState() => _DatePickerState();
}

class _DatePickerState extends State<DatePicker> {
  String inputHintText = 'Выбрать дату';
  DateTime date = _getInitialDate('');

  @override
  void initState() {
    super.initState();
    if (widget.controller!.text.isNotEmpty) {
      final DateTime parsetDate = _getInitialDate(widget.controller!.text);

      final day = parsetDate.day.toString().padLeft(2, '0');
      final month = parsetDate.month.toString().padLeft(2, '0');
      final year = parsetDate.year;

      inputHintText = 'Дата: $day.$month.$year';
      widget.controller!.text = parsetDate.toIso8601String();
    }
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? selectedDate = await showDatePicker(
      context: context,
      initialDate: _getInitialDate(widget.controller!.text),
      firstDate: DateTime(1900),
      lastDate: DateTime(2050),
      cancelText: 'Закрыть',
      confirmText: 'Применить',
      fieldLabelText: 'Дата',
      helpText: 'Выберите дату',
      locale: const Locale("ru", "RU"),
    );

    if (selectedDate != null) {
      setState(() {
        final day = selectedDate.day.toString().padLeft(2, '0');
        final month = selectedDate.month.toString().padLeft(2, '0');
        final year = selectedDate.year;

        inputHintText = 'Дата: $day.$month.$year';
        widget.controller!.text = selectedDate.toIso8601String();
        date = selectedDate;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        Opacity(
          opacity: 0,
          child: TextFormField(
            readOnly: true,
            validator: widget.validator,
            controller: widget.controller,
          ),
        ),
        Positioned.fill(
          top: 0,
          left: 0,
          child: Button(
            label: Text(inputHintText),
            style: secondaryButtonStyle,
            // label: widget.controller!.text,
            onPressed: () => _selectDate(context),
          ),
        ),
      ],
    );
  }
}

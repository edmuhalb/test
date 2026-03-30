import 'package:ambulance/app/theme/theme.dart';
import 'package:flutter/material.dart';

import '../button/button.dart';

class TimePicker extends StatefulWidget {
  const TimePicker({
    super.key,
    this.hintText = 'Выбрать время',
    this.validator,
    this.controller,
  });

  final String? hintText;
  final String? Function(String?)? validator;
  final TextEditingController? controller;

  @override
  State<TimePicker> createState() => _TimePickerState();
}

class _TimePickerState extends State<TimePicker> {
  String inputHintText = 'Выбрать время';
  TimeOfDay time = TimeOfDay.now();

  @override
  void initState() {
    super.initState();

    if (widget.controller!.text.isNotEmpty) {
      final String value = widget.controller!.text;
      final DateTime parsetValue = DateTime.parse(value);

      final hour = parsetValue.hour;
      final minute = parsetValue.minute;
      final selectedTime = TimeOfDay(hour: hour, minute: minute);

      final String h = hour.toString().padLeft(2, '0');
      final String m = minute.toString().padLeft(2, '0');

      // final selectedTime = TimeOfDay(hour: int.parse(hour), minute: int.parse(minute));

      widget.controller!.text = '$h:$m:00';
      inputHintText = 'Время: $h:$m';
      time = selectedTime;
    }
  }

  Future<void> _selectTime(BuildContext context) async {
    final TimeOfDay? selectedTime = await showTimePicker(
      context: context,
      initialTime: time,
      initialEntryMode: TimePickerEntryMode.inputOnly,
      cancelText: 'Закрыть',
      confirmText: 'Применить',
      hourLabelText: 'Час',
      minuteLabelText: 'Минуты',
      builder: (BuildContext context, Widget? child) {
        return MediaQuery(
          data: MediaQuery.of(context).copyWith(alwaysUse24HourFormat: true),
          child: child ?? Container(),
        );
      },
    );

    if (selectedTime != null) {
      setState(() {
        final String h = selectedTime.hour.toString().padLeft(2, '0');
        final String m = selectedTime.minute.toString().padLeft(2, '0');

        widget.controller!.text = '$h:$m:00';
        inputHintText = 'Время: $h:$m';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        Opacity(
          opacity: 1,
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
            onPressed: () => _selectTime(context),
          ),
        ),
      ],
    );
    // return InputField(
    //   hintText: inputHintText,
    //   readOnly: true,
    //   onTap: () => _selectDate(context),
    //   textAlign: TextAlign.center,
    //   validator: widget.validator,
    //   controller: widget.controller,
    // );
  }
}

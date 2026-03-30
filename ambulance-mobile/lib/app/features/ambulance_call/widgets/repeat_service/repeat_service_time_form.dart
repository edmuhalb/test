import 'package:flutter/material.dart';
import 'package:numberpicker/numberpicker.dart';

import '../../../../theme/theme.dart';

// TODO: refactor (time picker form)
class RepeatTimeForm extends StatefulWidget {
  final int? initialHour;
  final int? intialMinute;
  const RepeatTimeForm({
    super.key,
    this.initialHour,
    this.intialMinute,
  });

  @override
  State<RepeatTimeForm> createState() => RepeatServiceTimeFormState();
}

class RepeatServiceTimeFormState extends State<RepeatTimeForm> {
  int _hour = 0;
  int _minute = 0;

  int get hour => _hour;
  int get minute => _minute;

  @override
  void initState() {
    super.initState();
    _hour = widget.initialHour ?? 0;
    _minute = widget.intialMinute ?? 0;
  }

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Stack(
          alignment: Alignment.center,
          children: [
            Container(
              width: 100,
              height: 50,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(Sizes.p16),
                color: Colors.white,
              ),
            ),
            NumberPicker(
              value: _hour,
              haptics: true,
              zeroPad: true,
              infiniteLoop: true,
              minValue: 0,
              maxValue: 23,
              onChanged: (value) => setState(() => _hour = value),
              textStyle: TextStyle(
                fontSize: 36,
                color: AppColors.text.withOpacity(0.2),
                fontWeight: FontWeight.w400,
              ),
              selectedTextStyle: TextStyle(
                fontSize: 36,
                color: AppColors.text,
                fontWeight: FontWeight.w400,
              ),
            ),
          ],
        ),
        Gaps.w8,
        Text(
          ':',
          style: TextStyle(
            fontSize: 36,
            color: AppColors.text,
            fontWeight: FontWeight.w400,
          ),
        ),
        Gaps.w8,
        Stack(
          alignment: Alignment.center,
          children: [
            Container(
              width: 100,
              height: 50,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(Sizes.p16),
                color: Colors.white,
              ),
            ),
            NumberPicker(
              value: _minute,
              haptics: true,
              zeroPad: true,
              infiniteLoop: true,
              minValue: 0,
              maxValue: 59,
              step: 15,
              onChanged: (value) => setState(() => _minute = value),
              textStyle: TextStyle(
                fontSize: 36,
                color: AppColors.text.withOpacity(0.2),
                fontWeight: FontWeight.w400,
              ),
              selectedTextStyle: TextStyle(
                fontSize: 36,
                color: AppColors.text,
                fontWeight: FontWeight.w400,
              ),
            ),
          ],
        ),
      ],
    );
  }
}

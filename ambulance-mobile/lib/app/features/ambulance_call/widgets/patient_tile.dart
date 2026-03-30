import 'package:flutter/material.dart';

import '../../../theme/theme.dart';

class PatientTile extends StatelessWidget {
  final String? fio;
  final String? passport;
  final String? age;
  final Function()? onTap;

  const PatientTile({
    super.key,
    this.fio = '',
    this.passport = '',
    this.age = '',
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 5, horizontal: 15),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(5),
          border: Border.all(width: 2, color: AppColors.scaffold),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Flexible(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  RichText(
                    text: TextSpan(
                      style: DefaultTextStyle.of(context).style,
                      children: [
                        const TextSpan(text: 'ФИО: '),
                        TextSpan(text: fio),
                      ],
                    ),
                  ),
                  RichText(
                    text: TextSpan(
                      style: DefaultTextStyle.of(context).style,
                      children: [
                        const TextSpan(text: 'Возраст: '),
                        TextSpan(text: age),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            if (onTap != null)
              Flexible(
                flex: 0,
                child: Container(
                  width: 50,
                  height: 50,
                  margin: const EdgeInsets.only(left: 10),
                  decoration: BoxDecoration(
                    color: AppColors.primary,
                    borderRadius: BorderRadius.circular(100),
                  ),
                  child: const Icon(Icons.edit, color: Colors.white),
                ),
              ),
          ],
        ),
      ),
    );
  }
}

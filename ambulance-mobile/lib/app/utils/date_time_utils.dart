import 'package:intl/intl.dart';
import 'package:mask_text_input_formatter/mask_text_input_formatter.dart';

final dateOnlyFormat = DateFormat('dd.MM.yyyy');
final timeOnlyFormat = DateFormat('HH:mm');
final dayMonthAndDayOfWeekFormat = DateFormat('d MMMM, EEEE', 'ru');
const dateOnlyMask = '##.##.####';
final dateOnlyMaskFormatter = MaskTextInputFormatter(
  mask: dateOnlyMask,
  filter: {"#": RegExp(r'[0-9]')},
  type: MaskAutoCompletionType.lazy,
);
const timeOnlyMask = '##:##';
final timeOnlyMaskFormatter = MaskTextInputFormatter(
  mask: timeOnlyMask,
  filter: {"#": RegExp(r'[0-9]')},
  type: MaskAutoCompletionType.lazy,
);

extension DateTimeExt on DateTime {
  DateTime roundUpToNextQuarterHour() {
    int minutesToAdd = 15 - (minute % 15);
    if (minute % 15 == 0) {
      minutesToAdd = 15;
    }
    return add(Duration(minutes: minutesToAdd));
  }
}

DateTime ageToDateOfBirth({String? age, int fallbackAge = 18}) {
  int parseAge(String? age) {
    if (age == null) return -1;
    return int.tryParse(age) ?? -1;
  }

  int sanitizeAge(int age, {required int fallbackValue}) {
    return age > 0 ? age : fallbackValue;
  }

  final now = DateTime.now();
  final currentYear = now.year;
  final ageNum = sanitizeAge(
    parseAge(age),
    fallbackValue: fallbackAge,
  );

  int year;
  if (ageNum >= 1900 && ageNum <= currentYear) {
    year = ageNum;
  } else {
    year = currentYear - ageNum;
  }
  return DateTime(year, now.month, now.day);
}

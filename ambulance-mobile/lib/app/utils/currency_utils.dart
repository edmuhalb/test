import 'package:intl/intl.dart';

final rubleFormat = NumberFormat.currency(
  locale: 'ru_RU',
  symbol: '₽',
  decimalDigits: 0,
);

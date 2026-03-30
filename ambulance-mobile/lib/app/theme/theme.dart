library;

import 'package:flutter/material.dart';

part 'sizes.dart';
part 'gaps.dart';
part 'app_phrases.dart';
part 'app_images.dart';
part 'app_colors.dart';

const themeAnimationDuration = Duration(milliseconds: 350);

final theme = ThemeData(
  // fontFamily: 'NotoSans',
  primaryColor: AppColors.primary,
  canvasColor: AppColors.primary,
  scaffoldBackgroundColor: AppColors.scaffold,
  appBarTheme: const AppBarTheme(
    color: AppColors.primary,
    foregroundColor: AppColors.card,
    titleTextStyle: TextStyle(
      color: AppColors.appBarTitle,
      fontWeight: FontWeight.w500,
      fontSize: 18.0,
      height: 18.0 / 18.0,
    ),
  ),
  bottomAppBarTheme: BottomAppBarTheme(
    elevation: Sizes.p8,
    padding: const EdgeInsets.symmetric(
      vertical: Sizes.p8,
      horizontal: Sizes.p16,
    ),
    color: AppColors.card,
    shadowColor: AppColors.cardShadow,
  ),
  dividerColor: AppColors.focusedButton,
  cardColor: AppColors.card,
  cardTheme: CardTheme(
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(Sizes.p16),
    ),
  ),
  textTheme: TextTheme(
    bodySmall: TextStyle(
      color: AppColors.bodySmall,
      fontSize: 10.0,
    ),
    bodyMedium: bodyMediumStyle,
    bodyLarge: TextStyle(
      fontWeight: FontWeight.w500,
      fontSize: 18,
      height: 18.0 / 18.0,
    ),
    titleLarge: TextStyle(
      color: AppColors.titleLarge,
      fontWeight: FontWeight.w400,
      fontSize: 24,
    ),
    headlineSmall: TextStyle(
      color: AppColors.headlineSmall,
      fontSize: 14,
    ),
    headlineMedium: TextStyle(
      color: AppColors.headlineMedium,
      fontSize: 16,
      fontWeight: FontWeight.w500,
      height: 16.0 / 16.0,
    ),
    headlineLarge: TextStyle(
      color: AppColors.headlineLarge,
      fontSize: 18.0,
      fontWeight: FontWeight.w500,
      height: 18.0 / 18.0,
    ),
    labelSmall: TextStyle(
      color: AppColors.labelSmall,
      fontWeight: FontWeight.w400,
      fontSize: 10,
    ),
    labelMedium: TextStyle(
      color: AppColors.labelMedium,
      fontSize: 14.0,
      height: 16.0 / 14.0,
    ),
    labelLarge: TextStyle(
      color: AppColors.labelLarge,
      fontSize: 18,
      height: 18.0 / 18.0,
    ),
  ),
  textSelectionTheme: const TextSelectionThemeData(
    cursorColor: AppColors.primary,
  ),
  inputDecorationTheme: InputDecorationTheme(
    filled: false,
    isDense: true,
    contentPadding: EdgeInsets.only(bottom: 4, top: 0),
    enabledBorder: UnderlineInputBorder(
      borderSide: BorderSide(color: AppColors.textFieldEnabledBorder),
    ),
    focusedBorder: UnderlineInputBorder(
      borderSide: BorderSide(
        color: AppColors.textFieldFocusedBorder,
      ),
    ),
    errorBorder: UnderlineInputBorder(
      borderSide: BorderSide(
        color: AppColors.textFieldErrorBorder,
      ),
    ),
    focusedErrorBorder: UnderlineInputBorder(
      borderSide: BorderSide(
        color: AppColors.textFieldFocusedErrorBorder,
      ),
    ),
    labelStyle: const TextStyle(
      fontWeight: FontWeight.w400,
      fontSize: 14.0,
      color: AppColors.labelSmall,
    ),
    floatingLabelBehavior: FloatingLabelBehavior.always,
    floatingLabelStyle: const TextStyle(
      fontWeight: FontWeight.w400,
      fontSize: 13,
      height: 1,
      color: AppColors.labelSmall,
    ),
    prefixStyle: TextStyle(
      fontSize: 14.0,
      height: 16.0 / 14.0,
      color: AppColors.text,
    ),
    hintStyle: TextStyle(
      color: AppColors.textFieldHint,
      fontSize: 14.0,
      height: 16.0 / 14.0,
      fontWeight: FontWeight.w400,
    ),
    errorStyle: TextStyle(
      color: AppColors.alert,
      fontSize: 10,
      fontWeight: FontWeight.normal,
      height: 16.0 / 10.0,
    ),
  ),
  switchTheme: SwitchThemeData(
    trackColor: WidgetStateProperty.resolveWith((states) =>
        states.contains(WidgetState.selected)
            ? AppColors.primary
            : AppColors.title),
    trackOutlineWidth: WidgetStatePropertyAll(0),
    thumbColor: WidgetStatePropertyAll(AppColors.primaryButtonForeground),
  ),
  elevatedButtonTheme: ElevatedButtonThemeData(
    style: elevatedButtonStyle,
  ),
  textButtonTheme: const TextButtonThemeData(
    style: ButtonStyle(
      foregroundColor: WidgetStatePropertyAll(AppColors.text),
      textStyle: WidgetStatePropertyAll(
        TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.w400,
          height: 16.0 / 14.0,
        ),
      ),
    ),
  ),
  progressIndicatorTheme: ProgressIndicatorThemeData(
    color: AppColors.primary,
  ),
  bottomSheetTheme: BottomSheetThemeData(
    showDragHandle: true,
    backgroundColor: AppColors.scaffoldBody,
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.only(
        topLeft: Radius.circular(Sizes.p16),
        topRight: Radius.circular(Sizes.p16),
      ),
    ),
    dragHandleSize: Size(60, 6),
    dragHandleColor: AppColors.dragHandleColor,
  ),
  drawerTheme: DrawerThemeData(
    backgroundColor: AppColors.scaffoldBody,
    width: 269,
  ),
  listTileTheme: ListTileThemeData(
    titleTextStyle: bodyMediumStyle,
    minVerticalPadding: Sizes.p12,
    horizontalTitleGap: Sizes.p12,
  ),
  datePickerTheme: DatePickerThemeData(
    todayBorder: BorderSide.none,
    todayForegroundColor: WidgetStateProperty.resolveWith((states) =>
        states.contains(WidgetState.selected)
            ? AppColors.card
            : AppColors.primary),
    todayBackgroundColor: WidgetStateProperty.resolveWith((states) =>
        states.contains(WidgetState.selected) ? AppColors.primary : null),
    dayBackgroundColor: WidgetStateProperty.resolveWith((states) =>
        states.contains(WidgetState.selected) ? AppColors.primary : null),
    dayShape: WidgetStatePropertyAll(CircleBorder(side: BorderSide.none)),
  ),
);

final bodyMediumStyle = TextStyle(
  fontSize: 14.0,
  height: 16.0 / 14.0,
  color: AppColors.text,
);

final elevatedButtonStyle = ButtonStyle(
  elevation: WidgetStatePropertyAll(0.0),
  shape: WidgetStateProperty.all<RoundedRectangleBorder>(
    RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(Sizes.p16),
    ),
  ),
  backgroundColor: const WidgetStatePropertyAll(
    AppColors.buttonBackground,
  ),
  foregroundColor: const WidgetStatePropertyAll(
    AppColors.buttonForeground,
  ),
  textStyle: const WidgetStatePropertyAll(
    TextStyle(
      fontSize: 16,
      fontWeight: FontWeight.w400,
      height: 16.0 / 16.0,
    ),
  ),
);

final primaryButtonStyle = elevatedButtonStyle.copyWith(
  foregroundColor: const WidgetStatePropertyAll(
    AppColors.primaryButtonForeground,
  ),
  backgroundColor: const WidgetStatePropertyAll(
    AppColors.primaryButtonBackground,
  ),
);

final primaryMediumButtonStyle = primaryButtonStyle.copyWith(
  textStyle: WidgetStateProperty.all(bodyMediumStyle),
);

final secondaryButtonStyle = elevatedButtonStyle.copyWith(
  foregroundColor: const WidgetStatePropertyAll(
    AppColors.secondaryButtonForeground,
  ),
  backgroundColor: const WidgetStatePropertyAll(
    AppColors.secondaryButtonBackground,
  ),
);

final secondaryMediumButtonStyle = secondaryButtonStyle.copyWith(
  textStyle: WidgetStateProperty.all(bodyMediumStyle),
);
final smallTextButtonTheme = const TextButtonThemeData(
  style: ButtonStyle(
    foregroundColor: WidgetStatePropertyAll(AppColors.smallTextButton),
    textStyle: WidgetStatePropertyAll(
      TextStyle(
        fontSize: 10,
        fontWeight: FontWeight.w400,
        height: 16.0 / 10.0,
      ),
    ),
  ),
);

final signInFormInputDecorationTheme = InputDecorationTheme(
  filled: true,
  isDense: true,
  fillColor: AppColors.card,
  floatingLabelBehavior: FloatingLabelBehavior.always,
  contentPadding: const EdgeInsets.symmetric(
    vertical: 8,
    horizontal: 16,
  ),
  constraints: const BoxConstraints(minHeight: 32),
  enabledBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(12),
    borderSide: const BorderSide(
      color: AppColors.textFieldEnabledBorder,
      width: 1.0,
    ),
  ),
  focusedBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(12),
    borderSide: const BorderSide(
      color: AppColors.textFieldFocusedBorder,
      width: 1.0,
    ),
  ),
  errorBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(12),
    borderSide: const BorderSide(
      color: AppColors.textFieldErrorBorder,
      width: 1.0,
    ),
  ),
  focusedErrorBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(12),
    borderSide: const BorderSide(
      color: AppColors.textFieldFocusedErrorBorder,
      width: 1.0,
    ),
  ),
  labelStyle: const TextStyle(
    fontWeight: FontWeight.normal,
  ),
  prefixStyle: const TextStyle(
    fontSize: 14.0,
    height: 16.0 / 14.0,
    color: AppColors.text,
  ),
  hintStyle: const TextStyle(
    color: AppColors.textFieldHint,
    fontSize: 14.0,
    height: 16.0 / 14.0,
    fontWeight: FontWeight.w400,
  ),
  errorStyle: const TextStyle(
    color: AppColors.alert,
    fontSize: 10,
    fontWeight: FontWeight.normal,
    height: 16.0 / 10.0,
  ),
);

final appDrawerTitleTheme = theme.textTheme.bodyLarge!.copyWith(
  color: AppColors.appDrawerTitle,
  fontSize: Sizes.p16,
);

final dropdownInputDecoration = InputDecoration(
  filled: true,
  fillColor: AppColors.card,
  border: OutlineInputBorder(
    borderRadius: BorderRadius.circular(Sizes.p16),
    borderSide: BorderSide.none,
  ),
  enabledBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(Sizes.p16),
    borderSide: BorderSide.none,
  ),
  focusedBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(Sizes.p16),
    borderSide: BorderSide.none,
  ),
  contentPadding: EdgeInsets.symmetric(
    horizontal: 16,
    vertical: 12,
  ),
  errorBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(Sizes.p16),
    borderSide: BorderSide(color: AppColors.alert),
  ),
  focusedErrorBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(Sizes.p16),
    borderSide: BorderSide(color: AppColors.alert),
  ),
  disabledBorder: OutlineInputBorder(
    borderRadius: BorderRadius.circular(Sizes.p16),
    borderSide: BorderSide.none,
  ),
);

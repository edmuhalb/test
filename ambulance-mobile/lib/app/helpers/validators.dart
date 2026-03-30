import 'package:intl/intl.dart';
import 'package:mask_text_input_formatter/mask_text_input_formatter.dart';

import '../theme/theme.dart';
import '../widgets/images_picker/images_picker.dart';

typedef Validator = String? Function(String? value);

class Validators {
  static String? valueSelected<T>(T? inputValue) {
    return inputValue == null ? 'Заполните поле' : null;
  }

  static String? string(String? inputValue) {
    if (inputValue == null || inputValue.trim().isEmpty) {
      return 'Заполните поле';
    }
    return null;
  }

  static String? number(String? inputValue) {
    if (inputValue == null || inputValue.isEmpty) {
      return 'Заполните поле';
    }

    if (int.tryParse(inputValue) == null) {
      return "Введите сумму числом";
    }
    return null;
  }

  static String? notEmptyAndEveryUploaded(List<PickedImage>? images) {
    if (images == null || images.isEmpty) {
      return 'Заполните поле';
    }

    if (images.where((x) => !x.uploaded).isNotEmpty) {
      return 'Необходимо дождаться загрузки всех изображений';
    }

    return null;
  }

  static String? everyUploaded(List<PickedImage>? images) {
    if (images?.where((x) => !x.uploaded).isNotEmpty ?? false) {
      return 'Необходимо дождаться загрузки всех изображений';
    }

    return null;
  }

  static String? password(String? inputValue) {
    return inputValue == null || inputValue.isEmpty
        ? AppPhrases.enterPassword
        : null;
  }

  static Validator createPhoneNumberValidator(
    MaskTextInputFormatter formatter,
    String phoneNumberRegExp,
  ) {
    String? phoneNumberValidator(String? inputValue) {
      final unmaskedValue = formatter.getUnmaskedText();

      if (!RegExp(phoneNumberRegExp).hasMatch(unmaskedValue)) {
        return AppPhrases.enterMobilePhoneNumber;
      }

      if (inputValue == null || inputValue.isEmpty) {
        return AppPhrases.enterPhoneNumber;
      }

      return null;
    }

    return phoneNumberValidator;
  }

  static Validator createDateValidator(
    DateFormat dateFormat,
  ) {
    String? dateValidator(String? inputValue) {
      if (inputValue == null || inputValue.isEmpty) {
        return AppPhrases.enterDate;
      }

      try {
        final parsedDate = dateFormat.parse(inputValue);
        final reformattedDate = dateFormat.format(parsedDate);

        if (reformattedDate != inputValue) {
          return AppPhrases.invalidDateFormat;
        }
      } catch (e) {
        return AppPhrases.invalidDateFormat;
      }

      return null;
    }

    return dateValidator;
  }

  static Validator createTimeValidator(
    DateFormat timeFormat,
  ) {
    String? timeValidator(String? inputValue) {
      if (inputValue == null || inputValue.isEmpty) {
        return AppPhrases.enterTime;
      }

      try {
        final parsedDate = timeFormat.parse(inputValue);
        final reformattedDate = timeFormat.format(parsedDate);

        if (reformattedDate != inputValue) {
          return AppPhrases.invalidTimeFormat;
        }
      } catch (e) {
        return AppPhrases.invalidTimeFormat;
      }

      return null;
    }

    return timeValidator;
  }


  Validators._();
}

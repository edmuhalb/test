String formatPrice(int? value, {String? postfix = "руб."}) =>
    value == null ? '---' : '$value $postfix';

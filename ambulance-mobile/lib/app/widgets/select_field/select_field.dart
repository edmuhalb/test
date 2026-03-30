import 'package:flutter/material.dart';
import 'package:multi_dropdown/multiselect_dropdown.dart';

import '../../theme/theme.dart';

class SelectField extends StatelessWidget {
  final String hint;
  final List<ValueItem> options;
  final SelectionType selectionType;
  final Function(List<ValueItem<dynamic>>)? onOptionSelected;
  final MultiSelectController? controller;
  final bool searchEnabled;
  final bool showClearIcon;
  final List<ValueItem<dynamic>> selectedOptions;
  final double dropdownHeight;

  const SelectField({
    super.key,
    required this.hint,
    required this.options,
    this.onOptionSelected,
    this.selectionType = SelectionType.single,
    this.controller,
    this.searchEnabled = false,
    this.showClearIcon = true,
    this.selectedOptions = const [],
    this.dropdownHeight = 200,
  });

  @override
  Widget build(BuildContext context) {
    return MultiSelectDropDown(
      //showClearIcon: showClearIcon,
      selectedOptions: selectedOptions,
      onOptionSelected: onOptionSelected,
      hint: hint,
      hintColor: Colors.black,
      alwaysShowOptionIcon: false,
      // padding: const EdgeInsets.all(0),
     // backgroundColor: Colors.transparent,
      borderColor: AppColors.scaffold,
      borderWidth: 1,
      borderRadius: 5,
      maxItems: 4,
      selectionType: selectionType,
      chipConfig:
          const ChipConfig(wrapType: WrapType.wrap, backgroundColor: AppColors.primary),
      dropdownHeight: dropdownHeight,
      searchEnabled: searchEnabled,
      dropdownMargin: 2,
      optionSeparator:
          const SizedBox(height: 1, child: Divider(color: Colors.black)),
      options: options,
      controller: controller,
      dropdownBorderRadius: 5,
    );
  }
}

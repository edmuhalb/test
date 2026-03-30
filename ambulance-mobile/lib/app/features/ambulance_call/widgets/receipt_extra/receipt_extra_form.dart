import 'package:flutter/material.dart';

import '../../../../theme/theme.dart';

class ReceiptExtraForm extends StatefulWidget {
  final String? initialComment;
  final bool? initialAdvertesed;
  final bool readOnly;

  const ReceiptExtraForm({
    super.key,
    this.initialComment,
    this.initialAdvertesed,
    this.readOnly = false,
  });

  @override
  State<ReceiptExtraForm> createState() => ReceiptExtraFormState();
}

class ReceiptExtraFormState extends State<ReceiptExtraForm> {
  final _formKey = GlobalKey<FormState>();

  String? _comment;
  bool _advertised = false;

  String? get comment => _comment;

  bool get advertised => _advertised;

  void save() => _formKey.currentState?.save();

  bool validate() => _formKey.currentState?.validate() ?? false;

  @override
  void initState() {
    super.initState();
    _comment = widget.initialComment;
    _advertised = widget.initialAdvertesed ?? false;
  }

  @override
  Widget build(BuildContext context) => Form(
        key: _formKey,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextFormField(
              readOnly: widget.readOnly,
              initialValue: _comment,
              maxLines: null,
              style: Theme.of(context).textTheme.bodyMedium,
              decoration: InputDecoration(
                labelText: '${AppPhrases.comment}:',
              ),
              onSaved: (newValue) => _comment = newValue,
            ),
            Gaps.h24,
            FormField<bool>(
              initialValue: _advertised,
              builder: (FormFieldState<bool> state) => Container(
                padding: EdgeInsets.symmetric(
                  vertical: Sizes.p12,
                ),
                height: Sizes.p40,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(AppPhrases.leftPromoMaterials),
                    Switch(
                      value: state.value ?? false,
                      onChanged: !widget.readOnly
                          ? (value) {
                              _advertised = value;
                              state.didChange(value);
                            }
                          : null,
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      );
}

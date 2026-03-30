import 'package:get_it/get_it.dart';
import 'package:flutter/material.dart';

import '../../../../api/rest_client.dart';
import '../../../../helpers/validators.dart';
import '../../../../theme/theme.dart';

class HospitalizationServiceForm extends StatefulWidget {
  final int? initialCost;
  final String? initialNote;

  const HospitalizationServiceForm({
    super.key,
    this.initialCost,
    this.initialNote,
  });

  @override
  State<HospitalizationServiceForm> createState() =>
      HospitalizationServiceFormState();
}

class HospitalizationServiceFormState
    extends State<HospitalizationServiceForm> {
  final api = GetIt.I<RestClientV1>();
  final _formKey = GlobalKey<FormState>();

  int? _cost;
  String? _note;

  int? get cost => _cost;

  String? get note => _note;

  @override
  void initState() {
    super.initState();
    _cost = widget.initialCost;
    _note = widget.initialNote;
  }

  void save() => _formKey.currentState?.save();

  bool validate() => _formKey.currentState?.validate() ?? false;

  @override
  Widget build(BuildContext context) {
    return Form(
      key: _formKey,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: _cost?.toString(),
            validator: Validators.number,
            keyboardType: TextInputType.number,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.serviceCost}:',
            ),
            onSaved: (newValue) {
              _cost = newValue != null ? int.parse(newValue) : null;
            },
          ),
          Gaps.h16,
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: _note,
            maxLines: null,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.note}:',
            ),
            onSaved: (newValue) => _note = newValue,
          ),
        ],
      ),
    );
  }
}

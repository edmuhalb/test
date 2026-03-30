import 'package:flutter/material.dart';
import 'package:ambulance/app/helpers/helpers.dart';

import '../../../../theme/theme.dart';

class OrdinaryServiceForm extends StatefulWidget {
  final int? initialCost;

  const OrdinaryServiceForm({
    super.key,
    this.initialCost,
  });

  @override
  State<OrdinaryServiceForm> createState() => OrdinaryServiceFormState();
}

class OrdinaryServiceFormState extends State<OrdinaryServiceForm> {
  final _formKey = GlobalKey<FormState>();

  int? _cost;

  int? get cost => _cost;

  void save() => _formKey.currentState?.save();

  bool validate() => _formKey.currentState?.validate() ?? false;

  @override
  void initState() {
    super.initState();
    _cost = widget.initialCost;
  }

  @override
  Widget build(BuildContext context) => Form(
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
          ],
        ),
      );
}

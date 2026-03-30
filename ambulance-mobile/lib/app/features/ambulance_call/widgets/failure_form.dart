import 'package:ambulance/app/helpers/helpers.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:flutter/material.dart';
import 'package:ambulance/app/widgets/widgets.dart';

class FailureForm extends StatefulWidget {
  const FailureForm({
    super.key,
    required this.onSubmit,
  });

  final Function(Map<String, dynamic> fields) onSubmit;

  @override
  State<FailureForm> createState() => _FailureFormFormState();
}

class _FailureFormFormState extends State<FailureForm> {
  final _formKey = GlobalKey<FormState>();
  final rejectedComment = TextEditingController();

  _handleSubmit() {
    if (_formKey.currentState!.validate()) {
      widget.onSubmit({
        'rejectedComment': rejectedComment.value.text,
      });
    }
  }

  @override
  void dispose() {
    super.dispose();
    rejectedComment.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Form(
      key: _formKey,
      child: Padding(
        padding: const EdgeInsets.all(25),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: InputField(
                keyboardType: TextInputType.multiline,
                minLines: 3,
                maxLines: 100,
                hintText: 'Причина отказа',
                validator: Validators.string,
                controller: rejectedComment,
              ),
            ),
            Button(
              label: Text('Отменить вызов'),
              style: secondaryButtonStyle,
              onPressed: _handleSubmit,
            ),
            SizedBox(height: MediaQuery.of(context).viewInsets.bottom),
          ],
        ),
      ),
    );
  }
}

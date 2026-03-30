import 'package:flutter/material.dart';

import '../../../helpers/validators.dart';
import '../../../theme/theme.dart';
import '../../../utils/date_time_utils.dart';

const averagePatientAge = 18;

class PatientForm extends StatefulWidget {
  final String? initialFullName;
  final DateTime? initialDateOfBirth;
  final String? initialNote;
  final String initialAddress;
  final String? initialAddressInfo;
  final String? initialDescription;

  const PatientForm({
    super.key,
    this.initialFullName,
    this.initialDateOfBirth,
    this.initialNote,
    required this.initialAddress,
    this.initialAddressInfo,
    this.initialDescription,
  });

  @override
  State<PatientForm> createState() => PatientFormState();
}

class PatientFormState extends State<PatientForm> {
  final _formKey = GlobalKey<FormState>();

  final _dateOfBirthController = TextEditingController();

  String? _fullName;
  String? _note;
  late String _address;
  String? _addressInfo;
  String? _description;

  String? get fullName => _fullName;

  DateTime? get dateOfBirth => dateOnlyFormat.tryParse(
        _dateOfBirthController.text,
      );

  String? get note => _note;

  String get address => _address;

  String? get addressInfo => _addressInfo;

  String? get description => _description;

  void save() => _formKey.currentState?.save();

  bool validate() => _formKey.currentState?.validate() ?? false;

  @override
  void initState() {
    super.initState();    
    _fullName = widget.initialFullName;

    if (widget.initialDateOfBirth != null) {
      _dateOfBirthController.text =
          dateOnlyFormat.format(widget.initialDateOfBirth!);
    }

    _note = widget.initialNote;
    _address = widget.initialAddress;
    _addressInfo = widget.initialAddressInfo;
    _description = widget.initialDescription;
  }

  @override
  Widget build(BuildContext context) {
    return Form(
      key: _formKey,
      autovalidateMode: AutovalidateMode.onUnfocus,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: _fullName,
            validator: Validators.string,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.patientFullName}:',
            ),
            onSaved: (newValue) => _fullName = newValue,
          ),
          Gaps.h16,
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            // TODO: date range validator
            controller: _dateOfBirthController,
            inputFormatters: [dateOnlyMaskFormatter],
            validator: Validators.createDateValidator(dateOnlyFormat),
            style: Theme.of(context).textTheme.bodyMedium,
            keyboardType: TextInputType.datetime,
            decoration: InputDecoration(
              labelText: '${AppPhrases.dateOfBirth}:',
              suffixIconConstraints: BoxConstraints(
                maxHeight: Sizes.p32,
              ),
              suffixIcon: IconButton(
                visualDensity: VisualDensity.comfortable,
                padding: EdgeInsets.zero,
                icon: Icon(Icons.calendar_today),
                onPressed: () => _selectDate(context),
              ),
            ),
          ),
          Gaps.h16,
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: _note,
            maxLines: null,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.comment}:',
            ),
            onSaved: (newValue) => _note = newValue,
          ),
          Gaps.h16,
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: _address,
            validator: Validators.string,
            maxLines: null,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.address}:',
            ),
            onSaved: (newValue) => _address = newValue!,
          ),
          Gaps.h16,
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: _addressInfo,
            maxLines: null,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.addressInfo}:',
            ),
            onSaved: (newValue) => _addressInfo = newValue,
          ),
          Gaps.h16,
          TextFormField(
            initialValue: _description,
            maxLines: null,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.comment}:',
            ),
            onSaved: (newValue) => _description = newValue,
          ),
        ],
      ),
    );
  }

  Future<void> _selectDate(BuildContext context) async {
    final firstPickerDate = DateTime(1900);
    final lastPickerDate = DateTime.now();
    final dateOfBirthText = _dateOfBirthController.text;
    final dateOfBirth = _sanitizeDateOfBirth(
      dateOnlyFormat.tryParse(dateOfBirthText),
      minDate: firstPickerDate,
      maxDate: lastPickerDate,
    );

    final pickedDate = await showDatePicker(
      context: context,
      initialDate: dateOfBirth,
      firstDate: firstPickerDate,
      lastDate: lastPickerDate,
      locale: const Locale('ru', 'RU'),
    );

    if (pickedDate != null) {
      final day = pickedDate.day.toString().padLeft(2, '0');
      final month = pickedDate.month.toString().padLeft(2, '0');
      final year = pickedDate.year;
      _dateOfBirthController.text = '$day.$month.$year';
    }
  }

  DateTime _sanitizeDateOfBirth(
    DateTime? value, {
    required DateTime minDate,
    required DateTime maxDate,
  }) =>
      (value == null || value.isBefore(minDate) || value.isAfter(maxDate))
          ? ageToDateOfBirth(fallbackAge: averagePatientAge)
          : value;
}

import 'package:ambulance/app/utils/date_time_utils.dart';
import 'package:get_it/get_it.dart';
import 'package:flutter/material.dart';

import '../../../../api/rest_client.dart';
import '../../../../helpers/validators.dart';
import '../../../../theme/theme.dart';
import '../../../../widgets/bottom_sheet/custom_bottom_sheet.dart';
import 'repeat_service_time_bottom_sheet.dart';

class RepeatServiceForm extends StatefulWidget {
  final int? initialPrepayment;
  final DateTime? initialDateTime;
  final int? initialApproximateCost;
  final String? initialNote;

  const RepeatServiceForm({
    super.key,
    this.initialPrepayment,
    this.initialDateTime,
    this.initialApproximateCost,
    this.initialNote,
  });

  @override
  State<RepeatServiceForm> createState() => RepeatServiceFormState();
}

class RepeatServiceFormState extends State<RepeatServiceForm> {
  final api = GetIt.I<RestClientV1>();
  final _formKey = GlobalKey<FormState>();
  late DateTime _initialDateTime;
  final _dateController = TextEditingController();
  final _timeController = TextEditingController();
  int? _approximateCost;
  int? _prepayment;
  String? _note;

  DateTime? get dateTime {
    final time = timeOnlyFormat.tryParse(_timeController.text);

    if (time == null) return null;

    return dateOnlyFormat.tryParse(_dateController.text)?.copyWith(
          hour: time.hour,
          minute: time.minute,
        );
  }

  int? get approximateCost => _approximateCost;

  int? get prepayment => _prepayment;

  String? get note => _note;

  @override
  void initState() {
    super.initState();
    _initialDateTime = widget.initialDateTime ??
        DateTime.now().add(Duration(hours: 3)).roundUpToNextQuarterHour();
    _dateController.text = dateOnlyFormat.format(_initialDateTime);
    _timeController.text = timeOnlyFormat.format(_initialDateTime);
    _prepayment = widget.initialPrepayment;
    _approximateCost = widget.initialApproximateCost;
    _note = widget.initialNote;
  }

  bool validate() => _formKey.currentState?.validate() ?? false;

  void save() => _formKey.currentState?.save();

  @override
  Widget build(BuildContext context) {
    return Form(
      key: _formKey,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // TODO: validate date + range
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            controller: _dateController,
            inputFormatters: [dateOnlyMaskFormatter],
            validator: Validators.createDateValidator(dateOnlyFormat),
            style: Theme.of(context).textTheme.bodyMedium,
            keyboardType: TextInputType.datetime,
            decoration: InputDecoration(
              labelText: '${AppPhrases.dateOfRepeatService}:',
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
          // TODO: validate time + range
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            controller: _timeController,
            inputFormatters: [timeOnlyMaskFormatter],
            validator: Validators.createTimeValidator(timeOnlyFormat),
            style: Theme.of(context).textTheme.bodyMedium,
            keyboardType: TextInputType.datetime,
            decoration: InputDecoration(
              labelText: '${AppPhrases.timeOfRepeatService}:',
              suffixIconConstraints: BoxConstraints(
                maxHeight: Sizes.p32,
              ),
              suffixIcon: IconButton(
                visualDensity: VisualDensity.comfortable,
                padding: EdgeInsets.zero,
                icon: Icon(Icons.access_time_rounded),
                onPressed: () => _selectTime(context),
              ),
            ),
          ),
          Gaps.h16,
          TextFormField(
            initialValue: _approximateCost?.toString(),
            validator: Validators.number,
            autovalidateMode: AutovalidateMode.onUnfocus,
            keyboardType: TextInputType.number,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.approximateCost}:',
            ),
            onSaved: (newValue) {
              _approximateCost = newValue != null ? int.parse(newValue) : null;
            },
          ),
          Gaps.h16,
          TextFormField(
            initialValue: _prepayment?.toString(),
            validator: Validators.number,
            autovalidateMode: AutovalidateMode.onUnfocus,
            keyboardType: TextInputType.number,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.prepayment}:',
            ),
            onSaved: (newValue) {
              _prepayment = newValue != null ? int.parse(newValue) : null;
            },
          ),
          Gaps.h16,
          TextFormField(
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

  Future<void> _selectDate(BuildContext context) async {
    final firstPickerDate = DateTime.now();
    final lastPickerDate = DateTime.now().add(Duration(days: 31));
    final dateText = _dateController.text;
    final date = dateOnlyFormat.tryParse(dateText) ?? _initialDateTime;
    final initialDate = date.isBefore(firstPickerDate)
        ? firstPickerDate
        : date.isAfter(lastPickerDate)
            ? lastPickerDate
            : date;

    final pickedDate = await showDatePicker(
      context: context,
      initialDate: initialDate,
      firstDate: firstPickerDate,
      lastDate: lastPickerDate,
      locale: const Locale('ru', 'RU'),
    );

    if (pickedDate != null) {
      final day = pickedDate.day.toString().padLeft(2, '0');
      final month = pickedDate.month.toString().padLeft(2, '0');
      final year = pickedDate.year;
      _dateController.text = '$day.$month.$year';
    }
  }

  Future<void> _selectTime(BuildContext context) async {
    final time =
        timeOnlyFormat.tryParse(_timeController.text) ?? _initialDateTime;

    void submit(RepeatServiceTimeBottomSheetState sheet) {
      final form = sheet.form;
      final hour = form.hour.toString().padLeft(2, '0');
      final minute = form.minute.toString().padLeft(2, '0');
      _timeController.text = '$hour:$minute';
      Navigator.of(context).pop();
    }

    showCustomModalBottomSheet(
      context: context,
      showCloseButton: true,
      showDragHandle: true,
      isScrollControlled: true,
      builder: (context) => RepeatServiceTimeBottomSheet(
        initialHour: time.hour,
        initialMinute: time.minute,
        onSubmitPressed: submit,
      ),
    );
  }
}

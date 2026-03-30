import 'dart:collection';

import 'package:ambulance/app/repositories/clinics/clinics.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:get_it/get_it.dart';
import 'package:flutter/material.dart';
import 'package:talker_flutter/talker_flutter.dart';

import '../../../../api/dto/clinic.dart';
import '../../../../api/rest_client.dart';
import '../../../../helpers/validators.dart';
import '../../../../theme/theme.dart';
import '../../../../widgets/images_picker/images_picker.dart';
import '../../../../widgets/images_picker/pickable_images_form_field.dart';

class HospitalServiceForm extends StatefulWidget {
  final int? initialPrepayment;
  final Clinic? initialClinic;
  final int? initialDailyCost;
  final List<PickedImage>? initialFiles;
  final String? initialNote;

  const HospitalServiceForm({
    super.key,
    this.initialPrepayment,
    this.initialClinic,
    this.initialDailyCost,
    this.initialFiles,
    this.initialNote,
  });

  @override
  State<HospitalServiceForm> createState() => HospitalServiceFormState();
}

class HospitalServiceFormState extends State<HospitalServiceForm> {
  final api = GetIt.I<RestClientV1>();
  final _clinicRepository = GetIt.I<ClinicRepository>();
  final _formKey = GlobalKey<FormState>();
  bool _isFetchingClinics = true;
  final List<Clinic> _clinics = [];

  Clinic? _clinic;
  List<PickedImage>? _files;
  int? _dailyCost;
  int? _prepayment;
  String? _note;

  int? get dailyCost => _dailyCost;

  int? get prepayment => _prepayment;

  String? get note => _note;

  Clinic? get clinic => _clinic;

  List<PickedImage>? get files =>
      _files != null ? UnmodifiableListView(_files!) : null;

  @override
  void initState() {
    super.initState();
    _prepayment = widget.initialPrepayment;
    _clinic = widget.initialClinic;
    _dailyCost = widget.initialDailyCost;
    _files = widget.initialFiles?.toList();
    _note = widget.initialNote;
    _fetchClinics();
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
          DropdownButtonFormField<Clinic>(
            autovalidateMode: AutovalidateMode.onUserInteraction,
            value: _clinic,
            validator: Validators.valueSelected,
            dropdownColor: AppColors.card,
            decoration: dropdownInputDecoration,
            icon: _isFetchingClinics
                ? SizedBox(
                    width: Sizes.p16,
                    height: Sizes.p16,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                    ),
                  )
                : null,
            style: Theme.of(context).textTheme.bodyMedium,
            items: _clinics
                .map((clinic) => DropdownMenuItem<Clinic>(
                      value: clinic,
                      child: Text(clinic.name),
                    ))
                .toList(),
            onChanged: (newValue) {
              setState(() {
                _clinic = newValue!;
              });
            },
            hint: Text(AppPhrases.chooseHospital),
          ),
          Gaps.h16,
          ImagesPickerFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: _files,
            label: '${AppPhrases.attachPhotosOfPassport}:',
            fileUploader: api.uploadFile,
            fileRemover: (path) {
              // TODO: FIX ARCHITECTURE ERROR
              int? parseIdFromPath(String url) {
                final regex = RegExp(r'^/api/v1/files/(\d+)');
                final match = regex.firstMatch(url);
                if (match != null) {
                  return int.tryParse(match.group(1)!);
                }
                return null;
              }

              final fileId = parseIdFromPath(path)!;
              return api.deleteFile(fileId);
            },
            // validator: Validators.notEmptyAndEveryUploaded,
            onSaved: (newValue) => _files = newValue,
          ),
          Gaps.h16,
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: _dailyCost?.toString(),
            validator: Validators.number,
            keyboardType: TextInputType.number,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.dailyCost}:',
            ),
            onSaved: (newValue) {
              _dailyCost = newValue != null ? int.parse(newValue) : null;
            },
          ),
          Gaps.h16,
          TextFormField(
            autovalidateMode: AutovalidateMode.onUnfocus,
            initialValue: prepayment?.toString(),
            validator: Validators.number,
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

  Future<void> _fetchClinics() async {
    try {
      setState(() {
        _isFetchingClinics = true;
      });

      final clinics = await _clinicRepository.getClinicList();

      if (!mounted) return;

      setState(() {
        _clinics.clear();
        _clinics.addAll(clinics);
        _isFetchingClinics = false;
      });
    } catch (error, stack) {
      GetIt.I<Talker>().debug('Failed to fetch clinics', error, stack);
      FirebaseCrashlytics.instance.recordError(error, stack);
      await Future.delayed(Duration(seconds: 2));
      await _fetchClinics();
    }
  }
}

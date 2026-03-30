import 'package:ambulance/app/widgets/images_picker/pickable_images_form_field.dart';
import 'package:get_it/get_it.dart';
import 'package:flutter/material.dart';
import 'package:ambulance/app/helpers/helpers.dart';

import '../../../api/rest_client.dart';
import '../../../theme/theme.dart';

class ShiftClosureForm extends StatefulWidget {
  const ShiftClosureForm({super.key});

  @override
  State<ShiftClosureForm> createState() => ShiftClosureFormState();
}

class ShiftClosureFormState extends State<ShiftClosureForm> {
  final api = GetIt.I<RestClientV1>();
  final _formKey = GlobalKey<FormState>();
  int? _mileage;
  int? _tollRoad;
  int? _parkingFees;
  List<String>? files;

  int? get mileage => _mileage;
  int? get tollRoad => _tollRoad;
  int? get parkingFees => _parkingFees;

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
            validator: Validators.number,
            keyboardType: TextInputType.number,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.shiftMileage}:',
            ),
            onSaved: (newValue) {
              _mileage = newValue != null ? int.parse(newValue) : null;
            },
          ),
          Gaps.h16,
          Text(
            AppPhrases.testModeDataComparison,
            style: Theme.of(context).textTheme.bodySmall,
          ),
          Gaps.h16,
          TextFormField(
            validator: Validators.number,
            keyboardType: TextInputType.number,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.totalTollRoadsCost}:',
            ),
            onSaved: (newValue) {
              _tollRoad = newValue != null ? int.parse(newValue) : null;
            },
          ),
          Gaps.h16,
          TextFormField(
            validator: Validators.number,
            keyboardType: TextInputType.number,
            style: Theme.of(context).textTheme.bodyMedium,
            decoration: InputDecoration(
              labelText: '${AppPhrases.shiftParkingCost}:',
            ),
            onSaved: (newValue) {
              _parkingFees = newValue != null ? int.parse(newValue) : null;
            },
          ),
          Gaps.h16,
          ImagesPickerFormField(
            label: '${AppPhrases.attachReceipt}:',
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
            validator: Validators.everyUploaded,
            onSaved: (newValue) {
              files = newValue?.map((x) => x.remoteFileUrl!).toList();
            },
          )
        ],
      ),
    );
  }
}

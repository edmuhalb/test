import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:flutter/material.dart';
import 'package:ambulance/app/helpers/helpers.dart';
import 'package:get_it/get_it.dart';
import 'package:talker_flutter/talker_flutter.dart';

import '../../../../api/dto/entity.dart';
import '../../../../api/rest_client.dart';
import '../../../../theme/theme.dart';

class RejectAmbulanceCallForm extends StatefulWidget {
  const RejectAmbulanceCallForm({super.key});

  @override
  State<RejectAmbulanceCallForm> createState() =>
      RejectAmbulanceCallFormState();
}

class RejectAmbulanceCallFormState extends State<RejectAmbulanceCallForm> {
  final api = GetIt.I<RestClientV1>();
  final _formKey = GlobalKey<FormState>();
  bool _isFetchingReasons = true;
  final List<Entity> _rejectionReasons = [];

  Entity? _rejectionReason;

  Entity? get rejectionReason => _rejectionReason;

  @override
  void initState() {
    super.initState();
    _fetchRejectionReasons();
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
          DropdownButtonFormField<Entity>(
            autovalidateMode: AutovalidateMode.onUserInteraction,
            value: _rejectionReason,
            validator: Validators.valueSelected,
            dropdownColor: AppColors.card,
            decoration: dropdownInputDecoration,
            icon: _isFetchingReasons
                ? SizedBox(
                    width: Sizes.p16,
                    height: Sizes.p16,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                    ),
                  )
                : null,
            style: Theme.of(context).textTheme.bodyMedium,
            items: _rejectionReasons
                .map((clinic) => DropdownMenuItem<Entity>(
                      value: clinic,
                      child: Text(clinic.name),
                    ))
                .toList(),
            onChanged: (newValue) {
              setState(() {
                _rejectionReason = newValue!;
              });
            },
            hint: Text(AppPhrases.chooseRejectionReason),
          ),
        ],
      ),
    );
  }

  Future<void> _fetchRejectionReasons() async {
    try {
      setState(() {
        _isFetchingReasons = true;
      });

      final rejectionReasonListResponse = await api.getRejectionReasons();

      if (!mounted) return;

      setState(() {
        _rejectionReasons.clear();
        _rejectionReasons.addAll(rejectionReasonListResponse.items);
        _isFetchingReasons = false;
      });
    } catch (error, stack) {
      GetIt.I<Talker>()
          .debug('Failed to fetch rejection reasons', error, stack);
      FirebaseCrashlytics.instance.recordError(error, stack);
      await Future.delayed(Duration(seconds: 2));
      await _fetchRejectionReasons();
    }
  }
}

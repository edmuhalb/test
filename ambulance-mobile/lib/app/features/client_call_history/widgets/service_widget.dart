import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/utils/currency_utils.dart';
import 'package:flutter/material.dart';

import '../../../repositories/ambulance_call/models/models.dart';

class ServiceWidget extends StatelessWidget {
  final ProvidedService service;

  const ServiceWidget({
    super.key,
    required this.service,
  });

  String get formattedServicePrice => rubleFormat.format(service.price);

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 0.5,
      color: AppColors.card,
      child: Padding(
        padding: const EdgeInsets.all(Sizes.p16),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(service.name),
            Text(formattedServicePrice),
          ],
        ),
      ),
    );
  }
}

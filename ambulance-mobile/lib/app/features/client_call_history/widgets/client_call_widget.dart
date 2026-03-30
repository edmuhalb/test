import 'package:ambulance/app/features/client_call_history/widgets/service_widget.dart';
import 'package:ambulance/app/repositories/ambulance_call/models/models.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/utils/date_time_utils.dart';
import 'package:flutter/material.dart';

const nullDatePlaceholder = '-';

class ClientCallWidget extends StatelessWidget {
  final ClientCall clientCall;

  const ClientCallWidget({
    super.key,
    required this.clientCall,
  });

  String get dateHeaderText {
    if (clientCall.completedAt == null) return nullDatePlaceholder;

    final localDate = DateTime.parse(clientCall.completedAt!).toLocal();
    return dayMonthAndDayOfWeekFormat.format(localDate);
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text(
          dateHeaderText,
          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                fontSize: Sizes.p24,
              ),
        ),
        ...clientCall.services!.map(
          (json) => ServiceWidget(
            service: ProvidedService.fromJson(json),
          ),
        ),
      ],
    );
  }
}

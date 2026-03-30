import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:ambulance/app/utils/date_time_utils.dart';
import 'package:flutter/material.dart';

import '../../../theme/theme.dart';

class CallListTile extends StatelessWidget {
  final VoidCallback? onTap;

  const CallListTile({
    super.key,
    required this.call,
    this.onTap,
  });

  final AmbulanceCall call;

  @override
  Widget build(BuildContext context) {
    final localDate = DateTime.parse(call.dateTime!).toLocal();
    final time = timeOnlyFormat.format(localDate);

    return Card(
      elevation: 0,
      clipBehavior: Clip.hardEdge,
      margin: EdgeInsets.zero,
      color: AppColors.card,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(Sizes.p16),
      ),
      child: InkWell(
        onTap: onTap,
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(Sizes.p16),
            border: Border.all(
              color: _getBorderColor(call.status),
              width: _getBorderWidth(call.status),
            ),
          ),
          child: Padding(
            padding: EdgeInsets.all(Sizes.p16),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Flexible(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        call.address!,
                        style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                              fontWeight: FontWeight.w500,
                            ),
                      ),
                      Gaps.h4,
                      Text(
                        call.statusLabel!,
                        style: Theme.of(context).textTheme.bodySmall,
                      ),
                    ],
                  ),
                ),
                Gaps.w16,
                Text(
                  time,
                  style: Theme.of(context).textTheme.headlineLarge?.copyWith(
                        fontSize: Sizes.p16 + Sizes.p4,
                      ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Color _getBorderColor(String? callStatus) {
    switch (callStatus) {
      case 'assigned':
        return AppColors.primary;
      case 'accepted':
      case 'dispatched':
      case 'treating':
      case 'arrived':
        return AppColors.success;
      default:
        return AppColors.scaffoldBody;
    }
  }

  double _getBorderWidth(String? callStatus) {
    switch (callStatus) {
      case 'assigned':
        return 1;
      case 'accepted':
      case 'dispatched':
      case 'treating':
      case 'arrived':
        return 3;
      default:
        return 1;
    }
  }
}

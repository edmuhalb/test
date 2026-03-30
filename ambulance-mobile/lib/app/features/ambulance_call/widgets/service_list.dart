import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../../theme/theme.dart';

class ServiceList extends StatelessWidget {
  final List<dynamic> list;
  final Function(Map<String, dynamic>) onEditService;

  const ServiceList({
    super.key,
    required this.list,
    required this.onEditService,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(list.length, (index) {
        final item = list[index];
        final service = item["service"];
        final serviceType = item["service"]["type"];
        final plannedPrice = item["plannedPrice"];
        final plannedAt = item["plannedAt"];
        final description = item["description"];
        final price = item["price"] != null ? item["price"].toString() : "";
        final clinic = item["clinic"] != null ? item["clinic"].toString() : "";

        final serviceData = {
          'serviceId': index,
          'service': service,
          'price': price,
          'plannedPrice': plannedPrice,
          'plannedAt': plannedAt,
          'description': description,
          'clinic': clinic,
        };

        return GestureDetector(
          onTap: () => onEditService(serviceData),
          child: Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 5, horizontal: 15),
              margin: const EdgeInsets.only(bottom: 5),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(5),
                border: Border.all(width: 2, color: AppColors.scaffold),
              ),
              child: Flex(
                direction: Axis.horizontal,
                children: [
                  Flexible(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Flexible(
                              child: Text(
                                service["name"],
                                style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
                              ),
                            ),
                            Text('$price руб', textAlign: TextAlign.center),
                          ],
                        ),
                        if (plannedPrice != null)
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                serviceType == 'replay'
                                    ? 'Ориентировочная цена'
                                    : 'Стоимость за сутки:',
                                overflow: TextOverflow.ellipsis,
                                textAlign: TextAlign.left,
                                style: const TextStyle(color: Colors.grey),
                              ),
                              Text(
                                '${plannedPrice.toString()} руб',
                                overflow: TextOverflow.ellipsis,
                                textAlign: TextAlign.left,
                                style: const TextStyle(color: Colors.grey),
                              ),
                            ],
                          ),
                        if (plannedAt != null)
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'Дата:',
                                overflow: TextOverflow.ellipsis,
                                textAlign: TextAlign.left,
                                style: TextStyle(color: Colors.grey),
                              ),
                              Text(
                                DateFormat.yMMMMd('ru_RU')
                                    .format(DateTime.parse(plannedAt)),
                                overflow: TextOverflow.ellipsis,
                                textAlign: TextAlign.left,
                                style: const TextStyle(color: Colors.grey),
                              ),
                            ],
                          ),
                        if (description != null)
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Flexible(
                                child: Text(
                                  description,
                                  overflow: TextOverflow.ellipsis,
                                  textAlign: TextAlign.left,
                                  maxLines: 10,
                                  style: const TextStyle(color: Colors.grey),
                                ),
                              ),
                            ],
                          ),
                      ],
                    ),
                  ),
                ],
              )),
        );
      }),
    );
  }
}

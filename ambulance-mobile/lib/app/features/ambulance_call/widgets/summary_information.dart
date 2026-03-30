import 'package:ambulance/app/widgets/tile_header/tile_header.dart';
import 'package:ambulance/app/widgets/tile_section/tile_section.dart';
import 'package:flutter/material.dart';

class SummaryInformation extends StatelessWidget {
  final String currentOrderPrice;
  final String callingPaymentNextOrder;
  final String totalAmount;

  const SummaryInformation({
    super.key,
    required this.currentOrderPrice,
    required this.callingPaymentNextOrder,
    required this.totalAmount,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 10, right: 10, bottom: 5),
          child: TileSection(
            title: 'Текущий заказ:',
            content: currentOrderPrice,
            direction: "row",
          ),
        ),
        Padding(
          padding: const EdgeInsets.only(left: 10, right: 10, bottom: 5),
          child: TileSection(
            title: 'Предоплата:',
            content: callingPaymentNextOrder,
            direction: "row",
          ),
        ),
        Padding(
          padding: const EdgeInsets.only(left: 10, right: 10, bottom: 5),
          child: TileHeader(
            title: 'Итого:',
            content: Text(
              totalAmount,
              style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
            ),
            direction: "row",
          ),
        ),
      ],
    );
  }
}

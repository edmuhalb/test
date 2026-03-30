import 'package:ambulance/app/api/dto/medication_product.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/widgets/button/button.dart';
import 'package:flutter/material.dart';

class MedicationProductSetSelectionItem extends StatefulWidget {
  final MedicationProduct product;

  MedicationProductSetSelectionItem({
    super.key,
    required this.product,
  }) : assert(product.products != null && product.products!.isNotEmpty);

  @override
  State<MedicationProductSetSelectionItem> createState() =>
      _MedicationProductSetSelectionItemState();
}

class _MedicationProductSetSelectionItemState
    extends State<MedicationProductSetSelectionItem> {
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: Button(
            onPressed: () {},
            label: Row(
              children: [
                Flexible(
                  child: Text(
                    widget.product.name,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: Theme.of(context).textTheme.bodyMedium,
                  ),
                ),
              ],
            ),
            style: ElevatedButton.styleFrom(
              padding: EdgeInsets.zero,
            ),
          ),
        ),
        SizedBox(
          width: Sizes.p40 + Sizes.p4,
          height: Sizes.p24,
          child: Button.icon(
            onPressed: () {},
            label: Text('+'),
            style: ElevatedButton.styleFrom(
              padding: EdgeInsets.only(
                left: Sizes.p12,
                right: Sizes.p12,
              ),
              // TODO: move color to theme
              backgroundColor: Color(0xFFF5F4F2),
            ),
          ),
        ),
      ],
    );
  }
}

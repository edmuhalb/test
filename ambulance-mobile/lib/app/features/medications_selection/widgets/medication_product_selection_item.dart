import 'package:ambulance/app/api/dto/medication_product.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/widgets/button/button.dart';
import 'package:flutter/material.dart';

class MedicationProductSelectionItem extends StatefulWidget {
  final MedicationProduct product;

  const MedicationProductSelectionItem({
    super.key,
    required this.product,
  });

  @override
  State<MedicationProductSelectionItem> createState() =>
      _MedicationProductSelectionItemState();
}

class _MedicationProductSelectionItemState
    extends State<MedicationProductSelectionItem> {
  int _count = 0;

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Expanded(
          child: Button(
            onPressed: () {},
            label: Row(
              children: [
                Flexible(
                  child: Text(
                    widget.product.name,
                    maxLines: 2,
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
        if (widget.product.unitLabel != null)
          Flexible(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: Sizes.p8),
              child: Text(
                widget.product.unitLabel!,
                style: Theme.of(context).textTheme.bodySmall,
              ),
            ),
          ),
        AnimatedSwitcher(
          duration: kThemeAnimationDuration,
          child: _count == 0
              ? Container(
                  alignment: Alignment.centerRight,
                  width: Sizes.p96,
                  color: Colors.amber,
                  child: SizedBox(
                    width: Sizes.p40 + Sizes.p4,
                    height: Sizes.p24,
                    child: Button.icon(
                      onPressed: () {
                        setState(() {
                          _count += 1;
                        });
                      },
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
                )
              : Container(
                  alignment: Alignment.centerRight,
                  width: Sizes.p96,
                  color: Colors.amber,
                  child: SizedBox(
                    width: Sizes.p40 + Sizes.p4,
                    height: Sizes.p24,
                    child: Row(
                      children: [
                        Flexible(
                          child: Button.icon(
                            onPressed: () {
                              setState(() {
                                _count += 1;
                              });
                            },
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
                        Flexible(
                          child: Button.icon(
                            onPressed: () {
                              setState(() {
                                _count -= 1;
                              });
                            },
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
                    ),
                  ),
                ),
        ),
      ],
    );
  }
}

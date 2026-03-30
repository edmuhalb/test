import 'dart:collection';

import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/utils/currency_utils.dart';
import 'package:ambulance/app/widgets/button/button.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

import '../../../../api/dto/ambulance_call_service.dart';
import '../../../../api/dto/order_item.dart';
import '../../../../repositories/services/services.dart';
import '../../../../widgets/bottom_sheet/custom_bottom_sheet.dart';
import 'cart_item_bottom_sheet.dart';

class CartWidget extends StatefulWidget {
  final Map<AmbulanceCallService, OrderItem?> initialCart;
  final ValueChanged<OrderItem>? didPutCartItem;
  final ValueChanged<OrderItem>? didRemoveCartItem;
  final bool readOnly;

  const CartWidget({
    super.key,
    this.initialCart = const {},
    this.didPutCartItem,
    this.didRemoveCartItem,
    this.readOnly = false,
  });

  @override
  State<CartWidget> createState() => CartWidgetState();
}

class CartWidgetState extends State<CartWidget> {
  final _cart = <AmbulanceCallService, OrderItem?>{};

  @override
  void initState() {
    super.initState();
    _cart.addAll(widget.initialCart);
  }

  List<OrderItem> get items =>
      UnmodifiableListView(_cart.values.nonNulls);

  int get totalCost => _cart.values
      .toList()
      .fold(0, (sum, orderItem) => sum + (orderItem?.price ?? 0));

  bool isAdded(Service service) => _cart[service] != null;

  void put(OrderItem item, {bool silent = false}) {
    setState(() {
      _cart[item.service] = item;
    });

    if (!silent) {
      widget.didPutCartItem?.call(item);
    }
  }

  OrderItem? operator [](Service service) => _cart[service];

  bool contains(Service service) => _cart[service] != null;

  OrderItem? remove(AmbulanceCallService service,
      {bool silent = false}) {
    final removed = _cart[service];
    _cart[service] = null;

    if (removed != null) {
      setState(() {});

      if (!silent) {
        widget.didRemoveCartItem?.call(removed);
      }
    }

    return removed;
  }

  @override
  Widget build(BuildContext context) => Padding(
        padding: const EdgeInsets.all(Sizes.p16),
        child: Column(
          children: [
            for (final entry in _cart.keys.indexed) ...{
              if (_cart[entry.$2] != null) ...[
                _buildAddedCartEntryButton(
                  service: entry.$2,
                  cost: _cart[entry.$2]!.price!,
                  onPressed: () => _showCartItemBottomSheet(entry.$2),
                ),
              ] else ...[
                _buildCartEntryButton(
                  service: entry.$2,
                  onPressed: () => _showCartItemBottomSheet(entry.$2),
                ),
              ],
              if (entry.$1 < _cart.length - 1) Gaps.h8,
            },
            Gaps.h8,
            _buildTotalCost(
              context,
              totalCost: totalCost,
            ),
          ],
        ),
      );

  Widget _buildCartEntryButton({
    required AmbulanceCallService service,
    required VoidCallback? onPressed,
  }) {
    return Button.icon(
      onPressed: !widget.readOnly ? onPressed : null,
      icon: Icon(Icons.chevron_right),
      iconAlignment: IconAlignment.end,
      label: Row(
        children: [
          Flexible(
            child: Text(
              service.name,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
      style: ElevatedButton.styleFrom(
        padding: EdgeInsets.only(
          left: Sizes.p16,
          right: Sizes.p12,
        ),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(Sizes.p16),
          side: BorderSide(
            color: AppColors.textFieldEnabledBorder, // Border color
            width: 1, // Border width
          ),
        ),
      ),
    );
  }

  Widget _buildAddedCartEntryButton({
    required AmbulanceCallService service,
    required int cost,
    required VoidCallback? onPressed,
  }) {
    return Button.icon(
      onPressed: !widget.readOnly ? onPressed : null,
      icon: SvgPicture.asset(
        AppImages.check,
        colorFilter: ColorFilter.mode(
          AppColors.success,
          BlendMode.srcIn,
        ),
        width: Sizes.p16,
        height: Sizes.p16,
      ),
      iconAlignment: IconAlignment.end,
      label: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Flexible(
            child: Text(
              service.name,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
          Gaps.w8,
          Text(rubleFormat.format(cost)),
        ],
      ),
      style: ElevatedButton.styleFrom(
        padding: EdgeInsets.only(
          left: Sizes.p16,
          right: Sizes.p12,
        ),
        backgroundColor: AppColors.scaffoldBody,
        disabledBackgroundColor: AppColors.scaffoldBody,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(Sizes.p16),
        ),
      ),
    );
  }

  Widget _buildTotalCost(
    BuildContext context, {
    required int totalCost,
  }) {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: Sizes.p16,
        vertical: Sizes.p12,
      ),
      height: Sizes.p40,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            '${AppPhrases.total}:',
            style: Theme.of(context).textTheme.bodyLarge,
          ),
          Text(
            rubleFormat.format(totalCost),
            style: Theme.of(context).textTheme.bodyLarge,
          ),
        ],
      ),
    );
  }

  void _showCartItemBottomSheet(AmbulanceCallService service) =>
      showCustomModalBottomSheet(
        context: context,
        showCloseButton: true,
        showDragHandle: true,
        isScrollControlled: true,
        builder: (context) => CartItemBottomSheet(
          service: service,
          initialOrderItem: _cart[service],
          onPutIntoCart: (cartItem) {
            put(cartItem);
            Navigator.of(context).pop();
          },
          onRemoveFromCart: () {
            remove(service);
            Navigator.of(context).pop();
          },
        ),
      );
}

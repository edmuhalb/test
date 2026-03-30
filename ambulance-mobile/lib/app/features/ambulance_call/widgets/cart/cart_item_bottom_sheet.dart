import 'package:ambulance/app/features/ambulance_call/widgets/hospital_service/hospital_service_form.dart';
import 'package:ambulance/app/features/ambulance_call/widgets/hospitalization_service/hospitalizaton_service_form.dart';
import 'package:ambulance/app/features/ambulance_call/widgets/medical_exemption_service/medical_exemption_service_form.dart';
import 'package:ambulance/app/features/ambulance_call/widgets/ordinary_service/ordinary_service_form.dart';
import 'package:ambulance/app/features/ambulance_call/widgets/repeat_service/repeat_service_form.dart';
import 'package:ambulance/app/widgets/images_picker/images_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

import '../../../../api/dto/ambulance_call_service.dart';
import '../../../../api/dto/order_item.dart';
import '../../../../theme/theme.dart';
import '../../../../widgets/bottom_sheet/bottom_sheet_scaffold.dart';
import '../../../../widgets/button/button.dart';

class CartItemBottomSheet extends StatefulWidget {
  final AmbulanceCallService service;
  final OrderItem? initialOrderItem;
  final ValueChanged<OrderItem>? onPutIntoCart;
  final VoidCallback? onRemoveFromCart;

  const CartItemBottomSheet({
    super.key,
    required this.service,
    this.initialOrderItem,
    this.onPutIntoCart,
    this.onRemoveFromCart,
  });

  @override
  State<CartItemBottomSheet> createState() =>
      CartItemBottomSheetState();
}

class CartItemBottomSheetState
    extends State<CartItemBottomSheet> {
  final _formKey = GlobalKey();
  late WidgetBuilder _buildForm;
  late bool Function() _validateForm;
  late VoidCallback _saveForm;
  late OrderItem Function(bool inCash) _createCartItem;

  AmbulanceCallService get service => widget.service;

  @override
  void initState() {
    super.initState();
    switch (service.id) {
      case 9:
        RepeatServiceFormState getForm() =>
            _formKey.currentState! as RepeatServiceFormState;
        _buildForm = (context) {
          final orderItem = widget.initialOrderItem;
          return RepeatServiceForm(
            key: _formKey,
            initialPrepayment: orderItem?.price,
            initialDateTime: DateTime.tryParse(orderItem?.plannedAt ?? ''),
            initialApproximateCost: orderItem?.plannedPrice,
            initialNote: orderItem?.description,
          );
        };
        _validateForm = () => getForm().validate();
        _saveForm = () => getForm().save();
        _createCartItem = (inCash) => OrderItem(
              service: service,
              price: getForm().prepayment!,
              plannedAt: getForm().dateTime!.toIso8601String(),
              plannedPrice: getForm().approximateCost!,
              description: getForm().note,
              inCash: inCash,
            );
        break;
      case 10:
        HospitalServiceFormState getForm() =>
            _formKey.currentState! as HospitalServiceFormState;
        _buildForm = (context) {
          final initialOrderItem = widget.initialOrderItem;
          return HospitalServiceForm(
            key: _formKey,
            initialPrepayment: initialOrderItem?.price,
            initialClinic: initialOrderItem?.clinic,
            initialDailyCost: initialOrderItem?.plannedPrice,
            initialFiles: initialOrderItem?.files
                ?.map((x) => PickedImage(remoteFileUrl: x))
                .toList(),
            initialNote: initialOrderItem?.description,
          );
        };
        _validateForm = () => getForm().validate();
        _saveForm = () => getForm().save();
        _createCartItem = (inCash) => OrderItem(
              service: service,
              price: getForm().prepayment!,
              clinic: getForm().clinic!,
              plannedPrice: getForm().dailyCost!,
              files: getForm().files!.map((x) => x.remoteFileUrl!).toList(),
              description: getForm().note,
              inCash: inCash,
            );
        break;
      case 12:
        HospitalizationServiceFormState getForm() =>
            _formKey.currentState! as HospitalizationServiceFormState;
        _buildForm = (context) {
          final initialOrderItem = widget.initialOrderItem;
          return HospitalizationServiceForm(
            key: _formKey,
            initialCost: initialOrderItem?.price,
            initialNote: initialOrderItem?.description,
          );
        };
        _validateForm = () => getForm().validate();
        _saveForm = () => getForm().save();
        _createCartItem = (inCash) => OrderItem(
              service: service,
              price: getForm().cost!,
              description: getForm().note,
              inCash: inCash,
            );
        break;
      case 23:
        MedicalExemptionServiceFormState getForm() =>
            _formKey.currentState! as MedicalExemptionServiceFormState;
        _buildForm = (context) {
          final initialOrderItem = widget.initialOrderItem;
          return MedicalExemptionServiceForm(
            key: _formKey,
            initialCost: initialOrderItem?.price,
            initialNote: initialOrderItem?.description,
          );
        };
        _validateForm = () => getForm().validate();
        _saveForm = () => getForm().save();
        _createCartItem = (inCash) => OrderItem(
              service: service,
              price: getForm().cost!,
              description: getForm().note,
              inCash: inCash,
            );
        break;
      default:
        OrdinaryServiceFormState getForm() =>
            _formKey.currentState! as OrdinaryServiceFormState;
        _buildForm = (context) => OrdinaryServiceForm(
              key: _formKey,
              initialCost: widget.initialOrderItem?.price,
            );
        _validateForm = () => getForm().validate();
        _saveForm = () => getForm().save();
        _createCartItem = (inCash) => OrderItem(
              service: service,
              price: getForm().cost!,
              inCash: inCash,
            );
    }
  }

  @override
  Widget build(BuildContext context) => BottomSheetScaffold(
        title: widget.service.name,
        titleAlignment: MainAxisAlignment.center,
        body: _buildForm(context),
        bottomBar: _buildBottomBar(),
      );

  Widget _buildBottomBar() => Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Row(
            children: [
              Flexible(
                child: Button.icon(
                  label: Text(AppPhrases.inCashAbbr),
                  icon: SvgPicture.asset(
                    AppImages.rubleInCash,
                    colorFilter: ColorFilter.mode(
                      AppColors.primaryButtonForeground,
                      BlendMode.srcIn,
                    ),
                    width: Sizes.p24,
                    height: Sizes.p24,
                  ),
                  style: primaryMediumButtonStyle,
                  onPressed: _onInCashButtonPressed,
                ),
              ),
              Gaps.w8,
              Flexible(
                child: Button.icon(
                  label: Text(AppPhrases.cashlessAbbr),
                  icon: SvgPicture.asset(
                    AppImages.bankCard,
                    colorFilter: ColorFilter.mode(
                      AppColors.primaryButtonForeground,
                      BlendMode.srcIn,
                    ),
                    width: Sizes.p24,
                    height: Sizes.p24,
                  ),
                  style: primaryMediumButtonStyle,
                  onPressed: _onCashlessButtonPressed,
                ),
              ),
            ],
          ),
          if (widget.initialOrderItem != null) ...[
            Gaps.h8,
            Flexible(
              child: Button(
                label: Text(AppPhrases.remove),
                style: secondaryMediumButtonStyle,
                onPressed: widget.onRemoveFromCart,
              ),
            ),
          ],
        ],
      );

  void _onInCashButtonPressed() {
    if (!_validateForm()) return;
    _saveForm();
    final inCash = true;
    widget.onPutIntoCart?.call(
      _createCartItem(inCash),
    );
  }

  void _onCashlessButtonPressed() {
    if (!_validateForm()) return;
    _saveForm();
    final inCash = false;
    widget.onPutIntoCart?.call(
      _createCartItem(inCash),
    );
  }
}

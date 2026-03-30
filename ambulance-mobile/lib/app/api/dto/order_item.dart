import 'package:ambulance/app/api/dto/ambulance_call_service.dart';
import 'package:ambulance/app/api/dto/clinic.dart';
import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';


part 'order_item.g.dart';

@JsonSerializable(includeIfNull: false)
class OrderItem extends Equatable {
  final AmbulanceCallService service;

  final int? price;
  final int? plannedPrice;
  final String? plannedAt;
  final String? description;
  final int? partnerReward;
  final int? coastPrice;
  final Clinic? clinic;
  final bool? inCash;
  final List<String>? files;

  const OrderItem({
    required this.service,
    this.price,
    this.plannedPrice,
    this.plannedAt,
    this.description,
    this.partnerReward,
    this.coastPrice,
    this.clinic,
    this.inCash,
    this.files,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) =>
      _$OrderItemFromJson(json);
  Map<String, dynamic> toJson() => _$OrderItemToJson(this);

  @override
  List<Object?> get props => [
        price,
        plannedAt,
        plannedPrice,
        description,
        partnerReward,
        coastPrice,
        clinic,
        inCash,
        files
      ];
}

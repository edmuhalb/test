// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'order_item.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

OrderItem _$OrderItemFromJson(Map<String, dynamic> json) => OrderItem(
      service: AmbulanceCallService.fromJson(
          json['service'] as Map<String, dynamic>),
      price: (json['price'] as num?)?.toInt(),
      plannedPrice: (json['plannedPrice'] as num?)?.toInt(),
      plannedAt: json['plannedAt'] as String?,
      description: json['description'] as String?,
      partnerReward: (json['partnerReward'] as num?)?.toInt(),
      coastPrice: (json['coastPrice'] as num?)?.toInt(),
      clinic: json['clinic'] == null
          ? null
          : Clinic.fromJson(json['clinic'] as Map<String, dynamic>),
      inCash: json['inCash'] as bool?,
      files:
          (json['files'] as List<dynamic>?)?.map((e) => e as String).toList(),
    );

Map<String, dynamic> _$OrderItemToJson(OrderItem instance) => <String, dynamic>{
      'service': instance.service.toJson(),
      if (instance.price case final value?) 'price': value,
      if (instance.plannedPrice case final value?) 'plannedPrice': value,
      if (instance.plannedAt case final value?) 'plannedAt': value,
      if (instance.description case final value?) 'description': value,
      if (instance.partnerReward case final value?) 'partnerReward': value,
      if (instance.coastPrice case final value?) 'coastPrice': value,
      if (instance.clinic?.toJson() case final value?) 'clinic': value,
      if (instance.inCash case final value?) 'inCash': value,
      if (instance.files case final value?) 'files': value,
    };

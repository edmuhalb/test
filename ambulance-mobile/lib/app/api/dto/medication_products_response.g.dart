// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'medication_products_response.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

MedicationProductsResponse _$MedicationProductsResponseFromJson(
        Map<String, dynamic> json) =>
    MedicationProductsResponse(
      items: (json['items'] as List<dynamic>)
          .map((e) => MedicationProduct.fromJson(e as Map<String, dynamic>))
          .toList(),
    );

Map<String, dynamic> _$MedicationProductsResponseToJson(
        MedicationProductsResponse instance) =>
    <String, dynamic>{
      'items': instance.items.map((e) => e.toJson()).toList(),
    };

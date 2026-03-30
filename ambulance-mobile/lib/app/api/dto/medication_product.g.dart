// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'medication_product.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

MedicationProduct _$MedicationProductFromJson(Map<String, dynamic> json) =>
    MedicationProduct(
      id: (json['id'] as num).toInt(),
      name: json['name'] as String,
      categoryId: (json['categoryId'] as num).toInt(),
      price: (json['price'] as num).toDouble(),
      quantity: (json['quantity'] as num).toInt(),
      products: (json['products'] as List<dynamic>?)
          ?.map((e) => MedicationProduct.fromJson(e as Map<String, dynamic>))
          .toList(),
      unitLabel: json['unitLabel'] as String?,
    );

Map<String, dynamic> _$MedicationProductToJson(MedicationProduct instance) =>
    <String, dynamic>{
      'id': instance.id,
      'name': instance.name,
      'categoryId': instance.categoryId,
      'price': instance.price,
      'quantity': instance.quantity,
      'products': instance.products?.map((e) => e.toJson()).toList(),
      'unitLabel': instance.unitLabel,
    };

// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'provided_service.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

ProvidedService _$ProvidedServiceFromJson(Map<String, dynamic> json) =>
    ProvidedService(
      name: json['name'] as String,
      price: (json['price'] as num).toInt(),
    );

Map<String, dynamic> _$ProvidedServiceToJson(ProvidedService instance) =>
    <String, dynamic>{
      'name': instance.name,
      'price': instance.price,
    };

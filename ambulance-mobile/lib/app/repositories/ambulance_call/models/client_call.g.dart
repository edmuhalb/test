// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'client_call.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

ClientCall _$ClientCallFromJson(Map<String, dynamic> json) => ClientCall(
      id: (json['id'] as num).toInt(),
      status: json['status'] as String?,
      completedAt: json['completedAt'] as String?,
      services: (json['services'] as List<dynamic>?)
          ?.map((e) => e as Map<String, dynamic>)
          .toList(),
      price: (json['price'] as num?)?.toInt(),
      client: json['client'] as Map<String, dynamic>?,
    );

Map<String, dynamic> _$ClientCallToJson(ClientCall instance) =>
    <String, dynamic>{
      'id': instance.id,
      'status': instance.status,
      'completedAt': instance.completedAt,
      'services': instance.services,
      'price': instance.price,
      'client': instance.client,
    };

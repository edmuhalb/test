// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ambulance_call_client.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

AmbulanceCallClient _$AmbulanceCallClientFromJson(Map<String, dynamic> json) =>
    AmbulanceCallClient(
      id: (json['id'] as num).toInt(),
      phone: json['phone'] as String?,
      name: json['name'] as String?,
    );

Map<String, dynamic> _$AmbulanceCallClientToJson(
        AmbulanceCallClient instance) =>
    <String, dynamic>{
      'id': instance.id,
      'phone': instance.phone,
      'name': instance.name,
    };

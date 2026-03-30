// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ambulance_call_service.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

AmbulanceCallService _$AmbulanceCallServiceFromJson(
        Map<String, dynamic> json) =>
    AmbulanceCallService(
      id: (json['id'] as num).toInt(),
      name: json['name'] as String,
    );

Map<String, dynamic> _$AmbulanceCallServiceToJson(
        AmbulanceCallService instance) =>
    <String, dynamic>{
      'id': instance.id,
      'name': instance.name,
    };

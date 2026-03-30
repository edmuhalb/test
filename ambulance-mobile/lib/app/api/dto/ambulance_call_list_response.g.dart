// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ambulance_call_list_response.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

AmbulanceCallListResponse _$AmbulanceCallListResponseFromJson(
        Map<String, dynamic> json) =>
    AmbulanceCallListResponse(
      items: (json['items'] as List<dynamic>)
          .map((e) => Entity.fromJson(e as Map<String, dynamic>))
          .toList(),
    );

Map<String, dynamic> _$AmbulanceCallListResponseToJson(
        AmbulanceCallListResponse instance) =>
    <String, dynamic>{
      'items': instance.items.map((e) => e.toJson()).toList(),
    };

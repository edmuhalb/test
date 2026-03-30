// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ambulance_call_list_item.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

AmbulanceCallListItem _$AmbulanceCallListItemFromJson(
        Map<String, dynamic> json) =>
    AmbulanceCallListItem(
      id: (json['id'] as num).toInt(),
      title: json['title'] as String,
      name: json['name'] as String,
      phone: json['phone'] as String,
      address: json['address'] as String,
      createdAt: json['createdAt'] as String,
      dateTime: json['dateTime'] as String?,
      status: json['status'] as String,
      statusLabel: json['statusLabel'] as String,
    );

Map<String, dynamic> _$AmbulanceCallListItemToJson(
        AmbulanceCallListItem instance) =>
    <String, dynamic>{
      'id': instance.id,
      'title': instance.title,
      'name': instance.name,
      'phone': instance.phone,
      'address': instance.address,
      'createdAt': instance.createdAt,
      'dateTime': instance.dateTime,
      'status': instance.status,
      'statusLabel': instance.statusLabel,
    };

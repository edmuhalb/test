// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'rejection_reason_list_response.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

RejectionReasonListResponse _$RejectionReasonListResponseFromJson(
        Map<String, dynamic> json) =>
    RejectionReasonListResponse(
      items: (json['items'] as List<dynamic>)
          .map((e) => Entity.fromJson(e as Map<String, dynamic>))
          .toList(),
    );

Map<String, dynamic> _$RejectionReasonListResponseToJson(
        RejectionReasonListResponse instance) =>
    <String, dynamic>{
      'items': instance.items.map((e) => e.toJson()).toList(),
    };

// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'shift_detail.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

ShiftDetail _$ShiftDetailFromJson(Map<String, dynamic> json) => ShiftDetail(
      id: (json['id'] as num).toInt(),
      plannedStartAt: json['plannedStartAt'] as String,
      startedAt: json['startedAt'] as String?,
      completedAt: json['completedAt'] as String?,
      status: json['status'] as String,
      admin: json['admin'] == null
          ? null
          : Entity.fromJson(json['admin'] as Map<String, dynamic>),
      doctor: json['doctor'] == null
          ? null
          : Entity.fromJson(json['doctor'] as Map<String, dynamic>),
      driver: json['driver'] == null
          ? null
          : Entity.fromJson(json['driver'] as Map<String, dynamic>),
      plannedFinishAt: json['plannedFinishAt'] as String?,
      base: json['base'] == null
          ? null
          : Entity.fromJson(json['base'] as Map<String, dynamic>),
      car: json['car'] == null
          ? null
          : Entity.fromJson(json['car'] as Map<String, dynamic>),
      plannedDutyStartAt: json['plannedDutyStartAt'] as String?,
      plannedDutyFinishAt: json['plannedDutyFinishAt'] as String?,
      type: json['type'] as String?,
      city: json['city'] == null
          ? null
          : Entity.fromJson(json['city'] as Map<String, dynamic>),
      transportReport: json['transportReport'] == null
          ? null
          : TransportReportDetail.fromJson(
              json['transportReport'] as Map<String, dynamic>),
    );

Map<String, dynamic> _$ShiftDetailToJson(ShiftDetail instance) =>
    <String, dynamic>{
      'id': instance.id,
      'plannedStartAt': instance.plannedStartAt,
      'startedAt': instance.startedAt,
      'completedAt': instance.completedAt,
      'status': instance.status,
      'admin': instance.admin?.toJson(),
      'doctor': instance.doctor?.toJson(),
      'driver': instance.driver?.toJson(),
      'plannedFinishAt': instance.plannedFinishAt,
      'base': instance.base?.toJson(),
      'car': instance.car?.toJson(),
      'plannedDutyStartAt': instance.plannedDutyStartAt,
      'plannedDutyFinishAt': instance.plannedDutyFinishAt,
      'type': instance.type,
      'city': instance.city?.toJson(),
      'transportReport': instance.transportReport?.toJson(),
    };

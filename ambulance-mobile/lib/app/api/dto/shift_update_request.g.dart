// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'shift_update_request.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

ShiftUpdateRequest _$ShiftUpdateRequestFromJson(Map<String, dynamic> json) =>
    ShiftUpdateRequest(
      plannedStartAt: json['plannedStartAt'] as String?,
      startedAt: json['startedAt'] as String?,
      completedAt: json['completedAt'] as String?,
      status: json['status'] as String?,
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

Map<String, dynamic> _$ShiftUpdateRequestToJson(ShiftUpdateRequest instance) =>
    <String, dynamic>{
      if (instance.plannedStartAt case final value?) 'plannedStartAt': value,
      if (instance.startedAt case final value?) 'startedAt': value,
      if (instance.completedAt case final value?) 'completedAt': value,
      if (instance.status case final value?) 'status': value,
      if (instance.admin?.toJson() case final value?) 'admin': value,
      if (instance.doctor?.toJson() case final value?) 'doctor': value,
      if (instance.driver?.toJson() case final value?) 'driver': value,
      if (instance.plannedFinishAt case final value?) 'plannedFinishAt': value,
      if (instance.base?.toJson() case final value?) 'base': value,
      if (instance.car?.toJson() case final value?) 'car': value,
      if (instance.plannedDutyStartAt case final value?)
        'plannedDutyStartAt': value,
      if (instance.plannedDutyFinishAt case final value?)
        'plannedDutyFinishAt': value,
      if (instance.type case final value?) 'type': value,
      if (instance.city?.toJson() case final value?) 'city': value,
      if (instance.transportReport?.toJson() case final value?)
        'transportReport': value,
    };

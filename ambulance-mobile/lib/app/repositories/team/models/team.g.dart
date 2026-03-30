// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'team.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

Team _$TeamFromJson(Map<String, dynamic> json) => Team(
      id: (json['id'] as num).toInt(),
      plannedStartAt: json['plannedStartAt'] as String,
      plannedFinishAt: json['plannedFinishAt'] as String,
      plannedAt: json['plannedAt'] as String?,
      startedAt: json['startedAt'] as String?,
      completedAt: json['completedAt'] as String?,
      status: json['status'] as String,
      admin: json['admin'] as Map<String, dynamic>?,
      doctor: json['doctor'] as Map<String, dynamic>?,
      phone: json['phone'] as Map<String, dynamic>?,
    );

Map<String, dynamic> _$TeamToJson(Team instance) => <String, dynamic>{
      'id': instance.id,
      'plannedAt': instance.plannedAt,
      'plannedStartAt': instance.plannedStartAt,
      'plannedFinishAt': instance.plannedFinishAt,
      'startedAt': instance.startedAt,
      'completedAt': instance.completedAt,
      'status': instance.status,
      'admin': instance.admin,
      'doctor': instance.doctor,
      'phone': instance.phone,
    };

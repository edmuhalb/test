import 'package:json_annotation/json_annotation.dart';

import 'entity.dart';
import 'transport_report_detail.dart';

part 'shift_detail.g.dart';

@JsonSerializable()
class ShiftDetail {
  final int id;
  final String plannedStartAt; // e.g. "2024-05-20T10:00:00Z"
  final String? startedAt;     
  final String? completedAt;   
  final String status;         // e.g. "draft", "scheduled", "work", "completed", "cancelled"

  final Entity? admin;
  final Entity? doctor;
  final Entity? driver;

  final String? plannedFinishAt;
  final Entity? base;
  final Entity? car;
  final String? plannedDutyStartAt;
  final String? plannedDutyFinishAt;

  final String? type;

  final Entity? city;

  final TransportReportDetail? transportReport;

  ShiftDetail({
    required this.id,
    required this.plannedStartAt,
    this.startedAt,
    this.completedAt,
    required this.status,
    this.admin,
    this.doctor,
    this.driver,
    this.plannedFinishAt,
    this.base,
    this.car,
    this.plannedDutyStartAt,
    this.plannedDutyFinishAt,
    this.type,
    this.city,
    this.transportReport,
  });

  factory ShiftDetail.fromJson(Map<String, dynamic> json) =>
      _$ShiftDetailFromJson(json);

  Map<String, dynamic> toJson() => _$ShiftDetailToJson(this);
}
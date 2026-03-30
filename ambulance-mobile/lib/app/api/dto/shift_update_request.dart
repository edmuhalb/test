import 'package:ambulance/app/api/dto/transport_report_detail.dart';
import 'package:json_annotation/json_annotation.dart';

import 'entity.dart';

part 'shift_update_request.g.dart';

@JsonSerializable(includeIfNull: false)
class ShiftUpdateRequest {
  final String? plannedStartAt;
  final String? startedAt;
  final String? completedAt;
  final String? status;

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

  ShiftUpdateRequest({
    this.plannedStartAt,
    this.startedAt,
    this.completedAt,
    this.status,
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

  factory ShiftUpdateRequest.fromJson(Map<String, dynamic> json) =>
      _$ShiftUpdateRequestFromJson(json);

  Map<String, dynamic> toJson() => _$ShiftUpdateRequestToJson(this);
}

import 'package:json_annotation/json_annotation.dart';

import 'remote_file.dart';

part 'transport_report_detail.g.dart';

@JsonSerializable()
class TransportReportDetail {
  final int? mileage;
  final int? toolRoad;
  final int? parkingFees;

  final List<String>? files;

  TransportReportDetail({
    this.mileage,
    this.toolRoad,
    this.parkingFees,
    this.files,
  });

  factory TransportReportDetail.fromJson(Map<String, dynamic> json) =>
      _$TransportReportDetailFromJson(json);
  Map<String, dynamic> toJson() => _$TransportReportDetailToJson(this);
}
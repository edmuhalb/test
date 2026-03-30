// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'transport_report_detail.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

TransportReportDetail _$TransportReportDetailFromJson(
        Map<String, dynamic> json) =>
    TransportReportDetail(
      mileage: (json['mileage'] as num?)?.toInt(),
      toolRoad: (json['toolRoad'] as num?)?.toInt(),
      parkingFees: (json['parkingFees'] as num?)?.toInt(),
      files:
          (json['files'] as List<dynamic>?)?.map((e) => e as String).toList(),
    );

Map<String, dynamic> _$TransportReportDetailToJson(
        TransportReportDetail instance) =>
    <String, dynamic>{
      'mileage': instance.mileage,
      'toolRoad': instance.toolRoad,
      'parkingFees': instance.parkingFees,
      'files': instance.files,
    };

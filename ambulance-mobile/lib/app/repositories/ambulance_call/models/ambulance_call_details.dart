import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'ambulance_call_details.g.dart';

@JsonSerializable()
class AmbulanceCallDetails extends Equatable {
  final int id;
  final String? title;
  final String? description;
  final String? name;
  final String? phone;
  final String? address;
  final String? addressInfo;
  final String? numberCalling;
  final String? leadType;
  final String? status;
  final String? statusLabel;
  final String? chronicDiseases;
  final String? nosology;
  final String? partnerName;
  final String? rejectedComment;
  final String? note;
  final String? passport;
  final String? resultDate;
  final String? resultTime;
  final String? dateTime;
  final String? lat;
  final String? lon;
  final String? age;
  final int? price;
  final int? paymentNextOrder;
  final int? totalAmount;
  final int? estimated;
  final int? prepayment;
  final int? coastHospitalAdmission;
  final int? coastHospital;
  final int? costDay;
  final Map<String, dynamic>? admin;
  final Map<String, dynamic>? doctor;
  final Map<String, dynamic>? client;
  final String? fio;
  final List<Map<String, dynamic>>? services;
  final List<Map<String, dynamic>>? images;
  final bool? currentPartnerHospitalization;
  final bool? currentNoBusinessCards;
  final bool? personal;
  final bool? doNotHospitalize;

  const AmbulanceCallDetails({
    required this.id,
    required this.title,
    required this.description,
    required this.name,
    required this.phone,
    required this.address,
    required this.addressInfo,
    required this.numberCalling,
    required this.chronicDiseases,
    required this.nosology,
    required this.partnerName,
    required this.rejectedComment,
    required this.note,
    required this.passport,
    required this.resultDate,
    required this.resultTime,
    required this.dateTime,
    required this.lat,
    required this.lon,
    required this.age,
    required this.price,
    required this.paymentNextOrder,
    required this.totalAmount,
    required this.estimated,
    required this.prepayment,
    required this.coastHospitalAdmission,
    required this.coastHospital,
    required this.costDay,
    required this.leadType,
    required this.status,
    required this.statusLabel,
    required this.admin,
    required this.doctor,
    required this.client,
    required this.fio,
    required this.services,
    required this.images,
    required this.currentPartnerHospitalization,
    required this.currentNoBusinessCards,
    required this.personal,
    required this.doNotHospitalize,
  });

  @override
  List<Object?> get props => [];

  factory AmbulanceCallDetails.fromJson(Map<String, dynamic> json) =>
      _$AmbulanceCallDetailsFromJson(json);

  Map<String, dynamic> toJson() => _$AmbulanceCallDetailsToJson(this);
}

extension AmbulanceCallDetailsExt on AmbulanceCallDetails {
  // TODO: refactor
  String get clientName => client?['name'] ?? '';
}

import 'package:json_annotation/json_annotation.dart';

import 'ambulance_call_client.dart';
import 'entity.dart';
import 'order_item.dart';

part 'ambulance_call_detail.g.dart';

@JsonSerializable()
class AmbulanceCallDetail {
  final int id;
  final String title;
  final String name;
  final String phone;
  final String? fio;
  final String numberCalling;
  final String address;
  final String description;
  final String? chronicDiseases;
  final String? nosology;
  final String? age;
  final String? leadType;
  final String? partnerName;
  final Entity? reasonForCancellation;
  final String? dateTime;
  final Entity? admin;
  final Entity? doctor;
  final Entity? partner;

  final int? price;
  final int? estimated;
  final int? prepayment;
  final String? note;
  final String? passport;
  final int? coastHospitalAdmission;
  final int? coastHospital;
  final int? costDay;
  final String? resultDate;
  final String? resultTime;

  final String? lon;
  final String? lat;

  // order
  final List<OrderItem> services;

  final int? paymentNextOrder;
  final int? totalAmount;
  final AmbulanceCallClient? client;
  final bool personal;
  final bool doNotHospitalize;
  final List<String> images;
  final String? addressInfo;
  final String? arrivalDateTime;
  final String? endOfServiceDateTime;
  final String? birthday;
  final bool currentNoBusinessCards;
  final bool currentPartnerHospitalization;
  final String status;
  final String statusLabel;
  final bool isShiftOpen;

  AmbulanceCallDetail({
    required this.id,
    required this.title,
    required this.name,
    required this.phone,
    this.fio,
    required this.numberCalling,
    required this.address,
    required this.description,
    this.chronicDiseases,
    this.nosology,
    this.age,
    this.leadType,
    this.partnerName,
    this.reasonForCancellation,
    this.dateTime,
    this.admin,
    this.doctor,
    this.partner,
    this.price,
    this.estimated,
    this.prepayment,
    this.note,
    this.passport,
    this.coastHospitalAdmission,
    this.coastHospital,
    this.costDay,
    this.resultDate,
    this.resultTime,
    this.lon,
    this.lat,
    required this.services,
    this.paymentNextOrder,
    this.totalAmount,
    this.client,
    required this.personal,
    required this.doNotHospitalize,
    required this.images,
    this.addressInfo,
    this.arrivalDateTime,
    this.endOfServiceDateTime,
    this.birthday,
    required this.status,
    required this.currentNoBusinessCards,
    required this.currentPartnerHospitalization,
    required this.statusLabel,
    this.isShiftOpen = false,
  });

  factory AmbulanceCallDetail.fromJson(Map<String, dynamic> json) =>
      _$AmbulanceCallDetailFromJson(json);
  Map<String, dynamic> toJson() => _$AmbulanceCallDetailToJson(this);
}
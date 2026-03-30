import 'package:ambulance/app/api/dto/order_item.dart';
import 'package:json_annotation/json_annotation.dart';

import 'ambulance_call_client.dart';
import 'entity.dart';

part 'ambulance_call_update_request.g.dart';

@JsonSerializable(includeIfNull: false)
class AmbulanceCallUpdateRequest {
  final String? title;
  final String? name;
  final String? phone;
  final String? fio;
  final String? numberCalling;
  final String? address;
  final String? description;
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

  final List<OrderItem>? services;

  final int? paymentNextOrder;
  final int? totalAmount;
  final AmbulanceCallClient? client;
  final bool? personal;
  final bool? doNotHospitalize;
  final List<String>? images;
  final String? addressInfo;
  final String? arrivalDateTime;
  final String? endOfServiceDateTime;
  final String? birthday;

  final String? status;

  final bool? currentNoBusinessCards;
  final bool? currentPartnerHospitalization;
  final String? statusLabel;

  AmbulanceCallUpdateRequest({
    this.title,
    this.name,
    this.phone,
    this.fio,
    this.numberCalling,
    this.address,
    this.description,
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
    this.services,
    this.paymentNextOrder,
    this.totalAmount,
    this.client,
    this.personal,
    this.doNotHospitalize,
    this.images,
    this.addressInfo,
    this.arrivalDateTime,
    this.endOfServiceDateTime,
    this.birthday,
    this.status,
    this.currentNoBusinessCards,
    this.currentPartnerHospitalization,
    this.statusLabel,
  });

  factory AmbulanceCallUpdateRequest.fromJson(Map<String, dynamic> json) =>
      _$AmbulanceCallUpdateRequestFromJson(json);

  Map<String, dynamic> toJson() => _$AmbulanceCallUpdateRequestToJson(this);
}

// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ambulance_call_detail.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

AmbulanceCallDetail _$AmbulanceCallDetailFromJson(Map<String, dynamic> json) =>
    AmbulanceCallDetail(
      id: (json['id'] as num).toInt(),
      title: json['title'] as String,
      name: json['name'] as String,
      phone: json['phone'] as String,
      fio: json['fio'] as String?,
      numberCalling: json['numberCalling'] as String,
      address: json['address'] as String,
      description: json['description'] as String,
      chronicDiseases: json['chronicDiseases'] as String?,
      nosology: json['nosology'] as String?,
      age: json['age'] as String?,
      leadType: json['leadType'] as String?,
      partnerName: json['partnerName'] as String?,
      reasonForCancellation: json['reasonForCancellation'] == null
          ? null
          : Entity.fromJson(
              json['reasonForCancellation'] as Map<String, dynamic>),
      dateTime: json['dateTime'] as String?,
      admin: json['admin'] == null
          ? null
          : Entity.fromJson(json['admin'] as Map<String, dynamic>),
      doctor: json['doctor'] == null
          ? null
          : Entity.fromJson(json['doctor'] as Map<String, dynamic>),
      partner: json['partner'] == null
          ? null
          : Entity.fromJson(json['partner'] as Map<String, dynamic>),
      price: (json['price'] as num?)?.toInt(),
      estimated: (json['estimated'] as num?)?.toInt(),
      prepayment: (json['prepayment'] as num?)?.toInt(),
      note: json['note'] as String?,
      passport: json['passport'] as String?,
      coastHospitalAdmission: (json['coastHospitalAdmission'] as num?)?.toInt(),
      coastHospital: (json['coastHospital'] as num?)?.toInt(),
      costDay: (json['costDay'] as num?)?.toInt(),
      resultDate: json['resultDate'] as String?,
      resultTime: json['resultTime'] as String?,
      lon: json['lon'] as String?,
      lat: json['lat'] as String?,
      services: (json['services'] as List<dynamic>)
          .map((e) => OrderItem.fromJson(e as Map<String, dynamic>))
          .toList(),
      paymentNextOrder: (json['paymentNextOrder'] as num?)?.toInt(),
      totalAmount: (json['totalAmount'] as num?)?.toInt(),
      client: json['client'] == null
          ? null
          : AmbulanceCallClient.fromJson(
              json['client'] as Map<String, dynamic>),
      personal: json['personal'] as bool,
      doNotHospitalize: json['doNotHospitalize'] as bool,
      images:
          (json['images'] as List<dynamic>).map((e) => e as String).toList(),
      addressInfo: json['addressInfo'] as String?,
      arrivalDateTime: json['arrivalDateTime'] as String?,
      endOfServiceDateTime: json['endOfServiceDateTime'] as String?,
      birthday: json['birthday'] as String?,
      status: json['status'] as String,
      currentNoBusinessCards: json['currentNoBusinessCards'] as bool,
      currentPartnerHospitalization:
          json['currentPartnerHospitalization'] as bool,
      statusLabel: json['statusLabel'] as String,
      isShiftOpen: json['isShiftOpen'] as bool? ?? false,
    );

Map<String, dynamic> _$AmbulanceCallDetailToJson(
        AmbulanceCallDetail instance) =>
    <String, dynamic>{
      'id': instance.id,
      'title': instance.title,
      'name': instance.name,
      'phone': instance.phone,
      'fio': instance.fio,
      'numberCalling': instance.numberCalling,
      'address': instance.address,
      'description': instance.description,
      'chronicDiseases': instance.chronicDiseases,
      'nosology': instance.nosology,
      'age': instance.age,
      'leadType': instance.leadType,
      'partnerName': instance.partnerName,
      'reasonForCancellation': instance.reasonForCancellation?.toJson(),
      'dateTime': instance.dateTime,
      'admin': instance.admin?.toJson(),
      'doctor': instance.doctor?.toJson(),
      'partner': instance.partner?.toJson(),
      'price': instance.price,
      'estimated': instance.estimated,
      'prepayment': instance.prepayment,
      'note': instance.note,
      'passport': instance.passport,
      'coastHospitalAdmission': instance.coastHospitalAdmission,
      'coastHospital': instance.coastHospital,
      'costDay': instance.costDay,
      'resultDate': instance.resultDate,
      'resultTime': instance.resultTime,
      'lon': instance.lon,
      'lat': instance.lat,
      'services': instance.services.map((e) => e.toJson()).toList(),
      'paymentNextOrder': instance.paymentNextOrder,
      'totalAmount': instance.totalAmount,
      'client': instance.client?.toJson(),
      'personal': instance.personal,
      'doNotHospitalize': instance.doNotHospitalize,
      'images': instance.images,
      'addressInfo': instance.addressInfo,
      'arrivalDateTime': instance.arrivalDateTime,
      'endOfServiceDateTime': instance.endOfServiceDateTime,
      'birthday': instance.birthday,
      'currentNoBusinessCards': instance.currentNoBusinessCards,
      'currentPartnerHospitalization': instance.currentPartnerHospitalization,
      'status': instance.status,
      'statusLabel': instance.statusLabel,
      'isShiftOpen': instance.isShiftOpen,
    };

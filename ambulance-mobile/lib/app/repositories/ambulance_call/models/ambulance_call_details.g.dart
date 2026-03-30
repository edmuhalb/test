// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ambulance_call_details.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

AmbulanceCallDetails _$AmbulanceCallDetailsFromJson(
        Map<String, dynamic> json) =>
    AmbulanceCallDetails(
      id: (json['id'] as num).toInt(),
      title: json['title'] as String?,
      description: json['description'] as String?,
      name: json['name'] as String?,
      phone: json['phone'] as String?,
      address: json['address'] as String?,
      addressInfo: json['addressInfo'] as String?,
      numberCalling: json['numberCalling'] as String?,
      chronicDiseases: json['chronicDiseases'] as String?,
      nosology: json['nosology'] as String?,
      partnerName: json['partnerName'] as String?,
      rejectedComment: json['rejectedComment'] as String?,
      note: json['note'] as String?,
      passport: json['passport'] as String?,
      resultDate: json['resultDate'] as String?,
      resultTime: json['resultTime'] as String?,
      dateTime: json['dateTime'] as String?,
      lat: json['lat'] as String?,
      lon: json['lon'] as String?,
      age: json['age'] as String?,
      price: (json['price'] as num?)?.toInt(),
      paymentNextOrder: (json['paymentNextOrder'] as num?)?.toInt(),
      totalAmount: (json['totalAmount'] as num?)?.toInt(),
      estimated: (json['estimated'] as num?)?.toInt(),
      prepayment: (json['prepayment'] as num?)?.toInt(),
      coastHospitalAdmission: (json['coastHospitalAdmission'] as num?)?.toInt(),
      coastHospital: (json['coastHospital'] as num?)?.toInt(),
      costDay: (json['costDay'] as num?)?.toInt(),
      leadType: json['leadType'] as String?,
      status: json['status'] as String?,
      statusLabel: json['statusLabel'] as String?,
      admin: json['admin'] as Map<String, dynamic>?,
      doctor: json['doctor'] as Map<String, dynamic>?,
      client: json['client'] as Map<String, dynamic>?,
      fio: json['fio'] as String?,
      services: (json['services'] as List<dynamic>?)
          ?.map((e) => e as Map<String, dynamic>)
          .toList(),
      images: (json['images'] as List<dynamic>?)
          ?.map((e) => e as Map<String, dynamic>)
          .toList(),
      currentPartnerHospitalization:
          json['currentPartnerHospitalization'] as bool?,
      currentNoBusinessCards: json['currentNoBusinessCards'] as bool?,
      personal: json['personal'] as bool?,
      doNotHospitalize: json['doNotHospitalize'] as bool?,
    );

Map<String, dynamic> _$AmbulanceCallDetailsToJson(
        AmbulanceCallDetails instance) =>
    <String, dynamic>{
      'id': instance.id,
      'title': instance.title,
      'description': instance.description,
      'name': instance.name,
      'phone': instance.phone,
      'address': instance.address,
      'addressInfo': instance.addressInfo,
      'numberCalling': instance.numberCalling,
      'leadType': instance.leadType,
      'status': instance.status,
      'statusLabel': instance.statusLabel,
      'chronicDiseases': instance.chronicDiseases,
      'nosology': instance.nosology,
      'partnerName': instance.partnerName,
      'rejectedComment': instance.rejectedComment,
      'note': instance.note,
      'passport': instance.passport,
      'resultDate': instance.resultDate,
      'resultTime': instance.resultTime,
      'dateTime': instance.dateTime,
      'lat': instance.lat,
      'lon': instance.lon,
      'age': instance.age,
      'price': instance.price,
      'paymentNextOrder': instance.paymentNextOrder,
      'totalAmount': instance.totalAmount,
      'estimated': instance.estimated,
      'prepayment': instance.prepayment,
      'coastHospitalAdmission': instance.coastHospitalAdmission,
      'coastHospital': instance.coastHospital,
      'costDay': instance.costDay,
      'admin': instance.admin,
      'doctor': instance.doctor,
      'client': instance.client,
      'fio': instance.fio,
      'services': instance.services,
      'images': instance.images,
      'currentPartnerHospitalization': instance.currentPartnerHospitalization,
      'currentNoBusinessCards': instance.currentNoBusinessCards,
      'personal': instance.personal,
      'doNotHospitalize': instance.doNotHospitalize,
    };

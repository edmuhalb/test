// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ambulance_call_update_request.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

AmbulanceCallUpdateRequest _$AmbulanceCallUpdateRequestFromJson(
        Map<String, dynamic> json) =>
    AmbulanceCallUpdateRequest(
      title: json['title'] as String?,
      name: json['name'] as String?,
      phone: json['phone'] as String?,
      fio: json['fio'] as String?,
      numberCalling: json['numberCalling'] as String?,
      address: json['address'] as String?,
      description: json['description'] as String?,
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
      services: (json['services'] as List<dynamic>?)
          ?.map((e) => OrderItem.fromJson(e as Map<String, dynamic>))
          .toList(),
      paymentNextOrder: (json['paymentNextOrder'] as num?)?.toInt(),
      totalAmount: (json['totalAmount'] as num?)?.toInt(),
      client: json['client'] == null
          ? null
          : AmbulanceCallClient.fromJson(
              json['client'] as Map<String, dynamic>),
      personal: json['personal'] as bool?,
      doNotHospitalize: json['doNotHospitalize'] as bool?,
      images:
          (json['images'] as List<dynamic>?)?.map((e) => e as String).toList(),
      addressInfo: json['addressInfo'] as String?,
      arrivalDateTime: json['arrivalDateTime'] as String?,
      endOfServiceDateTime: json['endOfServiceDateTime'] as String?,
      birthday: json['birthday'] as String?,
      status: json['status'] as String?,
      currentNoBusinessCards: json['currentNoBusinessCards'] as bool?,
      currentPartnerHospitalization:
          json['currentPartnerHospitalization'] as bool?,
      statusLabel: json['statusLabel'] as String?,
    );

Map<String, dynamic> _$AmbulanceCallUpdateRequestToJson(
        AmbulanceCallUpdateRequest instance) =>
    <String, dynamic>{
      if (instance.title case final value?) 'title': value,
      if (instance.name case final value?) 'name': value,
      if (instance.phone case final value?) 'phone': value,
      if (instance.fio case final value?) 'fio': value,
      if (instance.numberCalling case final value?) 'numberCalling': value,
      if (instance.address case final value?) 'address': value,
      if (instance.description case final value?) 'description': value,
      if (instance.chronicDiseases case final value?) 'chronicDiseases': value,
      if (instance.nosology case final value?) 'nosology': value,
      if (instance.age case final value?) 'age': value,
      if (instance.leadType case final value?) 'leadType': value,
      if (instance.partnerName case final value?) 'partnerName': value,
      if (instance.reasonForCancellation?.toJson() case final value?)
        'reasonForCancellation': value,
      if (instance.dateTime case final value?) 'dateTime': value,
      if (instance.admin?.toJson() case final value?) 'admin': value,
      if (instance.doctor?.toJson() case final value?) 'doctor': value,
      if (instance.partner?.toJson() case final value?) 'partner': value,
      if (instance.price case final value?) 'price': value,
      if (instance.estimated case final value?) 'estimated': value,
      if (instance.prepayment case final value?) 'prepayment': value,
      if (instance.note case final value?) 'note': value,
      if (instance.passport case final value?) 'passport': value,
      if (instance.coastHospitalAdmission case final value?)
        'coastHospitalAdmission': value,
      if (instance.coastHospital case final value?) 'coastHospital': value,
      if (instance.costDay case final value?) 'costDay': value,
      if (instance.resultDate case final value?) 'resultDate': value,
      if (instance.resultTime case final value?) 'resultTime': value,
      if (instance.lon case final value?) 'lon': value,
      if (instance.lat case final value?) 'lat': value,
      if (instance.services?.map((e) => e.toJson()).toList() case final value?)
        'services': value,
      if (instance.paymentNextOrder case final value?)
        'paymentNextOrder': value,
      if (instance.totalAmount case final value?) 'totalAmount': value,
      if (instance.client?.toJson() case final value?) 'client': value,
      if (instance.personal case final value?) 'personal': value,
      if (instance.doNotHospitalize case final value?)
        'doNotHospitalize': value,
      if (instance.images case final value?) 'images': value,
      if (instance.addressInfo case final value?) 'addressInfo': value,
      if (instance.arrivalDateTime case final value?) 'arrivalDateTime': value,
      if (instance.endOfServiceDateTime case final value?)
        'endOfServiceDateTime': value,
      if (instance.birthday case final value?) 'birthday': value,
      if (instance.status case final value?) 'status': value,
      if (instance.currentNoBusinessCards case final value?)
        'currentNoBusinessCards': value,
      if (instance.currentPartnerHospitalization case final value?)
        'currentPartnerHospitalization': value,
      if (instance.statusLabel case final value?) 'statusLabel': value,
    };

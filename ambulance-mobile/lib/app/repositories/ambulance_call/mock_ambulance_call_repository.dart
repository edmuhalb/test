import 'dart:convert';
import 'dart:math';

import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:dio/dio.dart';

List<Map<String, dynamic>> generateMockCalls(int count) {
  final baseCall = jsonDecode("""
  {
    "id": 35165,
    "title": "Исх. 79872358897 тест",
    "name": "Николай Айти",
    "phone": "+74951096650",
    "fio": "Test",
    "numberCalling": "23916211",
    "address": "г Москва, ул Красная зеленая желтая синяя Пресня 1",
    "status": "assigned",
    "statusLabel": "Назначен",
    "description": "это тестовая заявка",
    "chronicDiseases": null,
    "nosology": "нарко",
    "age": "44",
    "leadType": "Наша",
    "partnerName": null,
    "sendPhone": false,
    "rejectedComment": null,
    "createdAt": "06.11.2024 12:11",
    "updatedAt": "25.11.2024 23:11",
    "acceptedAt": "2024-11-25T23:18:23+03:00",
    "dispatchedAt": "2024-11-25T23:23:47+03:00",
    "arrivedAt": "2024-11-25T23:23:51+03:00",
    "completedAt": "2024-11-20T21:43:57+03:00",
    "dateTime": "2024-11-26T22:15:00+03:00",
    "admin": {
        "id": 122,
        "phone": "79657965566",
        "name": "Димон"
    },
    "doctor": {
        "id": 41,
        "phone": "79000000033",
        "name": "ТестВрач"
    },
    "client": {
        "id": 13595,
        "phone": "79872358897",
        "name": "Николай Тест"
    },
    "price": 2000,
    "estimated": null,
    "prepayment": null,
    "note": "",
    "passport": null,
    "coastHospitalAdmission": null,
    "coastHospital": null,
    "costDay": null,
    "phoneRelatives": null,
    "resultDate": null,
    "resultTime": null,
    "partner": {
        "id": 123,
        "name": "Тестовый партнер",
        "whatsappGroup": null
    },
    "lon": "37.577157",
    "lat": "55.760226",
    "services": [],
    "paymentNextOrder": 0,
    "paymentHospitalization": 10,
    "totalAmount": 2010,
    "partnerReward": 500,
    "mkadDistance": 0,
    "ownerExternalId": null,
    "operator": null,
    "operatorReward": {
        "therapy": 10,
        "hospital": 10,
        "coding": 0,
        "stationary": 100,
        "total": 120
    },
    "noBusinessCards": true,
    "partnerHospitalization": true,
    "personal": true,
    "doNotHospitalize": true,
    "images": [],
    "addressInfo": null,
    "team": {
        "id": 5104,
        "status": "work",
        "phone": {
            "id": 0,
            "externalId": "0"
        }
    },
    "responsibleUserId": 6784588,
    "responsibleUserName": "Администратор",
    "city": {
        "id": 1,
        "name": "Москва",
        "externalId": "664367"
    },
    "repeat": 0,
    "currentNoBusinessCards": true,
    "currentPartnerHospitalization": true
  }""");

  return List<Map<String, dynamic>>.generate(count, (index) {
    final idOffset = index + 1;
    final newDateTime = DateTime.now()
        .subtract(Duration(days: index + Random().nextInt(3)))
        .toIso8601String();

    return {
      ...baseCall,
      "id": baseCall["id"] + idOffset,
      "title": "Исх. ${79872358897 + idOffset} тест",
      "name": "Николай ${['Айти', 'Смирнов', 'Петров', 'Иванов'][index % 4]}",
      "phone": "+7495${1096650 + idOffset}",
      "dateTime": newDateTime,
      "client": <String, dynamic>{
        ...baseCall["client"],
        "id": baseCall["client"]["id"] + idOffset,
        "phone": "7987${2358897 + idOffset}",
        "name": "Николай Клиент $idOffset"
      }
    };
  });
}

List<Map<String, dynamic>> generateClientHistory() {
  return [
    {
      "id": 32266,
      "status": "completed",
      "completedAt": "2024-11-06 12:35:28",
      "services": [
        {"name": "Госпитализация", "price": 50000},
        {"name": "Терапия", "price": 25000}
      ],
      "price": 75000,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 35171,
      "status": "completed",
      "completedAt": "2024-11-06 12:32:06",
      "services": [
        {"name": "Стационар", "price": 15000},
        {"name": "Терапия", "price": 33000},
        {"name": "Госпитализация", "price": 7000}
      ],
      "price": 40000,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 33961,
      "status": "completed",
      "completedAt": "2024-10-31 14:19:35",
      "services": [
        {"name": "Терапия", "price": 7777}
      ],
      "price": 7777,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 28881,
      "status": "completed",
      "completedAt": "2024-08-27 23:18:50",
      "services": [
        {"name": "Naltrexone имплантат 800", "price": 666},
        {"name": "Стационар", "price": 8888},
        {"name": "Госпитализация", "price": 5000},
        {"name": "Повтор", "price": 3000},
        {"name": "Терапия", "price": 1000}
      ],
      "price": 6666,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 28640,
      "status": "completed",
      "completedAt": "2024-08-25 14:19:52",
      "services": [
        {"name": "Naltrexone 300 мг/3 мл (масло/гель)", "price": 99},
        {"name": "Стационар", "price": 66},
        {"name": "Госпитализация", "price": 3000},
        {"name": "Терапия", "price": 300}
      ],
      "price": 3399,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 25034,
      "status": "completed",
      "completedAt": "2024-07-11 15:56:19",
      "services": [
        {"name": "Стационар", "price": 10000},
        {"name": "Терапия", "price": 100000}
      ],
      "price": 100000,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 24861,
      "status": "completed",
      "completedAt": "2024-07-09 13:44:40",
      "services": [
        {"name": "Госпитализация", "price": 150000},
        {"name": "Терапия", "price": 20000}
      ],
      "price": 170000,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 24472,
      "status": "completed",
      "completedAt": "2024-07-04 20:31:54",
      "services": [
        {"name": "Стационар", "price": 5000},
        {"name": "Терапия", "price": 200000}
      ],
      "price": 200000,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 24387,
      "status": "completed",
      "completedAt": "2024-07-03 23:01:30",
      "services": [
        {"name": "Стационар", "price": 50}
      ],
      "price": 0,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 22803,
      "status": "completed",
      "completedAt": null,
      "services": [],
      "price": null,
      "client": {"id": 13595, "name": "Николай Тест"}
    },
    {
      "id": 22800,
      "status": "completed",
      "completedAt": "2024-06-16 10:34:58",
      "services": [
        {"name": "Терапия", "price": 5000},
        {"name": "Стационар", "price": 500}
      ],
      "price": 5000,
      "client": {"id": 13595, "name": "Николай Тест"}
    }
  ];
}

class MockAmbulanceCallRepository implements AmbulanceCallRepository {
  final Dio dio;
  final List<Map<String, dynamic>> mockCalls = generateMockCalls(20);
  final List<Map<String, dynamic>> mockClientHistory = generateClientHistory();
  late final Map<int, Map<String, dynamic>> callsById = {
    for (var call in mockCalls) call['id']: call,
  };

  MockAmbulanceCallRepository({required this.dio});

  @override
  Future<List<AmbulanceCall>> getCallList(int adminId) async {
    await Future.delayed(Duration(seconds: 2));
    return mockCalls.map(AmbulanceCall.fromJson).toList()
      ..sort(
        (a, b) => DateTime.parse(b.dateTime!).compareTo(
          DateTime.parse(a.dateTime!),
        ),
      );
  }

  @override
  Future<List<ClientCall>> getCallsListByClient(
      int clientId, int callId) async {
    await Future.delayed(Duration(seconds: 2));
    return mockClientHistory.map(ClientCall.fromJson).toList();
  }

  @override
  Future<AmbulanceCallDetails> getCallDetails(int callId) async {
    await Future.delayed(Duration(seconds: 2));
    return AmbulanceCallDetails.fromJson(callsById[callId]!);
  }

  @override
  Future<AmbulanceCallDetails> update(
      int callId, Map<String, dynamic> fields) async {
    await Future.delayed(Duration(seconds: 2));
    final call = callsById[callId]!;
    call.addAll(fields);
    return AmbulanceCallDetails.fromJson(call);
  }

  @override
  Future<AmbulanceCallDetails> accept(int callId) async {
    await Future.delayed(Duration(seconds: 2));
    final json = callsById[callId]!;
    json['status'] = 'accepted';
    json['statusLabel'] = 'Принят';
    return AmbulanceCallDetails.fromJson(json);
  }

  @override
  Future<AmbulanceCallDetails> departed(
      int callId, DateTime arrivalDateTime) async {
    await Future.delayed(Duration(seconds: 2));
    final json = callsById[callId]!;
    json['status'] = 'dispatched';
    json['statusLabel'] = 'В пути';
    return AmbulanceCallDetails.fromJson(json);
  }

  @override
  Future<AmbulanceCallDetails> arrived(int callId) async {
    await Future.delayed(Duration(seconds: 2));
    final json = callsById[callId]!;
    json['status'] = 'arrived';
    json['statusLabel'] = 'Прибыли';
    return AmbulanceCallDetails.fromJson(json);
  }

  @override
  Future<AmbulanceCallDetails> startTreatment({
    required int ambCallId,
    required DateTime endOfServiceDateTime,
    required DateTime dateOfBirth,
    required String patientFullName,
    String? note,
  }) async {
    final json = callsById[ambCallId]!;
    json['status'] = 'treating';
    json['statusLabel'] = 'Начали лечение';
    return AmbulanceCallDetails.fromJson(json);
  }

  @override
  Future<AmbulanceCallDetails> finish({
    required int ambCallId,
    String? receiptComment,
    bool? advertised,
  }) async {
    await Future.delayed(Duration(seconds: 2));
    final json = callsById[ambCallId]!;
    json['status'] = AmbulanceCallStatus.completed;
    json['statusLabel'] = 'Завершен';
    json['receiptComment'] = receiptComment;
    json['advertised'] = advertised ?? false;
    return AmbulanceCallDetails.fromJson(json);
  }

  @override
  Future<AmbulanceCallDetails> reject(
    int callId,
    Map<String, dynamic> fields,
  ) async {
    await Future.delayed(Duration(seconds: 2));
    final json = callsById[callId]!;
    json['status'] = 'rejected';
    json['statusLabel'] = 'Отклонен';
    return AmbulanceCallDetails.fromJson(json);
  }

  @override
  Future<AmbulanceCallDetails> hospitalization(
      int callId, Map<String, dynamic> fields, bool withTherapy) async {
    final therapy = withTherapy ? 'with' : 'without';
    final String url = 'callings/$callId/hospitalization-$therapy-therapy';
    final response = await dio.post(url, data: fields);
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }

  @override
  Future<AmbulanceCallDetails> repeat(
      int callId, Map<String, dynamic> fields) async {
    final response = await dio.post('callings/$callId/repeat', data: fields);
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }
}

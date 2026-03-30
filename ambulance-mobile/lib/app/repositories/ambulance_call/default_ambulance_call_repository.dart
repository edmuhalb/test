import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:dio/dio.dart';

class DefaultAmbulanceCallRepository implements AmbulanceCallRepository {
  final Dio dio;

  DefaultAmbulanceCallRepository({required this.dio});

  @override
  Future<List<AmbulanceCall>> getCallList(int adminId) async {
    final response = await dio.get('calls', queryParameters: {
      'admin.id': adminId,
      'status[0]':'assigned',
      'status[1]':'accepted',
      'status[2]':'dispatched',
      'status[3]':'arrived',
      'status[4]':'treating',
      'status[5]':'completed',
      'status[6]':'rejected',
      'order[updatedAt]': 'desc',
    });

    return (response.data['items'] as List)
        .map((data) => AmbulanceCall.fromJson(data))
        .toList();
  }

  @override
  Future<List<ClientCall>> getCallsListByClient(
      int clientId, int callId) async {
    final response = await dio.get('calls/$callId/history');

    return (response.data as List)
        .map((data) => ClientCall.fromJson(data))
        .toList();
  }

  @override
  Future<AmbulanceCallDetails> getCallDetails(int callId) async {
    final response = await dio.get('callings/$callId');
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }

  @override
  Future<AmbulanceCallDetails> update(
      int callId, Map<String, dynamic> fields) async {
    final response = await dio.put('callings/$callId', data: fields);
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }

  @override
  Future<AmbulanceCallDetails> accept(int callId) async {
    final response = await dio.post('callings/$callId/accept', data: {});
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }

  @override
  Future<AmbulanceCallDetails> departed(
      int callId, DateTime arrivalDateTime) async {
    final response = await dio.post('callings/$callId/dispatch',
        data: {'arrivalDateTime': arrivalDateTime.toIso8601String()});
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }

  @override
  Future<AmbulanceCallDetails> arrived(int callId) async {
    final response = await dio.post('callings/$callId/arrive', data: {});
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }

  @override
  Future<AmbulanceCallDetails> startTreatment({
    required int ambCallId,
    required DateTime endOfServiceDateTime,
    required DateTime dateOfBirth,
    required String patientFullName,
    String? note,
  }) async {
    final response =
        await dio.post('callings/$ambCallId/start-treatment', data: {
      'fio': patientFullName,
      'dateOfBirth': dateOfBirth.toIso8601String(),
      'endOfServiceDateTime': endOfServiceDateTime.toIso8601String(),
      'note': note,
    });
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }

  @override
  Future<AmbulanceCallDetails> finish({
    required int ambCallId,
    String? receiptComment,
    bool? advertised,
  }) async {
    final response = await dio.post('callings/$ambCallId/finish', data: {
      'receiptComment': receiptComment,
      'advertised': advertised ?? false,
    });
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
  }

  @override
  Future<AmbulanceCallDetails> reject(
      int callId, Map<String, dynamic> fields) async {
    final response = await dio.post('callings/$callId/reject', data: fields);
    final data = response.data as Map<String, dynamic>;
    return AmbulanceCallDetails.fromJson(data);
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

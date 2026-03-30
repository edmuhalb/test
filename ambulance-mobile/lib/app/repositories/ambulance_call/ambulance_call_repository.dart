import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';

abstract class AmbulanceCallRepository {
  Future<List<AmbulanceCall>> getCallList(
    int adminId,
  );
  Future<List<ClientCall>> getCallsListByClient(
    int adminId,
    int ambCallId,
  );
  Future<AmbulanceCallDetails> getCallDetails(
    int ambCallId,
  );
  Future<AmbulanceCallDetails> accept(
    int ambCallId,
  );
  Future<AmbulanceCallDetails> departed(
    int ambCallId,
    DateTime arrivalDateTime,
  );
  Future<AmbulanceCallDetails> arrived(int ambCallId);
  Future<AmbulanceCallDetails> startTreatment({
    required int ambCallId,
    required DateTime endOfServiceDateTime,
    required DateTime dateOfBirth,
    required String patientFullName,
    String? note,
  });
  // SUBMIT FORMS
  Future<AmbulanceCallDetails> finish({
    required int ambCallId,
    String? receiptComment,
    bool? advertised,
  });
  Future<AmbulanceCallDetails> reject(
    int ambCallId,
    Map<String, dynamic> fields,
  );
  Future<AmbulanceCallDetails> hospitalization(
    int ambCallId,
    Map<String, dynamic> fields,
    bool withTherapy,
  );
  Future<AmbulanceCallDetails> repeat(
    int ambCallId,
    Map<String, dynamic> fields,
  );
  // UPDATE CALLING DATA
  Future<AmbulanceCallDetails> update(
    int ambCallId,
    Map<String, dynamic> fields,
  );
}

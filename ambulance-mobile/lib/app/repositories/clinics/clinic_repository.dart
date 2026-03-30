import '../../api/dto/clinic.dart';

abstract class ClinicRepository {
  Future<List<Clinic>>  getClinicList();
}

import 'package:dio/dio.dart';
import '../../api/dto/clinic.dart';
import 'clinic_repository.dart';

class DefaultClinicRepository implements ClinicRepository {
  final Dio dio;

  DefaultClinicRepository({required this.dio});

  @override
  Future<List<Clinic>> getClinicList() async {
    final response = await dio.get('clinics');
    final data = response.data['items'] as List;

    return data.map((data) => Clinic.fromJson(data)).toList();
  }
}

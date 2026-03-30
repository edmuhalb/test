import 'package:dio/dio.dart';
import 'package:ambulance/app/repositories/services/services.dart';

import '../../api/dto/ambulance_call_service.dart';

class ServicesRepository implements AbstractServicesRepository {
  final Dio dio;

  ServicesRepository({required this.dio});

  @override
  Future<List<AmbulanceCallService>> getServiceList() async {
    final response = await dio.get('services');
    final data = response.data['items'] as List;

    return data.map((data) => AmbulanceCallService.fromJson(data)).toList();
  }
}

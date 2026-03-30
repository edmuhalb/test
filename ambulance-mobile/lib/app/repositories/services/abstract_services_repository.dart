import '../../api/dto/ambulance_call_service.dart';

abstract class AbstractServicesRepository {
  Future<List<AmbulanceCallService>> getServiceList();
}

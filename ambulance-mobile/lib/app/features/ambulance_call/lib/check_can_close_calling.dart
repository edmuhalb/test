import 'package:ambulance/app/repositories/ambulance_call/models/ambulance_call_details.dart';

bool checkCanCloseCalling(AmbulanceCallDetails calling) {
  final services = calling.services ?? [];
  final hasServices = services.isNotEmpty;
  final hasPatientName = calling.fio != null ? true : false;
  bool filledIn = false;

  for (var i = 0; i < services.length; i++) {
    final name = services[i]["service"]["name"];
    final price = services[i]["price"];

    if (name == null || price == null) {
      filledIn = false;
      break;
    }
    filledIn = true;
  }
  return !(filledIn && hasServices && hasPatientName);
}

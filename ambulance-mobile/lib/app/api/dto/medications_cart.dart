import 'package:ambulance/app/api/dto/medication_product.dart';
import 'package:json_annotation/json_annotation.dart';

@JsonSerializable()
class MedicationsCart {
  final List<MedicationCartOp> ops;
  final int totalCost;

  MedicationsCart({
    required this.ops,
    required this.totalCost,
  });
}

@JsonSerializable()
class MedicationCartOp {
  final MedicationProduct product;
  final String type;

  MedicationCartOp({
    required this.product,
    required this.type,
  });
}

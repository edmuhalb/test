import 'package:json_annotation/json_annotation.dart';

part 'medication_product.g.dart';

@JsonSerializable()
class MedicationProduct {
  final int id;
  final String name;
  final int categoryId;
  final double price;
  final int quantity;
  final List<MedicationProduct>? products;
  final String? unitLabel;

  const MedicationProduct({
    required this.id,
    required this.name,
    required this.categoryId,
    required this.price,
    required this.quantity,
    this.products,
    this.unitLabel,
  });

  factory MedicationProduct.fromJson(Map<String, dynamic> json) =>
      _$MedicationProductFromJson(json);
  Map<String, dynamic> toJson() => _$MedicationProductToJson(this);
}

extension MedicationProductExt on MedicationProduct {
  bool get isSet => products != null && products!.isNotEmpty;
}

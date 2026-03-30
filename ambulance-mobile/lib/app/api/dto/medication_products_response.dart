import 'package:ambulance/app/api/dto/medication_product.dart';
import 'package:json_annotation/json_annotation.dart';

part 'medication_products_response.g.dart';

@JsonSerializable()
class MedicationProductsResponse {
  final List<MedicationProduct> items;

  MedicationProductsResponse({required this.items});

  factory MedicationProductsResponse.fromJson(Map<String, dynamic> json) =>
      _$MedicationProductsResponseFromJson(json);
  Map<String, dynamic> toJson() => _$MedicationProductsResponseToJson(this);
}

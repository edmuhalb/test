import 'package:json_annotation/json_annotation.dart';
import 'entity.dart';

part 'medication_categories_response.g.dart';

@JsonSerializable()
class MedicationCategoriesResponse {
  final List<Entity> items;

  MedicationCategoriesResponse({required this.items});

  factory MedicationCategoriesResponse.fromJson(Map<String, dynamic> json) =>
      _$MedicationCategoriesResponseFromJson(json);
  Map<String, dynamic> toJson() => _$MedicationCategoriesResponseToJson(this);
}

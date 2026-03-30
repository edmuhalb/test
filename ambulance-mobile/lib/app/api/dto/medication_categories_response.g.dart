// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'medication_categories_response.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

MedicationCategoriesResponse _$MedicationCategoriesResponseFromJson(
        Map<String, dynamic> json) =>
    MedicationCategoriesResponse(
      items: (json['items'] as List<dynamic>)
          .map((e) => Entity.fromJson(e as Map<String, dynamic>))
          .toList(),
    );

Map<String, dynamic> _$MedicationCategoriesResponseToJson(
        MedicationCategoriesResponse instance) =>
    <String, dynamic>{
      'items': instance.items.map((e) => e.toJson()).toList(),
    };

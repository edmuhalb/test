import 'package:json_annotation/json_annotation.dart';

part 'entity.g.dart';

@JsonSerializable()
class Entity {
  final int id;
  final String name;

  Entity({
    required this.id,
    required this.name,
  });

  factory Entity.fromJson(Map<String, dynamic> json) =>
      _$EntityFromJson(json);
  Map<String, dynamic> toJson() => _$EntityToJson(this);
}
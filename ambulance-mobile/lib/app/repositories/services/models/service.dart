import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'service.g.dart';

@JsonSerializable()
class Service extends Equatable {
  final int id;
  final String name;
  final String type;

  const Service({
    required this.id,
    required this.name,
    required this.type,
  });

  @override
  List<Object?> get props => [id, name];

  factory Service.fromJson(Map<String, dynamic> json) => _$ServiceFromJson(json);

  Map<String, dynamic> toJson() => _$ServiceToJson(this);
}

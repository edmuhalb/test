import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'clinic.g.dart';

@JsonSerializable()
class Clinic extends Equatable {
  final int id;
  final String name;

  const Clinic({
    required this.id,
    required this.name,
  });

  factory Clinic.fromJson(Map<String, dynamic> json) =>
      _$ClinicFromJson(json);
  Map<String, dynamic> toJson() => _$ClinicToJson(this);
  
  @override
  List<Object?> get props => [id, name];
}
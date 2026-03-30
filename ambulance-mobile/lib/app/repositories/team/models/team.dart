import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'team.g.dart';

@JsonSerializable()
class Team extends Equatable {
  final int id;
  final String? plannedAt;
  final String plannedStartAt;
  final String plannedFinishAt;
  final String? startedAt;
  final String? completedAt;
  final String status;
  final Map<String, dynamic>? admin;
  final Map<String, dynamic>? doctor;
  final Map<String, dynamic>? phone;

  const Team({
    required this.id,
    required this.plannedStartAt,
    required this.plannedFinishAt,
    required this.plannedAt,
    required this.startedAt,
    required this.completedAt,
    required this.status,
    required this.admin,
    required this.doctor,
    required this.phone,
  });

  @override
  List<Object?> get props => [id, admin, doctor, plannedAt, startedAt, completedAt, plannedStartAt, plannedFinishAt, phone];

  factory Team.fromJson(Map<String, dynamic> json) => _$TeamFromJson(json);

  Map<String, dynamic> toJson() => _$TeamToJson(this);
}

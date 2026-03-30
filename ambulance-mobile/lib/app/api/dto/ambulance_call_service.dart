import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'ambulance_call_service.g.dart';

@JsonSerializable()
class AmbulanceCallService extends Equatable {
  final int id;
  final String name;

  const AmbulanceCallService({
    required this.id,
    required this.name,
  });

  factory AmbulanceCallService.fromJson(Map<String, dynamic> json) =>
      _$AmbulanceCallServiceFromJson(json);
  Map<String, dynamic> toJson() => _$AmbulanceCallServiceToJson(this);
  
  @override
  List<Object?> get props => [id, name];
}

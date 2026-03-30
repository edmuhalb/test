import 'package:json_annotation/json_annotation.dart';

part 'ambulance_call_client.g.dart';

@JsonSerializable()
class AmbulanceCallClient {
  final int id;
  final String? phone;
  final String? name;

  AmbulanceCallClient({
    required this.id,
    this.phone,
    this.name,
  });

  factory AmbulanceCallClient.fromJson(Map<String, dynamic> json) =>
      _$AmbulanceCallClientFromJson(json);
  Map<String, dynamic> toJson() => _$AmbulanceCallClientToJson(this);
}
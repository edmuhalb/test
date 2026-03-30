import 'package:json_annotation/json_annotation.dart';
import 'entity.dart';

part 'ambulance_call_list_response.g.dart';

@JsonSerializable()
class AmbulanceCallListResponse {
  final List<Entity> items;

  AmbulanceCallListResponse({required this.items});

  factory AmbulanceCallListResponse.fromJson(Map<String, dynamic> json) =>
      _$AmbulanceCallListResponseFromJson(json);
  Map<String, dynamic> toJson() => _$AmbulanceCallListResponseToJson(this);
}

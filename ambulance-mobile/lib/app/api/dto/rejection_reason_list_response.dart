import 'package:json_annotation/json_annotation.dart';
import 'entity.dart';

part 'rejection_reason_list_response.g.dart';

@JsonSerializable()
class RejectionReasonListResponse {
  final List<Entity> items;

  RejectionReasonListResponse({required this.items});

  factory RejectionReasonListResponse.fromJson(Map<String, dynamic> json) =>
      _$RejectionReasonListResponseFromJson(json);
  Map<String, dynamic> toJson() => _$RejectionReasonListResponseToJson(this);
}

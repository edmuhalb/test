
import 'package:json_annotation/json_annotation.dart';

part 'touch_to_call_request.g.dart';

@JsonSerializable()
class TouchToCallRequest {
  final int callId;

  TouchToCallRequest({required this.callId});

  factory TouchToCallRequest.fromJson(Map<String, dynamic> json) =>
      _$TouchToCallRequestFromJson(json);
  Map<String, dynamic> toJson() => _$TouchToCallRequestToJson(this);
}
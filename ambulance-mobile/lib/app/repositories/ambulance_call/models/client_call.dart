import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'client_call.g.dart';

@JsonSerializable()
class ClientCall extends Equatable {
  final int id;
  final String? status;
  final String? completedAt;
  final List<Map<String, dynamic>>? services;
  final int? price;
  final Map<String, dynamic>? client;

  const ClientCall({
    required this.id,
    required this.status,
    required this.completedAt,
    required this.services,
    required this.price,
    required this.client
  });

  @override
  List<Object?> get props => [id, status, completedAt, price, client];

  factory ClientCall.fromJson(Map<String, dynamic> json) => _$ClientCallFromJson(json);

  Map<String, dynamic> toJson() => _$ClientCallToJson(this);
}

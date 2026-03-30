import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'ambulance_call.g.dart';

@JsonSerializable()
class AmbulanceCall extends Equatable {
  final int id;
  final String? title;
  final String? name;
  final String? phone;
  final String? address;
  final String? createdAt;
  final String? dateTime;
  final String? status;
  final String? statusLabel;

  const AmbulanceCall({
    required this.id,
    required this.title,
    required this.name,
    required this.phone,
    required this.address,
    required this.createdAt,
    required this.dateTime,
    required this.status,
    required this.statusLabel,
  });

  @override
  List<Object?> get props => [
        id,
        title,
        name,
        phone,
        address,
        createdAt,
        dateTime,
        status,
        statusLabel
      ];

  factory AmbulanceCall.fromJson(Map<String, dynamic> json) =>
      _$AmbulanceCallFromJson(json);

  Map<String, dynamic> toJson() => _$AmbulanceCallToJson(this);
}

class AmbulanceCallStatus {
  static const String assigned = "assigned"; // назначен
  static const String accepted = "accepted"; // принят
  static const String departed = "dispatched"; // выехали
  static const String arrived = "arrived"; // прибыли
  static const String treating = "treating";
  static const String completed = "completed"; // завершили
  static const String rejected = "rejected"; // отклонен

  AmbulanceCallStatus._();
}

extension AmbulanceCallExtenstion on AmbulanceCall {
  bool isAssigned() => status == AmbulanceCallStatus.assigned;
  bool isAccepted() => status == AmbulanceCallStatus.accepted;
  bool isDeparted() => status == AmbulanceCallStatus.departed;
  bool isArrived() => status == AmbulanceCallStatus.arrived;
  bool isTreating() => status == AmbulanceCallStatus.treating;
  bool isCompleted() => status == AmbulanceCallStatus.completed;
  bool isRejected() => status == AmbulanceCallStatus.rejected;
}

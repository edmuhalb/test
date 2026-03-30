import 'package:json_annotation/json_annotation.dart';

part 'ambulance_call_list_item.g.dart';

@JsonSerializable()
class AmbulanceCallListItem {
  final int id;
  final String title;
  final String name;
  final String phone;
  final String address;
  final String createdAt;
  final String? dateTime;
  final String status;
  final String statusLabel;

  AmbulanceCallListItem({
    required this.id,
    required this.title,
    required this.name,
    required this.phone,
    required this.address,
    required this.createdAt,
    this.dateTime,
    required this.status,
    required this.statusLabel,
  });

  factory AmbulanceCallListItem.fromJson(Map<String, dynamic> json) =>
      _$AmbulanceCallListItemFromJson(json);
  Map<String, dynamic> toJson() => _$AmbulanceCallListItemToJson(this);
}
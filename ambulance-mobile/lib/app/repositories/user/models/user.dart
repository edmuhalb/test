import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'user.g.dart';

@JsonSerializable()
class User extends Equatable {
  final int id;
  final String name;
  final String? position;
  final String phone;

  const User({
    required this.id,
    this.position,
    required this.name,
    required this.phone,
  });

  @override
  List<Object?> get props =>
      [id, name, phone, position];

  factory User.fromJson(Map<String, dynamic> json) => _$UserFromJson(json);

  Map<String, dynamic> toJson() => _$UserToJson(this);
}

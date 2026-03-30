import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'version.g.dart';

@JsonSerializable()
class Version extends Equatable {
  final String target;
  final String min;

  const Version({
    required this.target,
    required this.min,
  });

  @override
  List<Object?> get props => [target, min];

  factory Version.fromJson(Map<String, dynamic> json) => _$VersionFromJson(json);

  Map<String, dynamic> toJson() => _$VersionToJson(this);
}

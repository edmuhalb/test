import 'package:equatable/equatable.dart';
import 'package:json_annotation/json_annotation.dart';

part 'remote_file.g.dart';

@JsonSerializable()
class RemoteFile extends Equatable {
  final int id;
  final String? url;

  const RemoteFile({required this.id, this.url});

  factory RemoteFile.fromJson(Map<String, dynamic> json) =>
      _$RemoteFileFromJson(json);
  Map<String, dynamic> toJson() => _$RemoteFileToJson(this);

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is RemoteFile && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;
  
  @override
  List<Object?> get props => [id, url];
}

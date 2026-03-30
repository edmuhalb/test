// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'remote_file.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

RemoteFile _$RemoteFileFromJson(Map<String, dynamic> json) => RemoteFile(
      id: (json['id'] as num).toInt(),
      url: json['url'] as String?,
    );

Map<String, dynamic> _$RemoteFileToJson(RemoteFile instance) =>
    <String, dynamic>{
      'id': instance.id,
      'url': instance.url,
    };

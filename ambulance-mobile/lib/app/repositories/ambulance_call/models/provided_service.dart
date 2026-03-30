import 'package:json_annotation/json_annotation.dart';

part 'provided_service.g.dart';

@JsonSerializable()
class ProvidedService {
  final String name;
  final int price;

  const ProvidedService({
    required this.name,
    required this.price,
  });

  factory ProvidedService.fromJson(Map<String, dynamic> json) =>
      _$ProvidedServiceFromJson(json);

  Map<String, dynamic> toJson() => _$ProvidedServiceToJson(this);
}

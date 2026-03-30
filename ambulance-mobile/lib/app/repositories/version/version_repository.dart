import 'package:dio/dio.dart';
import 'package:ambulance/app/repositories/version/version.dart';

class VersionRepository implements AbstractVersionRepository {
  final Dio dio;

  VersionRepository({required this.dio});

  @override
  getAppVersions() async {
    final response = await dio.get('version');

    final data = response.data as Map<String, dynamic>;

    return Version.fromJson(data);
  }
}

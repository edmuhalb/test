import 'package:ambulance/app/repositories/version/version.dart';

abstract class AbstractVersionRepository {
  // TODO: refactor to return VersionInfo
  Future<Version> getAppVersions();
}

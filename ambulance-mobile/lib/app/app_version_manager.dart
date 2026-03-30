import 'package:get_it/get_it.dart';
import 'package:package_info_plus/package_info_plus.dart';

import 'repositories/version/abstract_version_repository.dart';

class AppVersionManager {
  AppVersionManager._();

  static AppVersion get currentVersion => AppVersion.fromString(
        GetIt.I<PackageInfo>().version,
      );

  static Future<VersionInfo> fetchVersionInfo() async {
    final version = await GetIt.I<AbstractVersionRepository>().getAppVersions();
    return VersionInfo(
      min: AppVersion.fromString(version.min),
      target: AppVersion.fromString(version.target),
    );
  }
}

class AppVersion implements Comparable<AppVersion> {
  final int major;
  final int minor;
  final int patch;

  AppVersion({
    required this.major,
    required this.minor,
    required this.patch,
  });

  factory AppVersion.fromString(String version) {
    final parts = version.split('.');
    if (parts.length != 3) {
      throw FormatException(
          'Invalid version format. Expected "major.minor.patch".');
    }

    try {
      final major = int.parse(parts[0]);
      final minor = int.parse(parts[1]);
      final patch = int.parse(parts[2]);
      return AppVersion(major: major, minor: minor, patch: patch);
    } catch (e) {
      throw FormatException(
          'Invalid version format. Version components must be integers.');
    }
  }

  @override
  int compareTo(AppVersion other) {
    if (major != other.major) {
      return major.compareTo(other.major);
    }
    if (minor != other.minor) {
      return minor.compareTo(other.minor);
    }
    return patch.compareTo(other.patch);
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    if (other is! AppVersion) return false;
    return major == other.major && minor == other.minor && patch == other.patch;
  }

  @override
  int get hashCode => Object.hash(major, minor, patch);

  bool operator <(AppVersion other) => compareTo(other) < 0;
  bool operator <=(AppVersion other) => compareTo(other) <= 0;
  bool operator >(AppVersion other) => compareTo(other) > 0;
  bool operator >=(AppVersion other) => compareTo(other) >= 0;

  @override
  String toString() => '$major.$minor.$patch';
}

class VersionInfo {
  final AppVersion min;
  final AppVersion target;

  VersionInfo({
    required this.min,
    required this.target,
  });

  bool isNotCompatible(AppVersion version) => !isCompatible(version);
  bool isCompatible(AppVersion version) => version >= min;
  bool canBeUpdated(AppVersion version) => version < target;

  @override
  String toString() => 'min: $min, target: $target';
}

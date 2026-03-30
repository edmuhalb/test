import 'package:shared_preferences/shared_preferences.dart';

class AppPreferences {
  final SharedPreferences sharedPreferences;

  AppPreferences(this.sharedPreferences);

  bool get authenticated => authToken != null;

  String? get authToken => sharedPreferences.getString(_Keys.authToken);

  set authToken(String? value) => value != null
      ? sharedPreferences.setString(_Keys.authToken, value)
      : removeAuthToken();

  void removeAuthToken() => _remove(_Keys.authToken);

  int? get teamId => sharedPreferences.getInt(_Keys.teamId);

  set teamId(int? value) => value != null
      ? sharedPreferences.setInt(_Keys.teamId, value)
      : _remove(_Keys.teamId);

  void _remove(String key) => sharedPreferences.remove(key);

  void clear() => sharedPreferences.clear();
}

class _Keys {
  static const authToken = 'auth_token';
  static const teamId = 'team_id';
}

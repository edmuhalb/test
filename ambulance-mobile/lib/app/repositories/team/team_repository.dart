import 'package:ambulance/app/repositories/team/team.dart';

abstract class TeamRepository {
  Future<List<Team>> getTeamList(Map<String, dynamic>? params);
  Future<Team> update(int teamId, Map<String, dynamic> fields);

  Future<Team?> accept();
  Future<Team?> complete();
}

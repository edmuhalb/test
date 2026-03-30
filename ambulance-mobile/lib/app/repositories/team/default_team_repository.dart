import 'package:dio/dio.dart';
import 'package:ambulance/app/repositories/team/team.dart';

class DefaultTeamRepository implements TeamRepository {
  final Dio dio;

  DefaultTeamRepository({required this.dio});

  @override
  Future<List<Team>> getTeamList(params) async {
    final response = await dio.get('med_teams', queryParameters: params);
    return (response.data as List).map((data) => Team.fromJson(data)).toList();
  }

  @override
  Future<Team> update(int teamId, Map<String, dynamic> fields) async {
    final response = await dio.patch('med_teams/$teamId',
        data: fields,
        options: Options(
          headers: {
            "Content-Type": "application/merge-patch+json",
          },
        ));
    final data = response.data as Map<String, dynamic>;
    return Team.fromJson(data);
  }

  @override
  Future<Team?> accept() async {
    final data = {
      'admin': {'id': 13, 'name': 'admin name'},
      'doctor': {'id': 13, 'name': 'doctor name'},
      'id': 37,
      'external': 1,
      'status': 'work',
      'plannedAt': '2023-12-18T00:00:00+03:00',
      'startedAt': '2023-12-18T00:00:00+03:00',
      'name': 'name',
    };
    return Team.fromJson(data);
  }

  @override
  Future<Team?> complete() async {
    final data = {
      'admin': {'id': 13, 'name': 'admin name'},
      'doctor': {'id': 13, 'name': 'doctor name'},
      'id': 37,
      'external': 1,
      'status': 'completed',
      'plannedAt': '2023-12-18T00:00:00+03:00',
      'startedAt': '2023-12-18T00:00:00+03:00',
      'name': 'name',
    };
    return Team.fromJson(data);
  }
}

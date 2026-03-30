part of 'team_list_bloc.dart';

abstract class TeamListEvent extends Equatable {}

class LoadTeamList extends TeamListEvent {
  final Completer? completer;

  LoadTeamList({this.completer});

  @override
  List<Object?> get props => [completer];
}

class StartWork extends TeamListEvent {
  final Completer? completer;
  final int teamid;

  StartWork({
    this.completer,
    required this.teamid,
  });

  @override
  List<Object?> get props => [completer];
}

class Completed extends TeamListEvent {
  final Completer? completer;
  final int teamid;
  final Map<String, dynamic> fields;

  Completed({
    this.completer,
    required this.teamid,
    required this.fields,
  });

  @override
  List<Object?> get props => [completer];
}

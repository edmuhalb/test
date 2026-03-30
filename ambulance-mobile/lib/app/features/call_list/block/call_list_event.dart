part of 'call_list_bloc.dart';

abstract class CallListEvent extends Equatable {}

class LoadCallList extends CallListEvent {
  final Completer? completer;

  LoadCallList({this.completer});

  @override
  List<Object?> get props => [completer];
}

class StartShift extends CallListEvent {
  final Completer? completer;
  final Team team;

  StartShift({
    this.completer,
    required this.team,
  });

  @override
  List<Object?> get props => [completer, team];
}

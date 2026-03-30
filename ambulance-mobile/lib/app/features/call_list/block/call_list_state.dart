part of 'call_list_bloc.dart';

abstract class CallListState extends Equatable {}

class CallListInitial extends CallListState {
  @override
  List<Object?> get props => [];
}

class CallListLoading extends CallListState {
  @override
  List<Object?> get props => [];
}

class CallListLoaded extends CallListState {
  final List<AmbulanceCall> callsList;
  final Team? team;

  CallListLoaded({
    required this.callsList,
    required this.team,
  });

  @override
  List<Object?> get props => [callsList, team];
}

class CallListLoadedFailure extends CallListState {
  final DioException? exception;

  CallListLoadedFailure({this.exception});

  @override
  List<Object?> get props => [exception];
}

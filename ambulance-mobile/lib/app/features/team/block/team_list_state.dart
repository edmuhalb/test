part of 'team_list_bloc.dart';

abstract class TeamListState extends Equatable {}

class TeamListInitial extends TeamListState {
  @override
  List<Object?> get props => [];
}

class TeamListLoading extends TeamListState {
  @override
  List<Object?> get props => [];
}

class TeamListLoaded extends TeamListState {
  final Team? teamList;

  TeamListLoaded({required this.teamList});

  @override
  List<Object?> get props => [teamList];
}

class TeamListLoadedFailure extends TeamListState {
  final DioException? exception;

  TeamListLoadedFailure({this.exception});

  @override
  List<Object?> get props => [exception];
}
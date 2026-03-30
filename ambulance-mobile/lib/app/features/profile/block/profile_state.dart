part of 'profile_bloc.dart';

abstract class ProfileState extends Equatable {}

class ProfileInitial extends ProfileState {
  @override
  List<Object?> get props => [];
}

class ProfileLoading extends ProfileState {
  @override
  List<Object?> get props => [];
}

class ProfileLoaded extends ProfileState {
  final User user;

  ProfileLoaded({required this.user});

  @override
  List<Object?> get props => [user];
}

class ProfileLoadFailed extends ProfileState {
  final Object? exception;

  ProfileLoadFailed({this.exception});

  @override
  List<Object?> get props => [exception];
}
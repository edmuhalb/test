part of 'profile_bloc.dart';

abstract class ProfileEvent extends Equatable {}

class LoadProfile extends ProfileEvent {
  final Completer? completer;

  LoadProfile({this.completer});

  @override
  List<Object?> get props => [completer];
}

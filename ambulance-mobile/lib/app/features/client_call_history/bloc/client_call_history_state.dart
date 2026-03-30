part of 'client_call_history_bloc.dart';

abstract class ClientCallHistoryState extends Equatable {}

class ClientCallHistoryInitial extends ClientCallHistoryState {
  @override
  List<Object?> get props => [];
}

class ClientCallHistoryLoadingState extends ClientCallHistoryState {
  @override
  List<Object?> get props => [];
}

class ClientCallHistoryLoadedState extends ClientCallHistoryState {
  final List<ClientCall> clientCalls;

  ClientCallHistoryLoadedState({required this.clientCalls});

  @override
  List<Object?> get props => [clientCalls];
}

class ClientCallHistoryLoadFailedState extends ClientCallHistoryState {
  final DioException? exception;

  ClientCallHistoryLoadFailedState({this.exception});

  @override
  List<Object?> get props => [exception];
}

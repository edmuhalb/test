part of 'client_call_history_bloc.dart';

abstract class ClientCallHistoryEvent extends Equatable {}

class LoadClientCallHistory extends ClientCallHistoryEvent {
  final Completer? completer;
  final int callId;
  final int clientId;

  LoadClientCallHistory({
    this.completer,
    required this.callId,
    required this.clientId,
  });

  @override
  List<Object?> get props => [completer];
}

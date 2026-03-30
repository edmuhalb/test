import 'dart:async';

import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:dio/dio.dart';
import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:talker_flutter/talker_flutter.dart';

part 'client_call_history_event.dart';
part 'client_call_history_state.dart';

class ClientCallHistoryBloc extends Bloc<ClientCallHistoryEvent, ClientCallHistoryState> {
  final AmbulanceCallRepository callsRepository;

  ClientCallHistoryBloc(this.callsRepository) : super(ClientCallHistoryInitial()) {
    on<LoadClientCallHistory>((event, emit) async {
      try {
        if (state is! ClientCallHistoryLoadedState) {
          emit(ClientCallHistoryLoadingState());
        }
        final clientCalls = await callsRepository.getCallsListByClient(
          event.clientId,
          event.callId,
        );

        emit(ClientCallHistoryLoadedState(clientCalls: clientCalls));
      } catch (e, st) {
        emit(ClientCallHistoryLoadFailedState(exception: e as DioException));
        GetIt.I<Talker>().handle(e, st);
      } finally {
        event.completer?.complete();
      }
    });
  }
}

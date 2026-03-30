import 'dart:async';

import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:ambulance/app/repositories/team/team.dart';
import 'package:dio/dio.dart';
import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:intl/intl.dart';
import 'package:jwt_decoder/jwt_decoder.dart';
import 'package:talker_flutter/talker_flutter.dart';

import '../../../app_preferences.dart';

part 'call_list_event.dart';
part 'call_list_state.dart';

class CallListBloc extends Bloc<CallListEvent, CallListState> {
  final AmbulanceCallRepository callsRepository;
  final TeamRepository teamRepository;

  CallListBloc(this.callsRepository, this.teamRepository)
      : super(CallListInitial()) {
    on<LoadCallList>((event, emit) async {
      final appPreferences = GetIt.I<AppPreferences>();
      final token = appPreferences.authToken;

      DateFormat dateFormat = DateFormat("yyyy-MM-ddTHH:mm:ss+03:00");
      DateTime now = DateTime.now();
      final beginningOfYesterday = DateTime(now.year, now.month, now.day)
          .subtract(const Duration(days: 1));
      final endOfToday = DateTime(now.year, now.month, now.day, 23, 59, 59);

      try {
        emit(CallListLoading());
        if (token != null) {
          final Map<String, dynamic> user = JwtDecoder.decode(token);
          final currentUserId = user["id"];

          final teams = await teamRepository.getTeamList({
            'admin.id': currentUserId,
            'pagination': false,
            'plannedStartAt[after]': dateFormat.format(beginningOfYesterday),
            'plannedStartAt[before]': dateFormat.format(endOfToday),
            'order[plannedStartAt]': "asc"
          });

          final workList = teams.where((team) => team.status == 'work');

          if (workList.isNotEmpty) {
            final callsList = await callsRepository.getCallList(currentUserId);
            emit(CallListLoaded(
              callsList: callsList,
              team: workList.first,
            ));

            appPreferences.teamId = workList.first.id;
            return;
          }

          final scheduledList =
              teams.where((team) => team.status == 'scheduled');

          if (scheduledList.isNotEmpty) {
            final callsList = await callsRepository.getCallList(currentUserId);
            emit(CallListLoaded(
              callsList: callsList,
              team: scheduledList.first,
            ));

            appPreferences.teamId = scheduledList.first.id;
            return;
          }

          emit(CallListLoaded(
            callsList: const [],
            team: null,
          ));
        }
      } catch (e, st) {
        emit(CallListLoadedFailure(exception: e as DioException));
        GetIt.I<Talker>().handle(e, st);
      } finally {
        event.completer?.complete();
      }
    });

    on<StartShift>((event, emit) async {
      try {
        emit(CallListLoading());

        await teamRepository.update(event.team.id, {
          'status': 'work',
          'startedAt': DateTime.now().toIso8601String(),
        });
        add(LoadCallList());
      } catch (e, st) {
        emit(CallListLoadedFailure(exception: e as DioException));
        GetIt.I<Talker>().handle(e, st);
      } finally {
        event.completer?.complete();
      }
    });
  }
}

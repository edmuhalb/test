import 'dart:async';

import 'package:ambulance/app/repositories/team/team.dart';
import 'package:dio/dio.dart';
import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:intl/intl.dart';
import 'package:jwt_decoder/jwt_decoder.dart';
import 'package:talker_flutter/talker_flutter.dart';

import '../../../app_preferences.dart';

part 'team_list_event.dart';
part 'team_list_state.dart';

class TeamListBloc extends Bloc<TeamListEvent, TeamListState> {
  final TeamRepository teamRepository;

  TeamListBloc(this.teamRepository) : super(TeamListInitial()) {
    on<LoadTeamList>((event, emit) async {
      try {
        emit(TeamListLoading());

        final appPreferences = GetIt.I<AppPreferences>();
        final token = appPreferences.authToken;

        if (token != null) {
          final Map<String, dynamic> user = JwtDecoder.decode(token);
          final currentUserId = user["id"];

          DateFormat dateFormat = DateFormat("yyyy-MM-ddTHH:mm:ss+03:00");
          DateTime now = DateTime.now();
          final beginningOfYesterday = DateTime(now.year, now.month, now.day)
              .subtract(const Duration(days: 1));
          final endOfToday = DateTime(now.year, now.month, now.day, 23, 59, 59);

          final teams = await teamRepository.getTeamList({
            'admin.id': currentUserId,
            'pagination': false,
            'plannedStartAt[after]': dateFormat.format(beginningOfYesterday),
            'plannedStartAt[before]': dateFormat.format(endOfToday),
          });

          final workTeamList =
              teams.where((element) => element.status == "work");

          if (workTeamList.isNotEmpty) {
            emit(TeamListLoaded(teamList: workTeamList.first));
            return;
          }

          final scheduledTeamList =
              teams.where((element) => element.status == "scheduled");

          if (scheduledTeamList.isNotEmpty) {
            emit(TeamListLoaded(teamList: scheduledTeamList.first));
            return;
          }

          emit(TeamListLoaded(teamList: null));
        } else {
          emit(TeamListLoaded(teamList: null));
        }
      } catch (e, st) {
        emit(TeamListLoadedFailure(exception: e as DioException));
        GetIt.I<Talker>().handle(e, st);
      } finally {
        event.completer?.complete();
      }
    });

    on<StartWork>((event, emit) async {
      try {
        final updatedteam = await teamRepository.update(event.teamid, {
          'status': 'work',
          'startedAt': DateTime.now().toIso8601String(),
        });
        emit(TeamListLoaded(teamList: updatedteam));
      } catch (e, st) {
        emit(TeamListLoadedFailure(exception: e as DioException));
        GetIt.I<Talker>().handle(e, st);
      } finally {
        event.completer?.complete();
      }
    });

    on<Completed>((event, emit) async {
      emit(TeamListLoading());
      try {
        final updatedteam = await teamRepository.update(event.teamid, {
          'status': 'completed',
          'completedAt': DateTime.now().toIso8601String(),
          ...event.fields
        });
        emit(TeamListLoaded(teamList: updatedteam));
      } catch (e, st) {
        emit(TeamListLoadedFailure(exception: e as DioException));
        GetIt.I<Talker>().handle(e, st);
      } finally {
        event.completer?.complete();
      }
    });
  }
}

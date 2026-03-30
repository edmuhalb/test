import 'dart:async';

import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:talker_flutter/talker_flutter.dart';
import 'package:jwt_decoder/jwt_decoder.dart';
import 'package:ambulance/app/repositories/user/user.dart';

import '../../../app_preferences.dart';

part 'profile_event.dart';
part 'profile_state.dart';

class ProfileBloc extends Bloc<ProfileEvent, ProfileState> {
  final AbstractUserRepository profileRepository;

  ProfileBloc(this.profileRepository) : super(ProfileInitial()) {
    on<LoadProfile>((event, emit) async {
      try {
        if (state is! ProfileLoaded) {
          emit(ProfileLoading());
        }
        final appPreferences = GetIt.I<AppPreferences>();
        final token = appPreferences.authToken;

        if (token != null) {
          Map<String, dynamic> userData = JwtDecoder.decode(token);

          final user =
              await profileRepository.getUser(userData["id"].toString());
          emit(ProfileLoaded(user: user));
        }
      } catch (e, st) {
        emit(ProfileLoadFailed(exception: e));
        GetIt.I<Talker>().handle(e, st);
      } finally {
        event.completer?.complete();
      }
    });
  }
}

// dart format width=80
// GENERATED CODE - DO NOT MODIFY BY HAND

// **************************************************************************
// AutoRouterGenerator
// **************************************************************************

// ignore_for_file: type=lint
// coverage:ignore-file

// ignore_for_file: no_leading_underscores_for_library_prefixes
import 'package:ambulance/app/app_version_manager.dart' as _i13;
import 'package:ambulance/app/features/ambulance_call/view/ambulance_call_detail_screen.dart'
    as _i1;
import 'package:ambulance/app/features/call_list/view/call_list_screen.dart'
    as _i2;
import 'package:ambulance/app/features/client_call_history/view/client_call_history_screen.dart'
    as _i3;
import 'package:ambulance/app/features/medications_selection/view/medications_selection_screen.dart'
    as _i4;
import 'package:ambulance/app/features/profile/view/profile_screen.dart' as _i5;
import 'package:ambulance/app/features/sign_in/sign_in_screen.dart' as _i6;
import 'package:ambulance/app/features/team/view/team_screen.dart' as _i7;
import 'package:ambulance/app/features/version/version_update_requirement_screen.dart'
    as _i8;
import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart'
    as _i11;
import 'package:auto_route/auto_route.dart' as _i9;
import 'package:flutter/foundation.dart' as _i10;
import 'package:flutter/material.dart' as _i12;

/// generated route for
/// [_i1.AmbulanceCallDetailScreen]
class AmbulanceCallDetailRoute
    extends _i9.PageRouteInfo<AmbulanceCallDetailRouteArgs> {
  AmbulanceCallDetailRoute({
    _i10.Key? key,
    required _i11.AmbulanceCall call,
    required List<_i11.ClientCall> clientCalls,
    List<_i9.PageRouteInfo>? children,
  }) : super(
         AmbulanceCallDetailRoute.name,
         args: AmbulanceCallDetailRouteArgs(
           key: key,
           call: call,
           clientCalls: clientCalls,
         ),
         initialChildren: children,
       );

  static const String name = 'AmbulanceCallDetailRoute';

  static _i9.PageInfo page = _i9.PageInfo(
    name,
    builder: (data) {
      final args = data.argsAs<AmbulanceCallDetailRouteArgs>();
      return _i1.AmbulanceCallDetailScreen(
        key: args.key,
        call: args.call,
        clientCalls: args.clientCalls,
      );
    },
  );
}

class AmbulanceCallDetailRouteArgs {
  const AmbulanceCallDetailRouteArgs({
    this.key,
    required this.call,
    required this.clientCalls,
  });

  final _i10.Key? key;

  final _i11.AmbulanceCall call;

  final List<_i11.ClientCall> clientCalls;

  @override
  String toString() {
    return 'AmbulanceCallDetailRouteArgs{key: $key, call: $call, clientCalls: $clientCalls}';
  }
}

/// generated route for
/// [_i2.CallsListScreen]
class CallsListRoute extends _i9.PageRouteInfo<void> {
  const CallsListRoute({List<_i9.PageRouteInfo>? children})
    : super(CallsListRoute.name, initialChildren: children);

  static const String name = 'CallsListRoute';

  static _i9.PageInfo page = _i9.PageInfo(
    name,
    builder: (data) {
      return const _i2.CallsListScreen();
    },
  );
}

/// generated route for
/// [_i3.ClientCallHistoryScreen]
class ClientCallHistoryRoute
    extends _i9.PageRouteInfo<ClientCallHistoryRouteArgs> {
  ClientCallHistoryRoute({
    _i12.Key? key,
    required int clientId,
    required int ambCallId,
    List<_i9.PageRouteInfo>? children,
  }) : super(
         ClientCallHistoryRoute.name,
         args: ClientCallHistoryRouteArgs(
           key: key,
           clientId: clientId,
           ambCallId: ambCallId,
         ),
         initialChildren: children,
       );

  static const String name = 'ClientCallHistoryRoute';

  static _i9.PageInfo page = _i9.PageInfo(
    name,
    builder: (data) {
      final args = data.argsAs<ClientCallHistoryRouteArgs>();
      return _i3.ClientCallHistoryScreen(
        key: args.key,
        clientId: args.clientId,
        ambCallId: args.ambCallId,
      );
    },
  );
}

class ClientCallHistoryRouteArgs {
  const ClientCallHistoryRouteArgs({
    this.key,
    required this.clientId,
    required this.ambCallId,
  });

  final _i12.Key? key;

  final int clientId;

  final int ambCallId;

  @override
  String toString() {
    return 'ClientCallHistoryRouteArgs{key: $key, clientId: $clientId, ambCallId: $ambCallId}';
  }
}

/// generated route for
/// [_i4.MedicationsSelectionScreen]
class MedicationsSelectionRoute extends _i9.PageRouteInfo<void> {
  const MedicationsSelectionRoute({List<_i9.PageRouteInfo>? children})
    : super(MedicationsSelectionRoute.name, initialChildren: children);

  static const String name = 'MedicationsSelectionRoute';

  static _i9.PageInfo page = _i9.PageInfo(
    name,
    builder: (data) {
      return const _i4.MedicationsSelectionScreen();
    },
  );
}

/// generated route for
/// [_i5.ProfileScreen]
class ProfileRoute extends _i9.PageRouteInfo<void> {
  const ProfileRoute({List<_i9.PageRouteInfo>? children})
    : super(ProfileRoute.name, initialChildren: children);

  static const String name = 'ProfileRoute';

  static _i9.PageInfo page = _i9.PageInfo(
    name,
    builder: (data) {
      return const _i5.ProfileScreen();
    },
  );
}

/// generated route for
/// [_i6.SignInScreen]
class SignInRoute extends _i9.PageRouteInfo<SignInRouteArgs> {
  SignInRoute({
    _i12.Key? key,
    required void Function() onSignedIn,
    List<_i9.PageRouteInfo>? children,
  }) : super(
         SignInRoute.name,
         args: SignInRouteArgs(key: key, onSignedIn: onSignedIn),
         initialChildren: children,
       );

  static const String name = 'SignInRoute';

  static _i9.PageInfo page = _i9.PageInfo(
    name,
    builder: (data) {
      final args = data.argsAs<SignInRouteArgs>();
      return _i6.SignInScreen(key: args.key, onSignedIn: args.onSignedIn);
    },
  );
}

class SignInRouteArgs {
  const SignInRouteArgs({this.key, required this.onSignedIn});

  final _i12.Key? key;

  final void Function() onSignedIn;

  @override
  String toString() {
    return 'SignInRouteArgs{key: $key, onSignedIn: $onSignedIn}';
  }
}

/// generated route for
/// [_i7.TeamScreen]
class TeamRoute extends _i9.PageRouteInfo<TeamRouteArgs> {
  TeamRoute({
    _i12.Key? key,
    bool redirectToCallListOnShiftStarted = false,
    List<_i9.PageRouteInfo>? children,
  }) : super(
         TeamRoute.name,
         args: TeamRouteArgs(
           key: key,
           redirectToCallListOnShiftStarted: redirectToCallListOnShiftStarted,
         ),
         initialChildren: children,
       );

  static const String name = 'TeamRoute';

  static _i9.PageInfo page = _i9.PageInfo(
    name,
    builder: (data) {
      final args = data.argsAs<TeamRouteArgs>(
        orElse: () => const TeamRouteArgs(),
      );
      return _i7.TeamScreen(
        key: args.key,
        redirectToCallListOnShiftStarted: args.redirectToCallListOnShiftStarted,
      );
    },
  );
}

class TeamRouteArgs {
  const TeamRouteArgs({
    this.key,
    this.redirectToCallListOnShiftStarted = false,
  });

  final _i12.Key? key;

  final bool redirectToCallListOnShiftStarted;

  @override
  String toString() {
    return 'TeamRouteArgs{key: $key, redirectToCallListOnShiftStarted: $redirectToCallListOnShiftStarted}';
  }
}

/// generated route for
/// [_i8.VersionUpdateRequirementScreen]
class VersionUpdateRequirementRoute
    extends _i9.PageRouteInfo<VersionUpdateRequirementRouteArgs> {
  VersionUpdateRequirementRoute({
    _i12.Key? key,
    required _i13.VersionInfo versionInfo,
    List<_i9.PageRouteInfo>? children,
  }) : super(
         VersionUpdateRequirementRoute.name,
         args: VersionUpdateRequirementRouteArgs(
           key: key,
           versionInfo: versionInfo,
         ),
         initialChildren: children,
       );

  static const String name = 'VersionUpdateRequirementRoute';

  static _i9.PageInfo page = _i9.PageInfo(
    name,
    builder: (data) {
      final args = data.argsAs<VersionUpdateRequirementRouteArgs>();
      return _i8.VersionUpdateRequirementScreen(
        key: args.key,
        versionInfo: args.versionInfo,
      );
    },
  );
}

class VersionUpdateRequirementRouteArgs {
  const VersionUpdateRequirementRouteArgs({
    this.key,
    required this.versionInfo,
  });

  final _i12.Key? key;

  final _i13.VersionInfo versionInfo;

  @override
  String toString() {
    return 'VersionUpdateRequirementRouteArgs{key: $key, versionInfo: $versionInfo}';
  }
}

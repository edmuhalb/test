import 'dart:async';

import 'package:ambulance/app/features/call_list/block/call_list_bloc.dart';
import 'package:ambulance/app/features/call_list/widgets/widgets.dart';
import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:ambulance/app/repositories/team/team.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/utils/date_time_utils.dart';
import 'package:ambulance/app/widgets/scaffold/scaffold_body.dart';
import 'package:ambulance/app/widgets/widgets.dart';
import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:ambulance/app/ambulance_app.dart';

import '../../../router/router.gr.dart';

@RoutePage()
class CallsListScreen extends StatefulWidget {
  const CallsListScreen({super.key});

  @override
  State<CallsListScreen> createState() => _CallsListScreenState();
}

class _CallsListScreenState extends State<CallsListScreen> with RouteAware {
  bool _pulledToRefresh = false;
  final _callListBloc = CallListBloc(
    GetIt.I<AmbulanceCallRepository>(),
    GetIt.I<TeamRepository>(),
  );

  @override
  void initState() {
    super.initState();
    _callListBloc.add(LoadCallList());
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    routeObserver.subscribe(this, ModalRoute.of(context)!);
  }

  @override
  void didPopNext() {
    // Маршрут покрытия был удален из навигатора. (тап по кнопке назад)
    _callListBloc.add(LoadCallList());
  }

  @override
  void dispose() {
    routeObserver.unsubscribe(this);
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(AppPhrases.ambulanceCalls),
      ),
      endDrawer: const AppDrawer(),
      body: ScaffoldBody(
        child: RefreshIndicator(
          color: AppColors.primary,
          backgroundColor: AppColors.card,
          onRefresh: _onPullToRefresh,
          child: BlocBuilder<CallListBloc, CallListState>(
            bloc: _callListBloc,
            builder: (context, state) {
              if (state is CallListLoaded &&
                  state.team != null &&
                  state.team!.status == 'scheduled') {
                _redirectToAmbulanceShift(
                  redirectToCallListOnShiftStarted: true,
                );
                return _buildProgressIndicator();
              }

              if (state is CallListLoaded &&
                  state.team != null &&
                  state.team!.status == 'work') {
                return _buildCallList(
                  // TODO: optimize
                  state.callsList.where((x) => x.dateTime != null).toList(),
                );
              }

              if (state is CallListLoaded) {
                return _buildTeamIsNotCreated();
              }

              if (state is CallListLoadedFailure) {
                return ErrorView(
                  errorMessage: state.exception?.message,
                  onTryAgain: () => _callListBloc.add(LoadCallList()),
                );
              }

              return _pulledToRefresh ? Container() : _buildProgressIndicator();
            },
          ),
        ),
      ),
    );
  }

  Center _buildProgressIndicator() {
    return const Center(
      child: CircularProgressIndicator(),
    );
  }

  CustomScrollView _buildTeamIsNotCreated() {
    return const CustomScrollView(
      slivers: <Widget>[
        SliverFillRemaining(
          child: Center(
            child: Text(AppPhrases.teamIsNotCreated),
          ),
        )
      ],
    );
  }

  ListView _buildCallList(List<AmbulanceCall> ambCalls) {
    String? curSectionHeader;

    return ListView.separated(
      padding: EdgeInsets.symmetric(
        vertical: Sizes.p24,
      ),
      itemCount: ambCalls.length,
      itemBuilder: (context, index) {
        final ambCall = ambCalls[index];
        final ambCallTile = CallListTile(
          call: ambCall,
          onTap: () => AutoRouter.of(context).push(
            AmbulanceCallDetailRoute(call: ambCall, clientCalls: []),
          ),
        );

        final localDate = DateTime.parse(ambCall.dateTime!).toLocal();
        final ambCallSectionHeader =
            dayMonthAndDayOfWeekFormat.format(localDate);
        if (curSectionHeader != ambCallSectionHeader) {
          curSectionHeader = ambCallSectionHeader;
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Gaps.h16,
              Text(
                curSectionHeader!,
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                      fontSize: Sizes.p24,
                    ),
              ),
              Gaps.h24,
              ambCallTile,
            ],
          );
        } else {
          return ambCallTile;
        }
      },
      separatorBuilder: (context, index) => SizedBox(
        height: Sizes.p8,
      ),
    );
  }

  void _redirectToAmbulanceShift(
      {bool redirectToCallListOnShiftStarted = false}) {
    AutoRouter.of(context).replace(TeamRoute(
      redirectToCallListOnShiftStarted: redirectToCallListOnShiftStarted,
    ));
  }

  Future<void> _onPullToRefresh() async {
    final completer = Completer();
    _pulledToRefresh = true;
    _callListBloc.add(LoadCallList(completer: completer));
    return completer.future.whenComplete(() => _pulledToRefresh = false);
  }
}

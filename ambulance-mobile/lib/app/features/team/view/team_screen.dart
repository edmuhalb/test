import 'dart:async';

import 'package:ambulance/app/features/team/block/team_list_bloc.dart';
import 'package:ambulance/app/repositories/team/team_repository.dart';
import 'package:ambulance/app/router/router.gr.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/widgets/scaffold/scaffold_body.dart';
import 'package:ambulance/app/widgets/scaffold/scaffold_bottom_bar.dart';
import 'package:ambulance/app/widgets/widgets.dart';
import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';

@RoutePage()
class TeamScreen extends StatefulWidget {
  final bool redirectToCallListOnShiftStarted;

  const TeamScreen({
    super.key,
    this.redirectToCallListOnShiftStarted = false,
  });

  @override
  State<TeamScreen> createState() => _TeamScreenState();
}

class _TeamScreenState extends State<TeamScreen> {
  final _teamList = TeamListBloc(GetIt.I<TeamRepository>());

  @override
  void initState() {
    super.initState();
    _teamList.add(LoadTeamList());
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(AppPhrases.ambulanceTeam),
      ),
      extendBody: true,
      endDrawer: const AppDrawer(),
      body: ScaffoldBody(
        child: RefreshIndicator(
          onRefresh: _onRefresh,
          child: BlocBuilder<TeamListBloc, TeamListState>(
            bloc: _teamList,
            builder: (context, state) {
              if (state is TeamListLoaded) {
                final team = state.teamList;

                if (team != null) {
                  return Column(
                    children: [
                      Gaps.h24,
                      TeamTile(brigade: team),
                    ],
                  );
                }
                return const Center(child: Text('Бригада не сформирована'));
              }

              if (state is TeamListLoadedFailure) {
                return ErrorView(
                  errorMessage: state.exception?.message,
                  onTryAgain: _onRefresh,
                );
              }
              return const Center(child: CircularProgressIndicator());
            },
          ),
        ),
      ),
      bottomNavigationBar: BlocBuilder<TeamListBloc, TeamListState>(
        bloc: _teamList,
        builder: (context, state) {
          if (state is! TeamListLoaded ||
              state.teamList == null ||
              state.teamList!.status != 'scheduled') {
            return Container();
          }

          final team = state.teamList!;

          return ScaffoldBottomBar(
            child: Button(
              label: Text('Начать смену'),
              style: primaryButtonStyle,
              onPressed: () {
                _openStartShiftDialog(context, team.id);
              },
            ),
          );
        },
      ),
    );
  }

  Future<void> _onRefresh() async {
    final completer = Completer();
    return completer.future;
  }

  Future<void> _openStartShiftDialog(BuildContext context, int teamId) async {
    showDialog(
      context: context,
      builder: (BuildContext context) => AlertDialog(
        title: const Text('Состав бригады'),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(5),
        ),
        content: const Text(
            'Перед началом смены убедитесь, что состав бригады правильный'),
        actions: <Widget>[
          Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: Button(
              label: Text('Начать смену'),
              style: primaryButtonStyle,
              onPressed: () {
                Navigator.of(context).pop();

                final completer = Completer();

                _teamList.add(StartWork(
                  completer: completer,
                  teamid: teamId,
                ));

                if (widget.redirectToCallListOnShiftStarted) {
                  completer.future.then(
                    (_) => _gotoCallList(),
                  );
                }
              },
            ),
          ),
          Button(
            label: Text('Отмена'),
            style: secondaryButtonStyle,
            onPressed: () => Navigator.of(context).pop(),
          ),
        ],
      ),
    );
  }

  void _gotoCallList() {
    AutoRouter.of(context).maybePop(context);
    AutoRouter.of(context).replace(const CallsListRoute());
  }
}

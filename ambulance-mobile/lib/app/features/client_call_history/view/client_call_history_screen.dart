import 'dart:async';

import 'package:ambulance/app/features/client_call_history/bloc/client_call_history_bloc.dart';
import 'package:ambulance/app/features/client_call_history/widgets/client_call_widget.dart';
import 'package:ambulance/app/repositories/ambulance_call/ambulance_call_repository.dart';
import 'package:ambulance/app/theme/theme.dart';
import 'package:ambulance/app/widgets/error_view/error_view.dart';
import 'package:ambulance/app/widgets/scaffold/scaffold_body.dart';
import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';

@RoutePage()
class ClientCallHistoryScreen extends StatefulWidget {
  final int clientId;
  final int ambCallId;

  const ClientCallHistoryScreen({
    super.key,
    required this.clientId,
    required this.ambCallId,
  });

  @override
  State<ClientCallHistoryScreen> createState() =>
      _ClientCallHistoryScreenState();
}

class _ClientCallHistoryScreenState extends State<ClientCallHistoryScreen> {
  final _callHistoryBloc = ClientCallHistoryBloc(GetIt.I<AmbulanceCallRepository>());

  @override
  void initState() {
    super.initState();
    _callHistoryBloc.add(LoadClientCallHistory(
      clientId: widget.clientId,
      callId: widget.ambCallId,
    ));
  }

  @override
  Widget build(BuildContext context) =>
      BlocBuilder<ClientCallHistoryBloc, ClientCallHistoryState>(
        bloc: _callHistoryBloc,
        builder: _build,
      );

  Widget _build(
    BuildContext context,
    ClientCallHistoryState blockState,
  ) {
    if (blockState is ClientCallHistoryLoadedState) {
      return _buildScaffold(
        body: RefreshIndicator(
          color: AppColors.primary,
          backgroundColor: AppColors.card,
          onRefresh: _onRefresh,
          child: _buildBody(context, blockState),
        ),
      );
    }

    if (blockState is ClientCallHistoryLoadFailedState) {
      return _buildScaffold(
        body: ErrorView(
          errorMessage: blockState.exception?.message,
          onTryAgain: () => _callHistoryBloc.add(
            LoadClientCallHistory(
              callId: widget.ambCallId,
              clientId: widget.clientId,
            ),
          ),
        ),
      );
    }

    return _buildScaffold(
      body: Center(
        child: CircularProgressIndicator(),
      ),
    );
  }

  Widget _buildScaffold({
    required Widget body,
    Widget? bottomAppBar,
  }) =>
      Scaffold(
        extendBody: true,
        body: ScaffoldBody(
          child: Stack(
            clipBehavior: Clip.none,
            children: [
              body,
              Positioned(
                top: 1,
                left: -Sizes.p16,
                child: _buildCloseScreenButton(context),
              ),
            ],
          ),
        ),
        bottomNavigationBar: bottomAppBar,
      );

  Widget _buildCloseScreenButton(BuildContext context) => IconButton(
        padding: EdgeInsets.zero,
        color: AppColors.primary,
        icon: Icon(
          Icons.close,
          size: Sizes.p24,
        ),
        onPressed: () => AutoRouter.of(context).maybePop(),
      );

  Widget _buildBody(BuildContext context, ClientCallHistoryLoadedState state) {
    final clientCalls = state.clientCalls
        .where((x) => x.services != null && x.services!.isNotEmpty)
        .toList();

    return ListView.separated(
      itemCount: clientCalls.length,
      itemBuilder: (context, index) {
        final clientCall = clientCalls[index];

        if (index == 0) {
          return Column(
            children: [
              Gaps.h16,
              buildScreenHeader(context),
              Gaps.h24,
              ClientCallWidget(
                clientCall: clientCall,
              ),
            ],
          );
        } else {
          return ClientCallWidget(
            clientCall: clientCall,
          );
        }
      },
      separatorBuilder: (context, index) => SizedBox(
        height: Sizes.p24,
      ),
    );
  }

  Widget buildScreenHeader(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Text(
          AppPhrases.clientHistory,
          style: Theme.of(context).textTheme.headlineLarge,
        ),
      ],
    );
  }

  Future _onRefresh() {
    final completer = Completer();
    _callHistoryBloc.add(LoadClientCallHistory(
      completer: completer,
      clientId: widget.clientId,
      callId: widget.ambCallId,
    ));
    return completer.future;
  }
}

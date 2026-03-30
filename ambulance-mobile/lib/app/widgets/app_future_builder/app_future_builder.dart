import 'package:flutter/material.dart';

import '../../theme/theme.dart';

class AppFutureBuilder<T> extends StatelessWidget {
  final Future<T>? future;
  final Function(BuildContext context)? progressBuilder;
  final Function(BuildContext context, T data) builder;
  final Function(BuildContext context)? emptyBuilder;
  final Function(BuildContext context, Object? error)? errorBuilder;

  const AppFutureBuilder({
    super.key,
    this.future,
    this.progressBuilder,
    required this.builder,
    this.emptyBuilder,
    this.errorBuilder,
  });

  @override
  Widget build(BuildContext context) => FutureBuilder<T>(
        future: future,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return _buildProgress(context);
          }

          if (snapshot.hasError) {
            return _buildError(context, snapshot.error);
          }

          if (snapshot.hasData) {
            return _buildDataWidget(context, snapshot.data as T);
          }

          return _buildNodataWidget(context);
        },
      );

  Widget _buildProgress(BuildContext context) => _buildWithAnimations(
        widgetId: 'progress',
        widget: progressBuilder?.call(context) ??
            AppFutureBuilder.buildDefaultProgressWidget(),
      );

  Widget _buildError(BuildContext context, Object? error) =>
      _buildWithAnimations(
        widgetId: 'error',
        widget: errorBuilder?.call(context, error) ?? Container(),
      );

  Widget _buildDataWidget(BuildContext context, T data) => _buildWithAnimations(
        widgetId: 'data',
        widget: builder(context, data),
      );

  Widget _buildNodataWidget(BuildContext context) => _buildWithAnimations(
        widgetId: 'nodata',
        widget: emptyBuilder?.call(context) ?? Container(),
      );

  static Widget buildDefaultProgressWidget() => Center(
        child: CircularProgressIndicator(),
      );

  static Widget _buildWithAnimations({
    required String widgetId,
    required Widget widget,
  }) =>
      AnimatedSize(
        duration: themeAnimationDuration,
        child: AnimatedSwitcher(
          duration: themeAnimationDuration,
          child: KeyedSubtree(
            key: ValueKey(widgetId),
            child: widget,
          ),
        ),
      );
}

part of 'app_drawer.dart';

const double _kDrawerHeaderHeight = kToolbarHeight + 1.0; // bottom edge

class _AppDrawerHeader extends StatelessWidget {
  const _AppDrawerHeader({
    required this.title,
    this.margin = const EdgeInsets.only(bottom: 8.0),
    this.padding = const EdgeInsets.fromLTRB(
      Sizes.p16,
      Sizes.p16 + Sizes.p4,
      Sizes.p16,
      Sizes.p8,
    ),
    this.duration = themeAnimationDuration,
    this.curve = Curves.fastOutSlowIn,
  });

  final String title;

  final EdgeInsetsGeometry padding;

  final EdgeInsetsGeometry? margin;

  final Duration duration;

  final Curve curve;

  @override
  Widget build(BuildContext context) {
    assert(debugCheckHasMaterial(context));
    assert(debugCheckHasMediaQuery(context));
    final double statusBarHeight = MediaQuery.paddingOf(context).top;
    return Container(
      height: statusBarHeight + _kDrawerHeaderHeight,
      margin: margin,
      decoration: BoxDecoration(
        border: Border(
          bottom: Divider.createBorderSide(context),
        ),
      ),
      child: AnimatedContainer(
        padding: padding.add(EdgeInsets.only(top: statusBarHeight)),
        decoration: BoxDecoration(
          color: AppColors.primary,
        ),
        duration: duration,
        curve: curve,
        child: DefaultTextStyle(
          style: appDrawerTitleTheme,
          child: MediaQuery.removePadding(
            context: context,
            removeTop: true,
            child: Text(title),
          ),
        ),
      ),
    );
  }
}

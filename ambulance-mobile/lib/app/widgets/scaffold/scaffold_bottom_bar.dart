import 'package:flutter/material.dart';

import '../../theme/theme.dart';

class ScaffoldBottomBar extends StatelessWidget {
  final Widget child;
  final Color? color;
  final double? elevation;
  final double? borderRadius;
  final EdgeInsets? padding;

  const ScaffoldBottomBar({
    super.key,
    required this.child,
    this.color,
    this.elevation,
    this.borderRadius,
    this.padding,
  });

  @override
  Widget build(BuildContext context) {
    return PhysicalShape(
      clipper: ShapeBorderClipper(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.only(
            topLeft: Radius.circular(borderRadius ?? Sizes.p24),
            topRight: Radius.circular(borderRadius ?? Sizes.p24),
          ),
        ),
      ),
      color: Theme.of(context).bottomAppBarTheme.color ?? AppColors.card,
      shadowColor: Theme.of(context).bottomAppBarTheme.shadowColor ??
          AppColors.cardShadow,
      elevation: elevation ??
          Theme.of(context).bottomAppBarTheme.elevation ??
          Sizes.p8,
      child: Material(
        type: MaterialType.transparency,
        child: SafeArea(
          child: Padding(
            padding: padding ?? Theme.of(context).bottomAppBarTheme.padding ??
                const EdgeInsets.symmetric(
                  vertical: Sizes.p8,
                  horizontal: Sizes.p16,
                ),
            child: child,
          ),
        ),
      ),
    );
  }
}

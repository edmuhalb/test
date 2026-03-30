import 'package:flutter/material.dart';

import '../../theme/theme.dart';

class ScaffoldBody extends StatelessWidget {
  final Widget? child;
  final Color? color;
  final BorderRadius? borderRadius;
  final EdgeInsets padding;

  const ScaffoldBody({
    super.key,
    this.child,
    this.color,
    this.borderRadius,
    this.padding = const EdgeInsets.symmetric(horizontal: Sizes.p16),
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => FocusScope.of(context).unfocus(),
      child: SafeArea(
        bottom: false,
        child: Container(
          height: double.infinity,
          width: double.infinity,
          padding: padding,
          clipBehavior: Clip.antiAlias,
          decoration: BoxDecoration(
            borderRadius: borderRadius ??
                BorderRadius.vertical(
                  top: Radius.circular(Sizes.p16),
                ),
            color: color ?? AppColors.scaffoldBody,
          ),
          child: child,
        ),
      ),
    );
  }
}

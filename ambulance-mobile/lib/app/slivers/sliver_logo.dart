import 'package:flutter/material.dart';

import '../theme/theme.dart';

class SliverLogo extends SliverPersistentHeader {
  SliverLogo({
    super.key,
    double maxPaddingTop = Sizes.p200,
    double maxLogoHeight = Sizes.p200,
  }) : super(
          pinned: false,
          floating: false,
          delegate: _Delegate(
            maxLogoHeight: maxLogoHeight,
            maxPaddingTop: maxPaddingTop,
          ),
        );
}

class _Delegate extends SliverPersistentHeaderDelegate {
  final double maxPaddingTop;
  final double maxLogoHeight;

  const _Delegate({
    required this.maxPaddingTop,
    required this.maxLogoHeight,
  });

  @override
  Widget build(
    BuildContext context,
    double shrinkOffset,
    bool overlapsContent,
  ) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        Flexible(
          child: ConstrainedBox(
            constraints: BoxConstraints(
              maxHeight: maxPaddingTop,
            ),
          ),
        ),
        Flexible(
          child: ConstrainedBox(
            constraints: BoxConstraints(
              maxHeight: maxLogoHeight,
            ),
            child: Image.asset(
              AppImages.logo2x,
              fit: BoxFit.contain,
            ),
          ),
        ),
      ],
    );
  }

  @override
  double get maxExtent => maxPaddingTop + maxLogoHeight;

  @override
  double get minExtent => (maxPaddingTop + maxLogoHeight) / 2;

  @override
  bool shouldRebuild(covariant SliverPersistentHeaderDelegate oldDelegate) =>
      true;
}

import 'package:flutter/material.dart';

import '../../theme/theme.dart';

class TileContainer extends StatelessWidget {
  final void Function()? onTap;
  final Widget child;
  final EdgeInsets margin;

  const TileContainer({
    super.key,
    this.onTap,
    required this.child,
    this.margin = const EdgeInsets.only(bottom: 25),
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      child: Container(
        margin: margin,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(5),
          border: Border.all(width: 2, color: AppColors.scaffold),
          boxShadow: const [
            BoxShadow(
              color: Color.fromRGBO(187, 196, 196, 1),
              blurRadius: 4,
              offset: Offset(0, 2),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(15),
          child: child,
        ),
      ),
    );
  }
}

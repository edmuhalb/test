import 'package:flutter/material.dart';

import '../../../theme/theme.dart';

class Fab extends StatelessWidget {
  final Function() onPressed;
  final Widget? child;
  final bool visible;

  const Fab({
    super.key,
    required this.onPressed,
    this.child,
    this.visible = true,
  });

  @override
  Widget build(BuildContext context) {
    if (!visible) {
      return Container();
    }

    return FloatingActionButton(
      onPressed: onPressed,
      shape: const CircleBorder(),
      backgroundColor: AppColors.primary,
      child: child,
      // child: const Icon(Icons.add_box, color: Colors.white),
    );
  }
}

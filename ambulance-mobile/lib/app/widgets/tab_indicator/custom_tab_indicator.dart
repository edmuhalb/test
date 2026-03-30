import 'package:ambulance/app/theme/theme.dart';
import 'package:flutter/material.dart';

class CustomTabIndicator extends Decoration {
  final Color? color;

  const CustomTabIndicator({this.color = Colors.blueAccent});

  @override
  BoxPainter createBoxPainter([VoidCallback? onChanged]) {
    return _CustomPainter(this, onChanged);
  }
}

class _CustomPainter extends BoxPainter {
  final CustomTabIndicator decoration;

  _CustomPainter(this.decoration, VoidCallback? onChanged) : super(onChanged);

  @override
  void paint(Canvas canvas, Offset offset, ImageConfiguration configuration) {
    assert(configuration.size != null);

    Offset pos = offset - Offset(Sizes.p12, 0);
    Size s = Size(configuration.size!.width + Sizes.p24, configuration.size!.height);
    final Rect rect = pos & s;
    final Paint paint = Paint();

    paint.color = decoration.color!;
    paint.style = PaintingStyle.fill;
    canvas.drawRRect(
        RRect.fromRectAndRadius(rect, Radius.circular(16.0)), paint);
  }
}

import 'package:flutter/material.dart';

class Logo extends StatelessWidget {
  const Logo({
    super.key,
    this.height = 200,
    this.width = 200,
  });

  final double? height;
  final double? width;

  @override
  Widget build(BuildContext context) {
    return Image.asset(
      'assets/images/logo.png',
      width: width,
      height: height,
    );
  }
}

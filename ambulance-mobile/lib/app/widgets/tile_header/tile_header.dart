import 'package:flutter/material.dart';

class TileHeader extends StatelessWidget {
  final String title;
  final Widget content;
  final EdgeInsets padding;
  final MainAxisAlignment mainAxisAlignment;
  final String? direction;
  final CrossAxisAlignment crossAxisAlignment;

  const TileHeader({
    super.key,
    required this.title,
    required this.content,
    this.padding = const EdgeInsets.only(bottom: 10),
    this.mainAxisAlignment = MainAxisAlignment.spaceBetween,
    this.direction = 'column',
    this.crossAxisAlignment = CrossAxisAlignment.start,
  });

  Widget _getContentWidget(List<Widget> children) {
    if (direction == 'row') {
      return Row(
        mainAxisAlignment: mainAxisAlignment,
        crossAxisAlignment: crossAxisAlignment,
        children: children,
      );
    }
    return Column(
      mainAxisAlignment: mainAxisAlignment,
      crossAxisAlignment: crossAxisAlignment,
      children: children,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: padding,
      child: _getContentWidget(
        [
          Text(
            title,
            style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 18),
          ),
          Flexible(child: content),
        ],
      ),
    );
  }
}

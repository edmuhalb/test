import 'package:flutter/material.dart';

import '../../theme/theme.dart';

class TileSection extends StatelessWidget {
  final String title;
  final String? content;
  final String? direction;

  const TileSection({
    super.key,
    required this.title,
    this.content,
    this.direction = 'column',
  });

  String _getSectionContent(String? sectionContent) {
    if (sectionContent != null && sectionContent.isNotEmpty) {
      return sectionContent;
    }
    return '---';
  }

  Widget _getContentWidget(List<Widget> children) {
    if (direction == 'row') {
      return Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: children,
      );
    }
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      mainAxisAlignment: MainAxisAlignment.start,
      children: children,
    );
  }

  @override
  Widget build(BuildContext context) {
    final int flex = direction == 'row' ? 1 : 0;

    return Padding(
      padding: const EdgeInsets.only(bottom: 5, top: 5),
      child: _getContentWidget(
        [
          Flexible(
            flex: flex,
            fit: FlexFit.loose,
            child: Padding(
              padding: const EdgeInsets.only(right: 5),
              child: Text(
                title,
                style: const TextStyle(
                  color: AppColors.primary,
                  fontWeight: FontWeight.w700,
                  fontSize: 15,
                ),
              ),
            ),
          ),
          Flexible(
            flex: flex,
            fit: FlexFit.loose,
            child: Text(_getSectionContent(content)),
          ),
        ],
      ),
    );
  }
}

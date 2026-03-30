import 'package:ambulance/app/repositories/team/team.dart';
import 'package:ambulance/app/widgets/widgets.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../theme/theme.dart';

String _gerParsetDate(String? date) {
  if (date == null) {
    return '';
  }
  return DateFormat.yMMMMd('ru_RU').format(DateTime.parse(date));
}

class TeamTile extends StatelessWidget {
  final Team? brigade;

  const TeamTile({
    super.key,
    this.brigade,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        TileContainer(
          margin: const EdgeInsets.only(bottom: 10),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            mainAxisAlignment: MainAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              TileSection(
                  title: 'Админ:',
                  content: brigade!.admin!["name"],
                  direction: 'row'),
              TileSection(
                  title: 'Доктор:',
                  content: brigade!.doctor!["name"],
                  direction: 'row'),
            ],
          ),
        ),
        if (brigade!.status == 'work')
          Padding(
            padding: EdgeInsets.symmetric(horizontal: Sizes.p16),
            child: Column(
              children: [
                TileSection(
                  title: 'Дата начала',
                  content: _gerParsetDate(brigade!.startedAt),
                  direction: 'row',
                ),
                // const TileSection(
                //     title: 'Отработано часов', content: '1', direction: 'row'),
                // const TileSection(
                //     title: 'Выполенно заказов', content: '3', direction: 'row'),
                // const TileSection(
                //     title: 'Сумма заказов',
                //     content: '1000 руб.',
                //     direction: 'row'),
              ],
            ),
          )
      ],
    );
  }
}

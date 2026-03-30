part of 'app_drawer.dart';

class _AppDrawerFooter extends StatelessWidget {

  const _AppDrawerFooter();

  @override
  Widget build(BuildContext context) => Column(
        children: [
          Text(
            'Версия ${AppVersionManager.currentVersion}',
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: AppColors.text,
                ),
          ),
          Gaps.h32,
        ],
      );
}

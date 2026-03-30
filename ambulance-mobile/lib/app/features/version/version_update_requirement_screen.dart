import 'package:ambulance/app/app_version_manager.dart';
import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';

import '../../slivers/sliver_logo.dart';
import '../../theme/theme.dart';
import '../../widgets/scaffold/scaffold_body.dart';

const logoMaxPaddingTopRatio = 0.27;

// TODO: implement button to download updated version
@RoutePage()
class VersionUpdateRequirementScreen extends StatelessWidget {
  final VersionInfo versionInfo;

  const VersionUpdateRequirementScreen({
    super.key,
    required this.versionInfo,
  });

  double getScreenHeight(BuildContext context) =>
      MediaQuery.sizeOf(context).height;

  String get displayCurrentVersion =>
      '${AppPhrases.currentVerion}: v${AppVersionManager.currentVersion}';

  String get displayTargetVersion =>
      '${AppPhrases.latestVerion}: v${versionInfo.target}';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: ScaffoldBody(
        color: Theme.of(context).cardColor,
        child: CustomScrollView(
          slivers: [
            SliverLogo(
              maxPaddingTop: getScreenHeight(context) * logoMaxPaddingTopRatio,
            ),
            SliverToBoxAdapter(child: Gaps.h40),
            SliverToBoxAdapter(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    AppPhrases.updateAppResetMed,
                    style: Theme.of(context).textTheme.bodyLarge,
                  ),
                  Gaps.h16,
                  Text(
                    '$displayCurrentVersion | $displayTargetVersion',
                    style: Theme.of(context).textTheme.bodySmall,
                  ),
                  Gaps.h16,
                  const Text(AppPhrases.updateAppMotivation)
                ],
              ),
            )
          ],
        ),
      ),
    );
  }
}

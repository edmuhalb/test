part of 'app_drawer.dart';

class _AppDrawerMenu extends StatelessWidget {
  final VoidCallback? onAmbulanceTeamTapped;
  final VoidCallback? onAmbulanceCallsTapped;
  final VoidCallback? onProfileTapped;
  final VoidCallback? onSignOutTapped;
  final Future<Team?> teamFuture;
  final VoidCallback? onFinishShiftTapped;

  const _AppDrawerMenu({
    this.onAmbulanceTeamTapped,
    this.onAmbulanceCallsTapped,
    this.onProfileTapped,
    this.onSignOutTapped,
    required this.teamFuture,
    this.onFinishShiftTapped,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        ListTile(
          leading: Padding(
            padding: const EdgeInsets.only(left: Sizes.p12),
            child: SvgPicture.asset(
              AppImages.ambulanceTeam,
              colorFilter: ColorFilter.mode(
                Theme.of(context).textTheme.bodyMedium!.color!,
                BlendMode.srcIn,
              ),
              width: Sizes.p16,
              height: Sizes.p16,
            ),
          ),
          title: Text(AppPhrases.ambulanceTeam),
          onTap: onAmbulanceTeamTapped,
        ),
        ListTile(
          leading: Padding(
            padding: const EdgeInsets.only(left: Sizes.p12),
            child: SvgPicture.asset(
              AppImages.phoneClocks,
              colorFilter: ColorFilter.mode(
                Theme.of(context).textTheme.bodyMedium!.color!,
                BlendMode.srcIn,
              ),
              width: Sizes.p16,
              height: Sizes.p16,
            ),
          ),
          title: Text(AppPhrases.ambulanceCalls),
          onTap: onAmbulanceCallsTapped,
        ),
        ListTile(
          leading: Padding(
            padding: const EdgeInsets.only(left: Sizes.p12),
            child: SvgPicture.asset(
              AppImages.profile,
              colorFilter: ColorFilter.mode(
                Theme.of(context).textTheme.bodyMedium!.color!,
                BlendMode.srcIn,
              ),
              width: Sizes.p16,
              height: Sizes.p16,
            ),
          ),
          title: Text(AppPhrases.profile),
          onTap: onProfileTapped,
        ),
        Divider(
          indent: Sizes.p16 + Sizes.p12,
          endIndent: Sizes.p16 + Sizes.p12,
        ),
        ListTile(
          leading: Padding(
            padding: const EdgeInsets.only(left: Sizes.p12),
            child: SvgPicture.asset(
              AppImages.times,
              colorFilter: ColorFilter.mode(
                Theme.of(context).textTheme.bodyMedium!.color!,
                BlendMode.srcIn,
              ),
              width: Sizes.p16,
              height: Sizes.p16,
            ),
          ),
          title: Text(AppPhrases.signOut),
          onTap: onSignOutTapped,
        ),
        FutureBuilder(
          future: teamFuture,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return AnimatedSwitcher(
                duration: themeAnimationDuration,
                child: SizedBox(
                  key: ValueKey('progress'),
                  height: Sizes.p40,
                  width: Sizes.p40,
                  child: Center(
                    child: SizedBox(
                      height: Sizes.p24,
                      width: Sizes.p24,
                      child: CircularProgressIndicator(
                        strokeWidth: 3,
                      ),
                    ),
                  ),
                ),
              );
            }

            if (snapshot.connectionState == ConnectionState.done &&
                snapshot.hasData &&
                snapshot.data != null) {
              return AnimatedSwitcher(
                duration: themeAnimationDuration,
                child: Padding(
                  key: ValueKey('button'),
                  padding: const EdgeInsets.symmetric(horizontal: Sizes.p16),
                  child: Button(
                    style: primaryButtonStyle,
                    label: Text(AppPhrases.finishWorkingShift),
                    onPressed: onFinishShiftTapped,
                  ),
                ),
              );
            }

            return AnimatedSwitcher(
              duration: themeAnimationDuration,
              child: Container(
                key: ValueKey('hidden'),
              ),
            );
          },
        ),
      ],
    );
  }
}

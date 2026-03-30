import 'package:ambulance/app/widgets/scaffold/scaffold_bottom_bar.dart';
import 'package:flutter/material.dart';

import '../../theme/theme.dart';

class BottomSheetScaffold extends StatelessWidget {
  final String? title;
  final MainAxisAlignment? titleAlignment;
  final Widget? body;
  final Widget? bottomBar;
  final bool expand;

  const BottomSheetScaffold({
    super.key,
    this.title,
    this.titleAlignment,
    this.body,
    this.bottomBar,
    this.expand = false,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => FocusScope.of(context).unfocus(),
      child: Padding(
        padding: EdgeInsets.only(
          bottom: MediaQuery.viewInsetsOf(context).bottom,
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.end,
          mainAxisSize: MainAxisSize.min,
          children: [
            Flexible(
              child: ListView(
                shrinkWrap: !expand,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.start,
                    children: [
                      Gaps.h16,
                      if (title != null) ...[
                        Gaps.h8,
                        Row(
                          mainAxisAlignment: titleAlignment ?? MainAxisAlignment.start,
                          children: [
                            Flexible(
                              child: Padding(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: Sizes.p16,
                                ),
                                child: Text(
                                  title!,
                                  overflow: TextOverflow.ellipsis,
                                  style: Theme.of(context)
                                      .appBarTheme
                                      .titleTextStyle
                                      ?.copyWith(
                                        color: AppColors.text,
                                      ),
                                ),
                              ),
                            ),
                          ],
                        ),
                        Gaps.h8,
                      ],
                      if (body != null) ...[
                        Gaps.h16,
                        Flexible(
                          flex: expand ? 1 : 0,
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: Sizes.p16,
                            ),
                            child: body!,
                          ),
                        ),
                        Gaps.h16,
                      ],
                      Gaps.h8,
                    ],
                  ),
                ],
              ),
            ),
            if (bottomBar != null) ...[
              ScaffoldBottomBar(
                elevation: 0,
                borderRadius: Sizes.p16,
                padding: EdgeInsets.all(Sizes.p16),
                child: bottomBar!,
              ),
            ],
          ],
        ),
      ),
    );
  }
}

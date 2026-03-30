import 'package:flutter/material.dart';

import '../../theme/theme.dart';

class Button extends StatelessWidget {
  final Widget label;
  final VoidCallback? onPressed;
  final ButtonStyle? style;
  final Widget? icon;
  final bool isLoading;
  final bool enabled;
  final FocusNode? focusNode;
  final IconAlignment? iconAlignment;

  const Button({
    super.key,
    required this.label,
    this.enabled = true,
    this.isLoading = false,
    this.onPressed,
    this.style,
    this.focusNode,
  })  : icon = null,
        iconAlignment = null;

  const Button.icon({
    super.key,
    required this.label,
    this.enabled = true,
    this.isLoading = false,
    this.onPressed,
    this.style,
    this.icon,
    this.focusNode,
    this.iconAlignment,
  });

  @override
  Widget build(BuildContext context) {
    final style = this.style ?? Theme.of(context).elevatedButtonTheme.style;
    return SizedBox(
      width: double.infinity,
      height: Sizes.p40,
      child: icon == null
          ? ElevatedButton(
              onPressed: enabled && !isLoading ? onPressed : null,
              focusNode: focusNode,
              style: style ?? Theme.of(context).elevatedButtonTheme.style,
              child: AnimatedSwitcher(
                duration: themeAnimationDuration,
                child: isLoading
                    ? SizedBox(
                        key: ValueKey(isLoading),
                        height: Sizes.p24,
                        width: Sizes.p24,
                        child: CircularProgressIndicator(
                          strokeWidth: 3,
                          color: style?.foregroundColor?.resolve(
                            {WidgetState.pressed},
                          ),
                        ),
                      )
                    : KeyedSubtree(
                        key: ValueKey(isLoading),
                        child: label,
                      ),
              ),
            )
          : ElevatedButton.icon(
              onPressed: enabled && !isLoading ? onPressed : null,
              focusNode: focusNode,
              style: style ?? Theme.of(context).elevatedButtonTheme.style,
              label: label,
              iconAlignment: iconAlignment ?? IconAlignment.start,
              icon: AnimatedSwitcher(
                duration: themeAnimationDuration,
                child: isLoading
                    ? SizedBox(
                        key: ValueKey(isLoading),
                        height: Sizes.p24,
                        width: Sizes.p24,
                        child: CircularProgressIndicator(
                          strokeWidth: 3,
                          color: style?.foregroundColor?.resolve(
                            {WidgetState.pressed},
                          ),
                        ),
                      )
                    : KeyedSubtree(
                        key: ValueKey(isLoading),
                        child: icon!,
                      ),
              ),
            ),
    );
  }
}

import 'package:flutter/material.dart';

class ErrorView extends StatelessWidget {
  final void Function() onTryAgain;
  final String? errorMessage;

  const ErrorView({
    super.key,
    required this.onTryAgain,
    this.errorMessage,
  });

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Text(errorMessage ?? 'Что-то пошло не так'),
          const Text('Пожалуйста, повторите попытку позже'),
          const SizedBox(height: 30),
          TextButton(
            onPressed: onTryAgain,
            child: const Text('Попробовать снова'),
          )
        ],
      ),
    );
  }
}

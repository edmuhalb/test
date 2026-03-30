import 'package:flutter/material.dart';

void showCustomBottomSheet<T>(
  BuildContext context, {
  required List<Widget> children,
}) =>
    showModalBottomSheet<T>(
      context: context,
      isScrollControlled: true,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(5),
      ),
      builder: (BuildContext context) => SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ...children,
            // SizedBox(height: MediaQuery.of(context).viewInsets.bottom),
          ],
        ),
        // child: Padding(
        //   padding: EdgeInsets.only(
        //     bottom: MediaQuery.of(context).viewInsets.bottom,
        //   ),
        //   child: Column(
        //     mainAxisSize: MainAxisSize.min,
        //     children: [
        //       ...children,
        //       SizedBox(height: MediaQuery.of(context).viewInsets.bottom),
        //     ],
        //   ),
        // ),
      ),
    );

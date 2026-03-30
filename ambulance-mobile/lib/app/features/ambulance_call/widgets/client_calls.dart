import 'package:flutter/material.dart';
import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:intl/intl.dart';

class ClientCalls extends StatelessWidget {
  final List<ClientCall> clientCalls;

  const ClientCalls({
    super.key,
    required this.clientCalls,
  });

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      itemCount: clientCalls.length,
      shrinkWrap: true, // wraps content in a scroll view
      physics:
          NeverScrollableScrollPhysics(), // disables scrolling for the listView
      itemBuilder: (context, i) {
        var clientCall = clientCalls[i];
        if (clientCall.completedAt == null) {
          return null;
        }
        // П��еобразуем дату в DateTime объект
        DateTime completedAt = DateTime.parse(clientCall.completedAt as String);

        // Используем DateFormat для получения строки даты в нужном формате
        String completedAtFormatted =
            DateFormat('dd.MM.yyyy').format(completedAt);

        String amount =
            clientCall.price != null ? clientCall.price.toString() : '-';

        return Container(
          decoration: const BoxDecoration(color: Colors.white, boxShadow: [
            BoxShadow(
              color: Color.fromRGBO(187, 196, 196, 1),
              blurRadius: 4,
              offset: Offset(0, 2),
            ),
          ]),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      completedAtFormatted,
                      style: const TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 18,
                      ),
                    ),
                    Text(
                      'Сумма: $amount',
                      style: const TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 18,
                      ),
                    ),
                  ],
                ),
                ...clientCall.services!.map((json) {
                  final service = ProvidedService.fromJson(json);
                  return Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(service.name, style: const TextStyle(fontSize: 12)),
                      Text(service.price.toString(),
                          style: const TextStyle(fontSize: 12)),
                    ],
                  );
                }),
              ],
            ),
          ),
        );
      },
    );
  }
}

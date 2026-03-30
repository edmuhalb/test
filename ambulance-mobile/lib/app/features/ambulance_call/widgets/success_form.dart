import 'package:ambulance/app/theme/theme.dart';
import 'package:flutter/material.dart';
import 'package:ambulance/app/widgets/widgets.dart';


class SuccessForm extends StatefulWidget {
  final Function(Map<String, dynamic> body) onSubmit;
  final String? age;
  final String? adress;
  final String totalPrice;
  final String price;
  final String paymentNextOrder;
  final List<Map<String, dynamic>> services;

  const SuccessForm({
    super.key,
    required this.onSubmit,
    this.age = '',
    this.adress = '',
    required this.totalPrice,
    required this.price,
    required this.paymentNextOrder,
    required this.services,
  });

  @override
  State<SuccessForm> createState() => _SuccessFormState();
}

class _SuccessFormState extends State<SuccessForm> {
  final _formKey = GlobalKey<FormState>();
  final note = TextEditingController();

  _handleSubmit() {
    if (_formKey.currentState!.validate()) {
      widget.onSubmit({
        'note': note.value.text,
      });
    }
  }

  @override
  void dispose() {
    super.dispose();
    note.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Form(
      key: _formKey,
      child: Padding(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Инфо вызова
            Container(
              width: double.infinity,
              margin: const EdgeInsets.only(bottom: 10),
              padding: const EdgeInsets.symmetric(vertical: 5, horizontal: 15),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(5),
                border: Border.all(width: 2, color: AppColors.scaffold),
              ),
              child: Column(
                children: [
                  TileHeader(
                    direction: 'row',
                    title: 'Текущий заказ:',
                    content: Text(
                      widget.price,
                      style: const TextStyle(
                          fontWeight: FontWeight.w700, fontSize: 16),
                    ),
                  ),
                  TileHeader(
                    direction: 'row',
                    title: 'Предоплата:',
                    content: Text(
                      widget.paymentNextOrder,
                      style: const TextStyle(
                          fontWeight: FontWeight.w700, fontSize: 16),
                    ),
                  ),
                  TileHeader(
                    direction: 'row',
                    title: 'Итого:',
                    content: Text(
                      widget.totalPrice,
                      style: const TextStyle(
                          fontWeight: FontWeight.w700, fontSize: 16),
                    ),
                  ),
                  TileHeader(
                    title: 'Адрес:',
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    content: Text('${widget.adress}',
                        style: const TextStyle(
                            fontWeight: FontWeight.w700, fontSize: 16)),
                  ),
                ],
              ),
            ),
            // Услуги
            Container(
              width: double.infinity,
              margin: const EdgeInsets.only(bottom: 10),
              padding: const EdgeInsets.symmetric(vertical: 5, horizontal: 15),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(5),
                border: Border.all(width: 2, color: AppColors.scaffold),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: List.generate(widget.services.length, (index) {
                  final service = widget.services[index];
                  final content = service["price"];
                  final title = service["name"] ?? "";

                  return TileSection(
                    title: title,
                    content: '$content руб.',
                    direction: 'row',
                  );
                }),
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: InputField(
                controller: note,
                keyboardType: TextInputType.multiline,
                minLines: 3,
                maxLines: 100,
                hintText: 'Примечание',
              ),
            ),
            Button(
              label: Text('Завершить'),
              style: primaryButtonStyle,
              onPressed: _handleSubmit,
            ),
            SizedBox(height: MediaQuery.of(context).viewInsets.bottom),
          ],
        ),
      ),
    );
  }
}

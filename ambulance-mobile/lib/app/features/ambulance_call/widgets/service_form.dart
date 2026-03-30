// import 'dart:convert';
// import 'dart:io';

// import 'package:ambulance/app/theme/theme.dart';
// import 'package:image_picker/image_picker.dart';
// import 'package:multi_dropdown/multiselect_dropdown.dart';
// import 'package:flutter/material.dart';
// import 'package:ambulance/app/repositories/ambulance_call/models/ambulance_call_details.dart';
// import 'package:ambulance/app/widgets/widgets.dart';
// import 'package:ambulance/app/helpers/helpers.dart';

// class ServiceForm extends StatefulWidget {
//   final Function(Map<String, dynamic>) onSubmit;
//   final Function() onRemove;
//   final Map<String, dynamic>? initialValues;
//   final List<ValueItem<Map<String, dynamic>>> services;
//   final List<ValueItem<Map<String, dynamic>>> clinics;
//   final AmbulanceCallDetails calling;
//   final bool isEdit;

//   const ServiceForm({
//     super.key,
//     required this.onSubmit,
//     required this.onRemove,
//     required this.calling,
//     this.isEdit = false,
//     this.initialValues = const {},
//     this.services = const [],
//     this.clinics = const [],
//   });

//   @override
//   State<ServiceForm> createState() => ServiceFormState();
// }

// class ServiceFormState extends State<ServiceForm> {
//   final _serviceFormKey = GlobalKey<FormState>();
//   bool isReplayServiceType = false;
//   bool isHospitalServiceType = false;
//   bool isDefaultServiceType = false;

//   final price = TextEditingController();
//   final service = MultiSelectController();
//   final clinic = MultiSelectController();
//   final description = TextEditingController();

//   final plannedPrice = TextEditingController();
//   final plannedTimedAt = TextEditingController();
//   final plannedAt = TextEditingController();

//   List<ValueItem<String>> serviceOptions = [];

//   final List<XFile> images = [];

//   @override
//   void initState() {
//     super.initState();
//     final bool isReplay = widget.initialValues!["service"]["type"] == 'replay';
//     final bool isHospital =
//         widget.initialValues!["service"]["type"] == 'hospital';

//     final bool isDefault =
//         widget.initialValues!["service"]["type"] == 'default';

//     setState(() {
//       isReplayServiceType = isReplay;
//       isHospitalServiceType = isHospital;
//       isDefaultServiceType = isDefault;
//     });

//     if (widget.isEdit) {
//       price.text = widget.initialValues!['price'].toString();
//       plannedPrice.text = widget.initialValues!["plannedPrice"] != null
//           ? widget.initialValues!["plannedPrice"].toString()
//           : "";
//       description.text = widget.initialValues!["description"] ?? "";
//       if (widget.initialValues!["plannedAt"] != null) {
//         DateTime plannedAtTime =
//             DateTime.parse(widget.initialValues!["plannedAt"]);
//         if (plannedAtTime.isUtc) {
//           plannedAtTime = plannedAtTime.toLocal();
//           plannedTimedAt.text = plannedAtTime.toString();
//           plannedAt.text = plannedAtTime.toString();
//         }
//       } else {
//         plannedTimedAt.text = "";
//         plannedAt.text = "";
//       }
//     }
//   }

//   void _handleSubmit() {
//     if (!_serviceFormKey.currentState!.validate()) {
//       return;
//     }

//     final Map<String, dynamic> requestBody = {};

//     List<Map<String, String>> base64Images = [];
//     for (XFile imageFile in images) {
//       List<int> imageBytes = File(imageFile.path).readAsBytesSync();
//       String base64Image = base64Encode(imageBytes);
//       base64Images.add({"base64content": base64Image});
//     }

//     requestBody["images"] = base64Images;

//     final mapedServices = widget.calling.services?.map((service) {
//       return {
//         ...service,
//         'service': '/api/services/${service["service"]["id"]}',
//         'price': service["price"],
//       };
//     });
//     final callingServices = mapedServices != null ? mapedServices.toList() : [];

//     final clinicUrl = clinic.selectedOptions.isNotEmpty
//         ? '/api/clinics/${clinic.selectedOptions[0].value["id"]}'
//         : null;

//     // UPDATE
//     if (widget.isEdit) {
//       final selected = service.selectedOptions.map((service) {
//         return {
//           'service': '/api/services/${service.value["id"]}',
//           'clinic': clinicUrl,
//           if (price.text.isNotEmpty) 'price': int.parse(price.text),
//           'description': description.text,
//           if (price.text.isNotEmpty) 'price': int.parse(price.text),
//           if (plannedPrice.text.isNotEmpty)
//             'plannedPrice': int.parse(plannedPrice.text),
//           if (plannedAt.text.isNotEmpty && isReplayServiceType)
//             'plannedAt':
//                 plannedAt.text.replaceRange(11, 19, plannedTimedAt.text),
//         };
//       }).toList();
//       final filteredServices = [];

//       for (var i = 0; i < callingServices.length; i++) {
//         final element = callingServices[i];

//         if (i != widget.initialValues!["serviceId"]) {
//           filteredServices.add(element);
//         }
//       }
//       requestBody["services"] = [...filteredServices, ...selected];
//     }

//     // CREATE
//     if (!widget.isEdit) {
//       final selected = service.selectedOptions.map((service) {
//         return {
//           'service': '/api/services/${service.value["id"]}',
//           'type': service.value["type"],
//           'clinic': clinicUrl,
//           'description': description.text,
//           if (price.text.isNotEmpty) 'price': int.parse(price.text),
//           if (plannedPrice.text.isNotEmpty)
//             'plannedPrice': int.parse(plannedPrice.text),
//           if (plannedAt.text.isNotEmpty)
//             'plannedAt':
//                 plannedAt.text.replaceAll(r'00:00:00', plannedTimedAt.text),
//         };
//       }).toList();
//       requestBody["services"] = [...selected, ...callingServices];
//     }

//     widget.onSubmit(requestBody);
//   }

//   List<ValueItem> _getSelectedOptions() {
//     if (widget.initialValues != null) {
//       final service = widget.services.firstWhere((element) {
//         return element.value!["id"] == widget.initialValues!["service"]["id"];
//       });
//       return [service];
//     }
//     return [];
//   }

//   List<ValueItem> _getSelectedClinicOptions() {
//     if (widget.initialValues == null) {
//       return [];
//     }
//     if (widget.initialValues?['clinic'] == null ||
//         widget.initialValues?['clinic'] == "") {
//       return [];
//     }

//     final clinic = widget.clinics.firstWhere((element) {
//       final initialClinic = widget.initialValues?['clinic'];
//       final clinicUrl = '/app/api/clinics/${element.value?["id"]}';

//       return initialClinic == clinicUrl;
//     });

//     return [clinic];
//   }

//   @override
//   void dispose() {
//     super.dispose();
//     price.dispose();
//     service.dispose();
//     description.dispose();
//     plannedPrice.dispose();
//     plannedTimedAt.dispose();
//     plannedAt.dispose();
//   }

//   @override
//   Widget build(BuildContext context) {
//     return Form(
//       key: _serviceFormKey,
//       child: Padding(
//         padding: const EdgeInsets.all(20),
//         child: Column(
//           mainAxisSize: MainAxisSize.min,
//           children: [
//             Padding(
//               padding: const EdgeInsets.only(bottom: 10),
//               child: SelectField(
//                 hint: 'Услуга',
//                 options: widget.services,
//                 controller: service,
//                 searchEnabled: false,
//                 showClearIcon: false,
//                 selectedOptions: _getSelectedOptions(),
//                 onOptionSelected: (options) {
//                   final option = options.first;
//                   setState(() {
//                     isReplayServiceType = option.value["type"] == 'replay';
//                     isHospitalServiceType = option.value["type"] == 'hospital';
//                   });
//                 },
//               ),
//             ),
//             if (isHospitalServiceType)
//               Padding(
//                 padding: const EdgeInsets.only(bottom: 10),
//                 child: SelectField(
//                     hint: 'Клиника',
//                     options: widget.clinics,
//                     controller: clinic,
//                     searchEnabled: false,
//                     showClearIcon: true,
//                     selectedOptions: _getSelectedClinicOptions()),
//               ),
//             if (isReplayServiceType)
//               Column(
//                 children: [
//                   Padding(
//                     padding: const EdgeInsets.only(bottom: 10),
//                     child: InputField(
//                       label: 'Ориентировочная цена',
//                       validator: Validators.number,
//                       keyboardType: TextInputType.number,
//                       controller: plannedPrice,
//                     ),
//                   ),
//                   Padding(
//                     padding: const EdgeInsets.only(bottom: 10),
//                     child: DatePicker(
//                       validator: Validators.string,
//                       controller: plannedAt,
//                     ),
//                   ),
//                   Padding(
//                     padding: const EdgeInsets.only(bottom: 10),
//                     child: TimePicker(
//                       validator: Validators.string,
//                       controller: plannedTimedAt,
//                     ),
//                   ),
//                 ],
//               ),
//             if (isHospitalServiceType)
//               ImagesPicker(
//                 initialImages: images,
//                 label: 'Добавить фото паспорта',
//               ),
//             if (isHospitalServiceType)
//               Column(
//                 children: [
//                   Padding(
//                     padding: const EdgeInsets.only(bottom: 10),
//                     child: InputField(
//                       label: 'Стоимость в сутки',
//                       validator: Validators.number,
//                       keyboardType: TextInputType.number,
//                       controller: plannedPrice,
//                     ),
//                   ),
//                 ],
//               ),
//             Padding(
//               padding: const EdgeInsets.only(bottom: 10),
//               child: InputField(
//                 label: isReplayServiceType || isHospitalServiceType
//                     ? 'Предоплата'
//                     : (isDefaultServiceType
//                         ? "Стоимость озвученная клиенту"
//                         : "Стоимость"),
//                 validator: Validators.number,
//                 keyboardType: TextInputType.number,
//                 controller: price,
//               ),
//             ),
//             Padding(
//               padding: const EdgeInsets.only(bottom: 10),
//               child: InputField(
//                 keyboardType: TextInputType.multiline,
//                 minLines: 3,
//                 maxLines: 100,
//                 hintText: 'Примечание',
//                 controller: description,
//               ),
//             ),
//             Row(
//               mainAxisAlignment: MainAxisAlignment.spaceBetween,
//               children: [
//                 if (widget.isEdit)
//                   Flexible(
//                     child: Button(
//                       label: Text('Удалить'),
//                       style: secondaryButtonStyle,
//                       onPressed: widget.onRemove,
//                     ),
//                   ),
//                 if (widget.isEdit) const SizedBox(width: 10),
//                 Flexible(
//                   child: Button(
//                     label: Text(widget.isEdit ? "Изменить" : "Добавить"),
//                     style: secondaryButtonStyle,
//                     onPressed: _handleSubmit,
//                   ),
//                 )
//               ],
//             ),
//             SizedBox(height: MediaQuery.of(context).viewInsets.bottom),
//           ],
//         ),
//       ),
//     );
//   }
// }

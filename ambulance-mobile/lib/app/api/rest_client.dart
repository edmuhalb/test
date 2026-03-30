import 'dart:io';

import 'package:ambulance/app/api/dto/ambulance_call_list_response.dart';
import 'package:ambulance/app/api/dto/medication_categories_response.dart';
import 'package:ambulance/app/api/dto/medication_product.dart';
import 'package:ambulance/app/api/dto/medication_products_response.dart';
import 'package:ambulance/app/api/dto/order_item.dart';
import 'package:ambulance/app/api/dto/rejection_reason_list_response.dart';
import 'package:ambulance/app/repositories/ambulance_call/ambulance_call.dart';
import 'package:dio/dio.dart' hide Headers;
import 'package:retrofit/retrofit.dart';

import 'dto/ambulance_call_detail.dart';
import 'dto/ambulance_call_update_request.dart';
import 'dto/entity.dart';
import 'dto/remote_file.dart';
import 'dto/shift_detail.dart';
import 'dto/shift_update_request.dart';
import 'dto/touch_to_call_request.dart';
import 'dto/transport_report_detail.dart';

part 'rest_client.g.dart';

@RestApi(baseUrl: "https://reset-m.ru/app/api/v1")
abstract class RestClientV1 {
  factory RestClientV1(Dio dio, {String baseUrl}) = _RestClientV1;

  @POST("/touch-to-call")
  Future<void> touchToCall(@Body() TouchToCallRequest request);

  @MultiPart()
  @POST("/files")
  Future<RemoteFile> uploadFile(
    @Part(name: "file") File file, {
    @SendProgress() ProgressCallback? onSendProgress,
  });

  @DELETE("/files/{id}")
  Future<void> deleteFile(@Path("id") int fileId);

  @GET("/ambulance_calls")
  Future<AmbulanceCallListResponse> getAmbulanceCalls({
    // Standard query params (page, itemsPerPage, pagination, etc.)
    @Query("page") int? page,
    @Query("itemsPerPage") int? itemsPerPage,
    @Query("pagination") bool? pagination,

    // Filtering
    @Query("team.id") int? teamId,
    @Query("team.id[]") List<int>? teamIdIn,
    @Query("status") String? status,
    @Query("status[]") List<String>? statusIn,
    @Query("admin.id") int? adminId,
    @Query("admin.id[]") List<int>? adminIdIn,
    @Query("doctor.id") int? doctorId,
    @Query("doctor.id[]") List<int>? doctorIdIn,
    @Query("partner.id") int? partnerId,
    @Query("partner.id[]") List<int>? partnerIdIn,
    @Query("operator.id") int? operatorId,
    @Query("operator.id[]") List<int>? operatorIdIn,
    @Query("client.id") int? clientId,
    @Query("client.id[]") List<int>? clientIdIn,
    @Query("city.id") int? cityId,
    @Query("city.id[]") List<int>? cityIdIn,
    @Query("employee") String? employee,
    @Query("name") String? name,
    @Query("sendPhone") bool? sendPhone,
    @Query("noBusinessCards") bool? noBusinessCards,
    @Query("partnerHospitalization") bool? partnerHospitalization,
    @Query("personal") bool? personal,
    @Query("doNotHospitalize") bool? doNotHospitalize,

    // Sorting
    @Query("order[createdAt]") String? orderCreatedAt,
    @Query("order[updatedAt]") String? orderUpdatedAt,
    @Query("order[completedAt]") String? orderCompletedAt,
    @Query("order[dateTime]") String? orderDateTime,

    // Date filters
    @Query("dateTime[before]") String? dateTimeBefore,
    @Query("dateTime[strictly_before]") String? dateTimeStrictlyBefore,
    @Query("dateTime[after]") String? dateTimeAfter,
    @Query("dateTime[strictly_after]") String? dateTimeStrictlyAfter,
    @Query("createdAt[before]") String? createdAtBefore,
    @Query("createdAt[strictly_before]") String? createdAtStrictlyBefore,
    @Query("createdAt[after]") String? createdAtAfter,
    @Query("createdAt[strictly_after]") String? createdAtStrictlyAfter,
    @Query("updatedAt[before]") String? updatedAtBefore,
    @Query("updatedAt[strictly_before]") String? updatedAtStrictlyBefore,
    @Query("updatedAt[after]") String? updatedAtAfter,
    @Query("updatedAt[strictly_after]") String? updatedAtStrictlyAfter,
    @Query("completedAt[before]") String? completedAtBefore,
    @Query("completedAt[strictly_before]") String? completedAtStrictlyBefore,
    @Query("completedAt[after]") String? completedAtAfter,
    @Query("completedAt[strictly_after]") String? completedAtStrictlyAfter,
  });

  @GET("/ambulance_calls/{id}")
  Future<AmbulanceCallDetail> getAmbulanceCallDetail(
    @Path("id") int id,
  );

  @PATCH("/ambulance_calls/{id}")
  @Headers(<String, dynamic>{
    'Content-Type': 'application/merge-patch+json',
  })
  Future<AmbulanceCallDetail> patchAmbulanceCall(
    @Path("id") int id,
    @Body() AmbulanceCallUpdateRequest body,
  );

  @PATCH("/shifts/{id}")
  @Headers(<String, dynamic>{
    'Content-Type': 'application/json',
  })
  Future<ShiftDetail> patchShift(
    @Path("id") int id,
    @Body() ShiftUpdateRequest request,
  );

  @GET("/reason_for_cancellations")
  Future<RejectionReasonListResponse> getRejectionReasons({
    @Query("page") int? page,
    @Query("itemsPerPage") int? itemsPerPage,
    @Query("pagination") bool? pagination,
  });
}

extension RestClientX on RestClientV1 {
  Future<ShiftDetail> finishShift({
    required int teamId,
    required TransportReportDetail transportReport,
  }) {
    return patchShift(
      teamId,
      ShiftUpdateRequest(
        status: 'completed',
        transportReport: transportReport,
      ),
    );
  }

  Future<AmbulanceCallDetail> acceptAmbulanceCall(int id) => patchAmbulanceCall(
      id, AmbulanceCallUpdateRequest(status: AmbulanceCallStatus.accepted));

  Future<AmbulanceCallDetail> updateAmbulanceCallToDeparted(
    int id, {
    required DateTime arrivalDateTime,
  }) =>
      patchAmbulanceCall(
        id,
        AmbulanceCallUpdateRequest(
            status: AmbulanceCallStatus.departed,
            arrivalDateTime: arrivalDateTime.toIso8601String()),
      );

  Future<AmbulanceCallDetail> updateAmbulanceCallToArrived(int id) =>
      patchAmbulanceCall(
          id, AmbulanceCallUpdateRequest(status: AmbulanceCallStatus.arrived));

  Future<AmbulanceCallDetail> updateAmbulanceCallToTreating(
    int id, {
    required DateTime endOfServiceDateTime,
    required DateTime dateOfBirth,
    required String patientFullName,
    String? note,
    required String address,
    String? addressInfo,
    String? description,
  }) =>
      patchAmbulanceCall(
        id,
        AmbulanceCallUpdateRequest(
          status: AmbulanceCallStatus.treating,
          endOfServiceDateTime: endOfServiceDateTime.toIso8601String(),
          fio: patientFullName,
          birthday: dateOfBirth.toIso8601String(),
          note: note,
          address: address,
          addressInfo: addressInfo,
          description: addressInfo,
        ),
      );

  Future<AmbulanceCallDetail> updateAmbulanceCallOrder(
    int id, {
    List<OrderItem>? order,
  }) =>
      patchAmbulanceCall(
          id,
          AmbulanceCallUpdateRequest(
            services: order,
          ));

  Future<AmbulanceCallDetail> finishAmbulanceCall(
    int id, {
    String? receiptComment,
    bool? advertised,
  }) =>
      patchAmbulanceCall(
          id,
          AmbulanceCallUpdateRequest(
            status: AmbulanceCallStatus.completed,
            // TOD: implement args patching
            // receiptComment: receiptComment,
            // advertised: advertised,
          ));

  Future<void> rejectAmbulanceCall(
    int id, {
    required Entity rejectionReason,
  }) =>
      patchAmbulanceCall(
        id,
        AmbulanceCallUpdateRequest(
          status: AmbulanceCallStatus.rejected,
          statusLabel: 'Отклонен',
          reasonForCancellation: rejectionReason,
        ),
      );
}

const medicationCategories = [
  {"id": 2, "name": "Ноотропы"},
  {"id": 3, "name": "Наборы"},
  {"id": 1, "name": "Анальгетики"},
];

const medicationProducts = [
  {
    "id": 1,
    "name": "Базовая терапия 5000",
    "categoryId": 3,
    "price": 5000.00,
    "isSet": true,
    "quantity": 1,
    "products": [
      {
        "id": 1,
        "name": "Sol. NaCl 0.09% 250.0 (100.0)",
        "price": 100.00,
        "categoryId": 2,
        "quantity": 1,
        "unitLabel": "1 флак",
      },
      {
        "id": 2,
        "name": "Sol. Thiamini 2ml",
        "categoryId": 2,
        "price": 150.00,
        "quantity": 1,
        "unitLabel": "1 флак",
      },
      {
        "id": 3,
        "name": "Sol. MgSO4 10 ml",
        "categoryId": 2,
        "price": 120.00,
        "quantity": 1,
        "unitLabel": "1 флак",
      },
      {
        "id": 4,
        "name": "Sol. KCL 10ml",
        "categoryId": 2,
        "price": 180.00,
        "quantity": 1,
        "unitLabel": "1 флак",
      },
      {
        "id": 5,
        "name": "Sol. Dexamethasoni 4mg",
        "categoryId": 2,
        "price": 120.00,
        "quantity": 1,
        "unitLabel": "1 флак",
      },
      {
        "id": 6,
        "name": "Tab. Carbamazepini",
        "categoryId": 2,
        "price": 200.00,
        "quantity": 5,
        "unitLabel": "1 флак",
      }
    ]
  },
  {
    "id": 7,
    "name": "Sol. NaCl 0.09% 250.0 (100.0)",
    "categoryId": 2,
    "price": 100.00,
    "unitLabel": "1 флак",
    "quantity": 1
  },
  {
    "id": 8,
    "name": "Sol. Thiamini 2ml",
    "categoryId": 2,
    "price": 150.00,
    "unitLabel": "1 флак",
    "quantity": 1
  },
  {
    "id": 9,
    "name": "Sol. MgSO4 10 ml",
    "categoryId": 2,
    "price": 120.00,
    "unitLabel": "1 флак",
    "quantity": 1
  },
  {
    "id": 10,
    "name": "Sol. KCL 10ml",
    "categoryId": 2,
    "price": 110.00,
    "unitLabel": "1 флак",
    "quantity": 1
  },
  {
    "id": 11,
    "name": "Sol. Dexamethasoni 4mg",
    "categoryId": 2,
    "price": 80.00,
    "unitLabel": "1 флак",
    "quantity": 1
  },
  {
    "id": 12,
    "name": "Tab. Carbamazepini",
    "categoryId": 2,
    "price": 200.00,
    "unitLabel": "1 флак",
    "quantity": 1
  },
  {
    "id": 13,
    "name": "Tab. Chlorprothixeni 15 mg",
    "categoryId": 2,
    "price": 90.00,
    "unitLabel": "1 флак",
    "quantity": 1
  }
];

extension RestClientMock on RestClientV1 {
  Future<MedicationCategoriesResponse> getMedicationCategories({
    @Query("page") int? page,
    @Query("itemsPerPage") int? itemsPerPage,
    @Query("pagination") bool? pagination,
  }) async {
    await Future.delayed(Duration(seconds: 1));

    return MedicationCategoriesResponse(
      items: medicationCategories.map(Entity.fromJson).toList(),
    );
  }

  Future<MedicationProductsResponse> getMedicationProducts({
    @Query("page") int? page,
    @Query("itemsPerPage") int? itemsPerPage,
    @Query("pagination") bool? pagination,
    @Query("categoryId") int? categoryId,
  }) async {
    await Future.delayed(Duration(seconds: 1));

    return categoryId == null
        ? MedicationProductsResponse(
            items: medicationProducts.map(MedicationProduct.fromJson).toList(),
          )
        : MedicationProductsResponse(
            items: medicationProducts
                .where((x) => x["categoryId"] == categoryId)
                .map(MedicationProduct.fromJson)
                .toList(),
          );
  }
}

// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'rest_client.dart';

// **************************************************************************
// RetrofitGenerator
// **************************************************************************

// ignore_for_file: unnecessary_brace_in_string_interps,no_leading_underscores_for_local_identifiers,unused_element,unnecessary_string_interpolations

class _RestClientV1 implements RestClientV1 {
  _RestClientV1(this._dio, {this.baseUrl, this.errorLogger}) {
    baseUrl ??= 'https://reset-m.ru/app/api/v1';
  }

  final Dio _dio;

  String? baseUrl;

  final ParseErrorLogger? errorLogger;

  @override
  Future<void> touchToCall(TouchToCallRequest request) async {
    final _extra = <String, dynamic>{};
    final queryParameters = <String, dynamic>{};
    final _headers = <String, dynamic>{};
    final _data = <String, dynamic>{};
    _data.addAll(request.toJson());
    final _options = _setStreamType<void>(
      Options(method: 'POST', headers: _headers, extra: _extra)
          .compose(
            _dio.options,
            '/touch-to-call',
            queryParameters: queryParameters,
            data: _data,
          )
          .copyWith(baseUrl: _combineBaseUrls(_dio.options.baseUrl, baseUrl)),
    );
    await _dio.fetch<void>(_options);
  }

  @override
  Future<RemoteFile> uploadFile(
    File file, {
    void Function(int, int)? onSendProgress,
  }) async {
    final _extra = <String, dynamic>{};
    final queryParameters = <String, dynamic>{};
    queryParameters.removeWhere((k, v) => v == null);
    final _headers = <String, dynamic>{};
    final _data = FormData();
    _data.files.add(
      MapEntry(
        'file',
        MultipartFile.fromFileSync(
          file.path,
          filename: file.path.split(Platform.pathSeparator).last,
        ),
      ),
    );
    final _options = _setStreamType<RemoteFile>(
      Options(
        method: 'POST',
        headers: _headers,
        extra: _extra,
        contentType: 'multipart/form-data',
      )
          .compose(
            _dio.options,
            '/files',
            queryParameters: queryParameters,
            data: _data,
            onSendProgress: onSendProgress,
          )
          .copyWith(baseUrl: _combineBaseUrls(_dio.options.baseUrl, baseUrl)),
    );
    final _result = await _dio.fetch<Map<String, dynamic>>(_options);
    late RemoteFile _value;
    try {
      _value = RemoteFile.fromJson(_result.data!);
    } on Object catch (e, s) {
      errorLogger?.logError(e, s, _options);
      rethrow;
    }
    return _value;
  }

  @override
  Future<void> deleteFile(int fileId) async {
    final _extra = <String, dynamic>{};
    final queryParameters = <String, dynamic>{};
    final _headers = <String, dynamic>{};
    const Map<String, dynamic>? _data = null;
    final _options = _setStreamType<void>(
      Options(method: 'DELETE', headers: _headers, extra: _extra)
          .compose(
            _dio.options,
            '/files/${fileId}',
            queryParameters: queryParameters,
            data: _data,
          )
          .copyWith(baseUrl: _combineBaseUrls(_dio.options.baseUrl, baseUrl)),
    );
    await _dio.fetch<void>(_options);
  }

  @override
  Future<AmbulanceCallListResponse> getAmbulanceCalls({
    int? page,
    int? itemsPerPage,
    bool? pagination,
    int? teamId,
    List<int>? teamIdIn,
    String? status,
    List<String>? statusIn,
    int? adminId,
    List<int>? adminIdIn,
    int? doctorId,
    List<int>? doctorIdIn,
    int? partnerId,
    List<int>? partnerIdIn,
    int? operatorId,
    List<int>? operatorIdIn,
    int? clientId,
    List<int>? clientIdIn,
    int? cityId,
    List<int>? cityIdIn,
    String? employee,
    String? name,
    bool? sendPhone,
    bool? noBusinessCards,
    bool? partnerHospitalization,
    bool? personal,
    bool? doNotHospitalize,
    String? orderCreatedAt,
    String? orderUpdatedAt,
    String? orderCompletedAt,
    String? orderDateTime,
    String? dateTimeBefore,
    String? dateTimeStrictlyBefore,
    String? dateTimeAfter,
    String? dateTimeStrictlyAfter,
    String? createdAtBefore,
    String? createdAtStrictlyBefore,
    String? createdAtAfter,
    String? createdAtStrictlyAfter,
    String? updatedAtBefore,
    String? updatedAtStrictlyBefore,
    String? updatedAtAfter,
    String? updatedAtStrictlyAfter,
    String? completedAtBefore,
    String? completedAtStrictlyBefore,
    String? completedAtAfter,
    String? completedAtStrictlyAfter,
  }) async {
    final _extra = <String, dynamic>{};
    final queryParameters = <String, dynamic>{
      r'page': page,
      r'itemsPerPage': itemsPerPage,
      r'pagination': pagination,
      r'team.id': teamId,
      r'team.id[]': teamIdIn,
      r'status': status,
      r'status[]': statusIn,
      r'admin.id': adminId,
      r'admin.id[]': adminIdIn,
      r'doctor.id': doctorId,
      r'doctor.id[]': doctorIdIn,
      r'partner.id': partnerId,
      r'partner.id[]': partnerIdIn,
      r'operator.id': operatorId,
      r'operator.id[]': operatorIdIn,
      r'client.id': clientId,
      r'client.id[]': clientIdIn,
      r'city.id': cityId,
      r'city.id[]': cityIdIn,
      r'employee': employee,
      r'name': name,
      r'sendPhone': sendPhone,
      r'noBusinessCards': noBusinessCards,
      r'partnerHospitalization': partnerHospitalization,
      r'personal': personal,
      r'doNotHospitalize': doNotHospitalize,
      r'order[createdAt]': orderCreatedAt,
      r'order[updatedAt]': orderUpdatedAt,
      r'order[completedAt]': orderCompletedAt,
      r'order[dateTime]': orderDateTime,
      r'dateTime[before]': dateTimeBefore,
      r'dateTime[strictly_before]': dateTimeStrictlyBefore,
      r'dateTime[after]': dateTimeAfter,
      r'dateTime[strictly_after]': dateTimeStrictlyAfter,
      r'createdAt[before]': createdAtBefore,
      r'createdAt[strictly_before]': createdAtStrictlyBefore,
      r'createdAt[after]': createdAtAfter,
      r'createdAt[strictly_after]': createdAtStrictlyAfter,
      r'updatedAt[before]': updatedAtBefore,
      r'updatedAt[strictly_before]': updatedAtStrictlyBefore,
      r'updatedAt[after]': updatedAtAfter,
      r'updatedAt[strictly_after]': updatedAtStrictlyAfter,
      r'completedAt[before]': completedAtBefore,
      r'completedAt[strictly_before]': completedAtStrictlyBefore,
      r'completedAt[after]': completedAtAfter,
      r'completedAt[strictly_after]': completedAtStrictlyAfter,
    };
    queryParameters.removeWhere((k, v) => v == null);
    final _headers = <String, dynamic>{};
    const Map<String, dynamic>? _data = null;
    final _options = _setStreamType<AmbulanceCallListResponse>(
      Options(method: 'GET', headers: _headers, extra: _extra)
          .compose(
            _dio.options,
            '/ambulance_calls',
            queryParameters: queryParameters,
            data: _data,
          )
          .copyWith(baseUrl: _combineBaseUrls(_dio.options.baseUrl, baseUrl)),
    );
    final _result = await _dio.fetch<Map<String, dynamic>>(_options);
    late AmbulanceCallListResponse _value;
    try {
      _value = AmbulanceCallListResponse.fromJson(_result.data!);
    } on Object catch (e, s) {
      errorLogger?.logError(e, s, _options);
      rethrow;
    }
    return _value;
  }

  @override
  Future<AmbulanceCallDetail> getAmbulanceCallDetail(int id) async {
    final _extra = <String, dynamic>{};
    final queryParameters = <String, dynamic>{};
    final _headers = <String, dynamic>{};
    const Map<String, dynamic>? _data = null;
    final _options = _setStreamType<AmbulanceCallDetail>(
      Options(method: 'GET', headers: _headers, extra: _extra)
          .compose(
            _dio.options,
            '/ambulance_calls/${id}',
            queryParameters: queryParameters,
            data: _data,
          )
          .copyWith(baseUrl: _combineBaseUrls(_dio.options.baseUrl, baseUrl)),
    );
    final _result = await _dio.fetch<Map<String, dynamic>>(_options);
    late AmbulanceCallDetail _value;
    try {
      _value = AmbulanceCallDetail.fromJson(_result.data!);
    } on Object catch (e, s) {
      errorLogger?.logError(e, s, _options);
      rethrow;
    }
    return _value;
  }

  @override
  Future<AmbulanceCallDetail> patchAmbulanceCall(
    int id,
    AmbulanceCallUpdateRequest body,
  ) async {
    final _extra = <String, dynamic>{};
    final queryParameters = <String, dynamic>{};
    final _headers = <String, dynamic>{
      r'Content-Type': 'application/merge-patch+json',
    };
    _headers.removeWhere((k, v) => v == null);
    final _data = <String, dynamic>{};
    _data.addAll(body.toJson());
    final _options = _setStreamType<AmbulanceCallDetail>(
      Options(
        method: 'PATCH',
        headers: _headers,
        extra: _extra,
        contentType: 'application/merge-patch+json',
      )
          .compose(
            _dio.options,
            '/ambulance_calls/${id}',
            queryParameters: queryParameters,
            data: _data,
          )
          .copyWith(baseUrl: _combineBaseUrls(_dio.options.baseUrl, baseUrl)),
    );
    final _result = await _dio.fetch<Map<String, dynamic>>(_options);
    late AmbulanceCallDetail _value;
    try {
      _value = AmbulanceCallDetail.fromJson(_result.data!);
    } on Object catch (e, s) {
      errorLogger?.logError(e, s, _options);
      rethrow;
    }
    return _value;
  }

  @override
  Future<ShiftDetail> patchShift(int id, ShiftUpdateRequest request) async {
    final _extra = <String, dynamic>{};
    final queryParameters = <String, dynamic>{};
    final _headers = <String, dynamic>{r'Content-Type': 'application/json'};
    _headers.removeWhere((k, v) => v == null);
    final _data = <String, dynamic>{};
    _data.addAll(request.toJson());
    final _options = _setStreamType<ShiftDetail>(
      Options(
        method: 'PATCH',
        headers: _headers,
        extra: _extra,
        contentType: 'application/json',
      )
          .compose(
            _dio.options,
            '/shifts/${id}',
            queryParameters: queryParameters,
            data: _data,
          )
          .copyWith(baseUrl: _combineBaseUrls(_dio.options.baseUrl, baseUrl)),
    );
    final _result = await _dio.fetch<Map<String, dynamic>>(_options);
    late ShiftDetail _value;
    try {
      _value = ShiftDetail.fromJson(_result.data!);
    } on Object catch (e, s) {
      errorLogger?.logError(e, s, _options);
      rethrow;
    }
    return _value;
  }

  @override
  Future<RejectionReasonListResponse> getRejectionReasons({
    int? page,
    int? itemsPerPage,
    bool? pagination,
  }) async {
    final _extra = <String, dynamic>{};
    final queryParameters = <String, dynamic>{
      r'page': page,
      r'itemsPerPage': itemsPerPage,
      r'pagination': pagination,
    };
    queryParameters.removeWhere((k, v) => v == null);
    final _headers = <String, dynamic>{};
    const Map<String, dynamic>? _data = null;
    final _options = _setStreamType<RejectionReasonListResponse>(
      Options(method: 'GET', headers: _headers, extra: _extra)
          .compose(
            _dio.options,
            '/reason_for_cancellations',
            queryParameters: queryParameters,
            data: _data,
          )
          .copyWith(baseUrl: _combineBaseUrls(_dio.options.baseUrl, baseUrl)),
    );
    final _result = await _dio.fetch<Map<String, dynamic>>(_options);
    late RejectionReasonListResponse _value;
    try {
      _value = RejectionReasonListResponse.fromJson(_result.data!);
    } on Object catch (e, s) {
      errorLogger?.logError(e, s, _options);
      rethrow;
    }
    return _value;
  }

  RequestOptions _setStreamType<T>(RequestOptions requestOptions) {
    if (T != dynamic &&
        !(requestOptions.responseType == ResponseType.bytes ||
            requestOptions.responseType == ResponseType.stream)) {
      if (T == String) {
        requestOptions.responseType = ResponseType.plain;
      } else {
        requestOptions.responseType = ResponseType.json;
      }
    }
    return requestOptions;
  }

  String _combineBaseUrls(String dioBaseUrl, String? baseUrl) {
    if (baseUrl == null || baseUrl.trim().isEmpty) {
      return dioBaseUrl;
    }

    final url = Uri.parse(baseUrl);

    if (url.isAbsolute) {
      return url.toString();
    }

    return Uri.parse(dioBaseUrl).resolveUri(url).toString();
  }
}

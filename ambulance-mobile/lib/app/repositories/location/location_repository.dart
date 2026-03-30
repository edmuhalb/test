import 'dart:math';

import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:get_it/get_it.dart';
import 'package:location/location.dart';
import 'package:ambulance/app/repositories/location/location.dart';

import '../../app_preferences.dart';

class LocationRepository implements AbstractLocationRepository {
  final Dio dio;
  Location location = Location();
  double? _centerLongitude;
  double? _centerLatitude;

  LocationRepository({
    required this.dio,
  });

  @override
  Future<void> listenUserPosition() async {
    bool isReady = await _isReady(location);

    if (!isReady) {
      return;
    }

    final appPreferences = GetIt.I<AppPreferences>();

    location.onLocationChanged.listen((LocationData event) async {
      double? lon = event.longitude;
      double? lat = event.latitude;

      if (lon != null && lat != null) {
        bool isUserInsideCircle = _userInsideCircle(lon, lat);
        final teamId = appPreferences.teamId;

        if (!isUserInsideCircle && teamId != null) {
          String longitude = lon.toString();
          String latitude = lat.toString();

          await _changeUserPosition(teamId, latitude, longitude);
          _setLatAndLon(lat, lon);
        }
      }
    });
  }

  Future<void> _changeUserPosition(
    int id,
    String lat,
    String lon,
  ) async {
    try {
      await dio.post('team-locations', data: {
        "medTeam": '/api/med_teams/$id',
        "lat": lat,
        "lon": lon,
      });
    } catch (e) {
      debugPrint("ERROR in Location -> _changeUserPosition");
    }
  }

  bool _userInsideCircle(double lon, double lat) {
    double circleRadius = 10.0;

    if (_centerLongitude == null && _centerLatitude == null) {
      _setLatAndLon(lat, lon);
      return false;
    }

    double x = lon - _centerLongitude!;
    double y = lat - _centerLatitude!;
    double distance = sqrt(pow(x, 2) + pow(y, 2));

    if (distance > circleRadius) {
      _setLatAndLon(lat, lon);
      return false;
    }

    return true;
  }

  Future<bool> _isReady(Location locationInstance) async {
    bool serviceEnabled = await locationInstance.serviceEnabled();

    if (!serviceEnabled) {
      serviceEnabled = await locationInstance.requestService();

      if (!serviceEnabled) return false;
    }

    PermissionStatus permissionGranted = await locationInstance.hasPermission();

    if (permissionGranted == PermissionStatus.denied) {
      permissionGranted = await locationInstance.requestPermission();

      if (permissionGranted != PermissionStatus.granted) return false;
    }

    locationInstance.enableBackgroundMode(enable: true);
    locationInstance.changeSettings(
      accuracy: LocationAccuracy.high,
      interval: 1000,
      distanceFilter: 0,
    );

    return true;
  }

  void _setLatAndLon(double lat, double lon) {
    _centerLongitude = lon;
    _centerLatitude = lat;
  }
}

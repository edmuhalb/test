import 'package:ambulance/app/repositories/user/user.dart';
import 'package:dio/dio.dart';

class UserRepository implements AbstractUserRepository {
  final Dio dio;

  UserRepository({required this.dio});

  @override
  Future<User> getUser(String userId) async {
    final response = await dio.get('users/$userId');

    final data = response.data as Map<String, dynamic>;

    return User.fromJson(data);
  }
}

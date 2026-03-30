import 'package:ambulance/app/repositories/user/user.dart';

abstract class AbstractUserRepository {
  Future<User>  getUser(String userId);
}
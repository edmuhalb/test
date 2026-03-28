# Сброс пароля — инструкция по установке на бэкенд

## 1. Скопировать файлы

```
Entity/PasswordResetCode.php     → api/src/Entity/PasswordResetCode.php
Service/SmsRuService.php         → api/src/Service/SmsRuService.php
Controller/PartnerApi/PasswordResetController.php → api/src/Controller/PartnerApi/PasswordResetController.php
```

## 2. Добавить переменную окружения

В `api/.env`:
```
SMSRU_API_ID=your_sms_ru_api_id_here
```

## 3. Зарегистрировать SmsRuService в services.yaml

В `api/config/services.yaml` добавить:
```yaml
    App\Service\SmsRuService:
        arguments:
            $smsRuApiId: '%env(SMSRU_API_ID)%'
```

## 4. Открыть маршруты в security.yaml

В `api/config/packages/security.yaml` в секции `access_control` ПЕРЕД
строкой `- { path: ^/partner, roles: IS_AUTHENTICATED_FULLY }` добавить:

```yaml
    - { path: ^/partner/forgot-password, roles: PUBLIC_ACCESS }
    - { path: ^/partner/reset-password, roles: PUBLIC_ACCESS }
```

## 5. Создать миграцию

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## API

### POST /partner/forgot-password
```json
{ "phone": "79991234567" }
```
Response: `{ "success": true }`

### POST /partner/reset-password
```json
{ "phone": "79991234567", "code": "1234", "password": "newpassword" }
```
Response: `{ "success": true }`

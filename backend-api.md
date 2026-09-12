# قرارداد اولیه API سامانه ادب

پایه API پیشنهادی برای اتصال رابط کاربری فعلی به بک‌اند واقعی.

## احراز هویت

### درخواست کد ورود یا ثبت‌نام

`POST /api/auth/request-otp`

```json
{
  "mobile": "09121234567",
  "purpose": "signup"
}
```

پاسخ موفق:

```json
{
  "ok": true,
  "message": "کد تأیید ارسال شد.",
  "expires_in": 120
}
```

### تأیید کد

`POST /api/auth/verify-otp`

```json
{
  "mobile": "09121234567",
  "code": "1234",
  "purpose": "signup"
}
```

پاسخ موفق:

```json
{
  "ok": true,
  "authenticated": true,
  "next": "complete-profile",
  "user": {
    "id": 12,
    "membership_status": "incomplete"
  }
}
```

## پروفایل و عضویت

### تکمیل پروفایل

`PUT /api/me/profile`

فیلدهای اصلی: `full_name`, `national_code`, `graduation_year`, `field_of_study`, `school`, `city`, `email`.

پس از ثبت، وضعیت کاربر به `pending` تغییر می‌کند.

### وضعیت عضویت

`GET /api/me/membership`

مقادیر وضعیت: `incomplete`, `pending`, `approved`, `revision`, `rejected`.

## مدیریت

### فهرست اعضای در انتظار بررسی

`GET /api/admin/members?status=pending`

### تصمیم مدیر

`POST /api/admin/members/{id}/review`

```json
{
  "decision": "approved",
  "note": "اطلاعات بررسی و تأیید شد."
}
```

تمام عملیات مدیریتی باید در `audit_logs` ثبت شوند و فقط برای کاربران دارای نقش `admin` قابل دسترسی باشند.

# راه‌اندازی API

1. از `config.example.php` یک فایل به نام `config.php` بسازید.
2. مقادیر دیتابیس و سرویس پیامک را فقط روی سرور وارد کنید؛ این فایل نباید در GitHub commit شود.
3. فایل `database-schema.sql` را روی MySQL اجرا کنید.
4. مسیر `api/` را روی PHP 8.1+ قرار دهید.
5. سرویس پیامک واقعی را در `auth/request-otp.php` متصل کنید.

## مسیرهای تکمیل‌شده

- `PUT /api/me/profile`
- `GET /api/me/membership`
- `GET /api/admin/members?status=pending`
- `POST /api/admin/review.php?user_id=12`

تا زمانی که این API روی هاست PHP اجرا نشود، GitHub Pages فقط نسخه نمایشی HTML را نمایش می‌دهد.

# 🫒 نظام إدارة معصرة الزيتون

نظام متكامل لإدارة معصرة الزيتون — مبني بـ Laravel 10

---

## 📋 المميزات

- ✅ تسجيل أصحاب الزيتون (الاسم، المدينة، الشارع، الهاتف، رقم الدور)
- ✅ إنشاء إيصالات استلام مع حساب الأجرة تلقائياً
- ✅ تتبع حالة كل شحنة (انتظار / معالجة / مكتمل)
- ✅ تحديث كمية الزيت المستخرج من الكاشير
- ✅ طباعة إيصالات (نسخة صاحب الزيتون + نسخة الكاشير)
- ✅ تصدير إيصالات PDF
- ✅ تقارير يومية وشهرية مع فلاتر
- ✅ لوحة تحكم مع إحصائيات شاملة
- ✅ واجهة عربية كاملة RTL
- ✅ إعدادات قابلة للتخصيص

---

## ⚙️ متطلبات التشغيل

- PHP 8.1 أو أحدث
- Composer
- MySQL أو MariaDB
- Web Server (Apache أو Nginx)

---

## 🚀 خطوات التثبيت

### الخطوة 1 — استخراج الملفات
فك ضغط الملف في مجلد المشاريع لديك (مثلاً `C:\xampp\htdocs\olive-press` أو `/var/www/html/olive-press`)

### الخطوة 2 — تثبيت المكتبات
افتح Command Prompt / Terminal في مجلد المشروع وشغّل:
```bash
composer install
```

### الخطوة 3 — إعداد ملف البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### الخطوة 4 — إعداد قاعدة البيانات
1. افتح phpMyAdmin أو أي أداة MySQL
2. أنشئ قاعدة بيانات جديدة باسم `olive_press`
3. عدّل ملف `.env`:
```
DB_DATABASE=olive_press
DB_USERNAME=root
DB_PASSWORD=كلمة_المرور_إن_وجدت
```

### الخطوة 5 — تشغيل الـ Migrations
```bash
php artisan migrate
```

### الخطوة 6 — تشغيل المشروع
```bash
php artisan serve
```
ثم افتح المتصفح على: **http://localhost:8000**

---

## 🖥️ التشغيل على XAMPP

1. ضع المجلد في `C:\xampp\htdocs\olive-press`
2. شغّل Apache و MySQL من XAMPP Control Panel
3. اتبع الخطوات 2-5 من أعلاه
4. افتح: `http://localhost/olive-press/public`

---

## 📱 شاشات النظام

| الشاشة | الوصف |
|--------|-------|
| `/` | لوحة التحكم الرئيسية |
| `/owners` | قائمة أصحاب الزيتون |
| `/owners/create` | إضافة صاحب جديد |
| `/receipts` | قائمة الإيصالات |
| `/receipts/create` | إيصال جديد |
| `/receipts/{id}/print` | طباعة الإيصال |
| `/receipts/{id}/pdf` | تحميل PDF |
| `/reports` | التقارير |
| `/settings` | الإعدادات |

---

## 🔧 حل المشاكل الشائعة

**مشكلة الصلاحيات:**
```bash
chmod -R 775 storage bootstrap/cache
```

**مشكلة APP_KEY:**
```bash
php artisan key:generate
```

**تحديث composer:**
```bash
composer update
```

---

## 📞 الدعم

للمساعدة في التثبيت أو الإضافات، تواصل مع المطور.

---
*نظام إدارة معصرة الزيتون — الإصدار 1.0.0*

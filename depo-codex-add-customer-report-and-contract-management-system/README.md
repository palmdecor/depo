# Depo Müşteri Rapor ve Sözleşme Sistemi

Laravel yerine sade PHP 8 kullanılarak geliştirilen bu uygulama, depo müşterileri için rapor paylaşımı ve sözleşme yönetimi sağlar. Sistem SQLite veritabanını ve mPDF kütüphanesini kullanarak dinamik PDF üretimi sunar.

## Başlıca Özellikler

- Müşteri kayıt ve giriş akışı
- Şifre güncelleme ve hesap engelleme kontrolü
- Yönetici panelinde müşteri arama, rapor yükleme ve sözleşme şablonu yönetimi
- Müşteri panelinde rapor görüntüleme/indirme ve sözleşme doldurarak PDF indirme
- Sadece PDF kabul eden rapor yükleme altyapısı (`storage/reports/{customer_id}`)
- CSRF koruması, modern şifre hashleme ve giriş kısıtlamaları

## Gereksinimler

- PHP 8.1+
- `pdo_sqlite` uzantısı etkin PHP
- Composer (mPDF kütüphanesi için)

## Kurulum Adımları

1. Bağımlılıkları yükleyin:
   ```bash
   composer install
   ```
2. Ortam dosyasını oluşturun:
   ```bash
   cp .env.example .env
   ```
3. Veritabanını ve tabloları oluşturun:
   ```bash
   php scripts/migrate.php
   ```
4. Opsiyonel olarak yönetici hesabı ekleyin:
   ```bash
   php scripts/seed_admin.php
   ```
5. Uygulamayı başlatın:
   ```bash
   php -S localhost:8000 -t public
   ```

## Kullanım Notları

- Yönetici kullanıcıları veritabanına manuel eklenebilir veya `seed_admin.php` betiği kullanılabilir.
- Sözleşme şablonu dinamik alanları: `{{first_name}}`, `{{last_name}}`, `{{full_name}}`, `{{address}}`, `{{national_id}}`, `{{phone}}`, `{{email}}`, `{{current_date}}`.
- Raporlar sadece PDF olarak yüklenebilir; dosyalar müşteri bazlı klasörlerde saklanır.
- mPDF kullanılmadan sözleşme PDF’i oluşturulamaz; eksikse `composer install` komutu ile kurulmalıdır.

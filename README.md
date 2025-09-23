# Depo Müşteri Rapor ve Sözleşme Sistemi

Bu depo, Laravel yerine sade PHP kullanılarak geliştirilen deneysel bir prototip içerir. Prototip, kullanıcı kayıt/giriş ve basit müşteri paneli özellikleri ile sistemin temelini oluşturur.

## Gereksinimler
- PHP 8.1+
- SQLite uzantısı etkin PHP

## Kurulum
1. Ortam dosyasını oluşturun:
   ```bash
   cp .env.example .env
   ```
2. Veritabanı migrasyonlarını çalıştırın:
   ```bash
   php scripts/migrate.php
   ```
3. İsteğe bağlı olarak yönetici kullanıcısı seed edin:
   ```bash
   php scripts/seed_admin.php
   ```
4. PHP yerleşik sunucusu ile uygulamayı başlatın:
   ```bash
   php -S localhost:8000 -t public
   ```

## Özellikler
- Kullanıcı kayıt ve giriş akışı
- SQLite tabanlı kullanıcı yönetimi
- Basit müşteri paneli

Geliştirme ilerledikçe sözleşme yönetimi, rapor yükleme ve yönetici paneli gibi özellikler eklenecektir.

# Depo Müşteri Rapor ve Sözleşme Sistemi

Bu depo, Laravel yerine sade PHP kullanılarak geliştirilen deneysel bir prototip içerir. Prototip, kullanıcı kayıt/giriş ve temel müşteri paneli özellikleri ile sistemin temelini oluşturur.

## Gereksinimler
- PHP 7.4+
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

5. Domain ve hosting hatırlatma e-postalarını tetiklemek için (örn. cron ile) aşağıdaki komutu çalıştırın:
    ```bash
    php scripts/send_reminders.php
    ```
    Varsayılan olarak hatırlatmalar `storage/logs/reminders.log` dosyasına yazılır. `.env` dosyasında `MAIL_TRANSPORT=mail` ayarlanarak sistem e-postası gönderimi etkinleştirilebilir.

## Özellikler
- Kullanıcı kayıt ve giriş akışı
- SQLite tabanlı kullanıcı yönetimi
- Müşteri ad-soyad, e-posta, telefon, domain ve hosting hizmetleri ile yenileme tarihleri, periyotları ve fiyatlarını içeren müşteri yönetim paneli
- Yaklaşan yenilemeleri gösteren dashboard ve rapor görünümü
- Cron ile çalıştırılabilen hatırlatma e-postası/log sistemi

## Hatırlatma Komutu
`php scripts/send_reminders.php` komutu, ön tanımlı gün sayısı içerisinde (varsayılan 7) domain veya hosting hizmeti yenilenmesi gereken müşterileri listeleyerek e-posta/log çıktısı üretir. Komut, her hizmet için yenileme süresi ve fiyat bilgisini de ekler.

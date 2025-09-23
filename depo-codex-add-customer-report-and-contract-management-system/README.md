# Depo Müşteri Rapor ve Sözleşme Sistemi

Bu proje, herhangi bir framework kullanmadan PHP 8 ile geliştirilmiş tam özellikli bir müşteri/rapor yönetim uygulamasıdır. Sistem; müşteri kayıt ve oturum açma, sözleşme doldurma ve PDF çıktısı alma, yönetici paneli üzerinden rapor yükleme ve müşteri yönetimi gibi gereksinimleri karşılar.

## Teknolojiler
- PHP 8.1+
- PDO (MySQL / MariaDB veya SQLite)
- HTML5, Bootstrap 5
- [mPDF](https://mpdf.github.io/) (DejaVu Sans fontu ile PDF üretimi)

## Kurulum
1. Bağımlılıkları yükleyin:
   ```bash
   composer install
   ```
2. Ortam dosyasını oluşturun ve veritabanı bilgilerini güncelleyin:
   ```bash
   cp .env.example .env
   ```
   - `DB_CONNECTION` alanına `mysql` veya `sqlite` değerini girin.
   - MySQL kullanacaksanız `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` alanlarını doldurun.
   - SQLite için varsayılan `storage/database.sqlite` konumu kullanılabilir.
3. Veritabanı tablolarını oluşturun:
   ```bash
   php scripts/migrate.php
   ```
4. Yönetici hesabı oluşturmak için (isteğe bağlı):
   ```bash
   php scripts/seed_admin.php
   ```
5. Uygulamayı geliştirme modunda çalıştırın:
   ```bash
   php -S localhost:8000 -t public
   ```

## Özellikler
### Müşteri Paneli
- Kayıt olma ve giriş yapma
- Rapor listesini görüntüleme ve PDF indirme
- Şifre güncelleme
- Yönetici tarafından sağlanan şablonla sözleşme doldurma ve PDF çıktısı alma

### Yönetici Paneli
- Müşterileri listeleme ve ada/soyada/telefona göre arama
- Müşteri hesaplarını engelleme veya aktifleştirme
- Her müşteri için PDF raporu yükleme (yalnızca `.pdf` dosyaları kabul edilir)
- Müşteri sözleşme geçmişini görüntüleme
- Dinamik sözleşme şablonu ekleme/güncelleme ({{first_name}}, {{last_name}}, {{address}}, {{date}} vb. alan destekleri)

### Güvenlik
- `password_hash()` (BCRYPT) ile şifre saklama
- PDO prepared statements ile SQL Injection koruması
- CSRF token doğrulaması
- XSS’e karşı form verilerinde `htmlspecialchars`
- Engellenmiş kullanıcıların oturum açmasının engellenmesi

### Dosya Yönetimi
- Rapor yüklemeleri `public/uploads/reports/{customer_id}` klasöründe saklanır
- Sözleşme PDF çıktıları `public/uploads/contracts/{customer_id}` klasörüne kaydedilir
- Yalnızca PDF uzantısındaki dosyalara izin verilir

## Geliştirme
- `composer install` komutu mPDF dahil gerekli bağımlılıkları kurar.
- Yeni sözleşme şablonu oluştururken yukarıdaki placeholder değişkenlerini kullanabilirsiniz.
- Oturumlar ve mPDF geçici dosyaları `storage/framework` klasöründe tutulur.

## Lisans
Bu depo eğitim amaçlı hazırlanmıştır.

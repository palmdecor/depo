# DataHub

DataHub, üyelerin data yükleyebildiği, admin onayından geçen ve operatör firmaların satın alabildiği PHP tabanlı bir veri pazar yeridir.

## Gereksinimler

- PHP 8.0+
- MySQL 5.7+
- Composer
- PHP uzantıları: `pdo_mysql`, `mbstring`, `zip`

## Kurulum

1. Depoyu web sunucunuzun `http://localhost/datahub/` adresine denk gelecek şekilde klonlayın.
2. Proje kök dizininde bağımlılıkları yükleyin:

   ```bash
   composer install
   ```

3. `config.php` dosyasında veritabanı bilgilerinizi düzenleyin.
4. `database.sql` dosyasını MySQL sunucunuzda çalıştırın. Varsayılan admin kullanıcı bilgileri:
   - E-posta: `admin@datahub.local`
   - Şifre: `Admin123!`
5. Web sunucunuzda `mod_rewrite` modülünün açık olduğundan emin olun.
6. Tarayıcınızda `http://localhost/datahub/` adresini açın.

## Özellikler

- Güvenli giriş/kayıt sistemi (`password_hash`).
- Üye paneli: Excel (PhpSpreadsheet) ile data yükleme, kazanç görüntüleme.
- Admin paneli: Kullanıcı yönetimi, bakiye yükleme, komisyon oranı ayarı, data onaylama.
- Operatör paneli: Bakiye görüntüleme, data satın alma, maskeleme (05XX XXX 12 34).
- Satın alma işlemleri PDO transaction ile güvence altına alınır.

## Notlar

- `vendor/` klasörü Composer tarafından oluşturulur. Yükleme sonrası sunucuya kopyalamayı unutmayın.
- Excel dosyaları varsayılan olarak `Ad`, `Soyad`, `Telefon`, `Kategori` sütunlarını içermelidir.
- Varsayılan kayıt fiyatı `record_price` ayarı üzerinden değiştirilebilir (`settings` tablosu).

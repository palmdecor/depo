# Depo Paneli

PHP, MySQL ve basit bir ön yüz ile hazırlanmış müşteri-yönetici yönetim paneli. Sistem hem masaüstü hem de mobil cihazlara uyumlu olacak şekilde responsive tasarım ile geliştirildi.

## Özellikler

- Güvenli müşteri kayıt ve giriş sistemi (SHA-512 + rastgele tuz ile şifre saklama)
- Yönetici ve müşteri için farklı yetkiler
- Müşteriler için: Anasayfa, PDF raporları görüntüleme, sözleşme doldurma/indirme ve şifre güncelleme alanları
- Yönetici için: Müşteri arama, rapor yükleme, sözleşme şablonu düzenleme ve müşteri hesabını engelleme/aktifleştirme işlemleri
- PDF raporları yükleme ve müşterilerin indirmesi için saklama alanı
- Modern ve responsive arayüz

## Kurulum

1. Depoyu sunucunuza veya yerel geliştirme ortamınıza kopyalayın.
2. `database.sql` dosyasını MySQL sunucunuza aktararak tabloları oluşturun ve varsayılan yöneticiyi ekleyin.
3. Web sunucunuzun kök dizinini bu projeye yönlendirin (örn. `public_html` içerisine).
4. PHP yapılandırmasında dosya yükleme için `uploads/` klasörünün yazılabilir olduğundan emin olun.
5. Gerekirse veritabanı bağlantı bilgilerini `.env` dosyasına veya sunucu yapılandırmanıza ekleyin:

```
DB_HOST=127.0.0.1
DB_NAME=depo_app
DB_USER=root
DB_PASS=parolaniz
DB_PORT=3306
```

Bu değişkenler otomatik olarak `config.php` tarafından algılanacaktır.

## Varsayılan Yönetici Bilgileri

- E-posta: `admin@example.com`
- Şifre: `Admin123!`

Güvenlik amacıyla ilk giriş sonrasında şifreyi güncellemeniz önerilir.

## Kullanım

- Müşteriler kayıt formu ile sisteme dahil olabilir ve giriş sonrası raporlarını görebilir, şifrelerini değiştirebilir.
- Yönetici paneli, müşteri listesini arama alanı ile filtreleyebilir. Her müşteri için detay sayfasından PDF raporu yüklenebilir ve müşteri engellenip/aktifleştirilebilir.
- "Sözleşme Şablonu" bölümünden sözleşme metni düzenlenebilir; müşteri bilgileri [[MUSTERI_BILGILERI]] etiketi ile sözleşmeye otomatik olarak eklenir.

## Geliştirme Notları

- Dosya yükleme işlemi sadece PDF türüne izin verir ve maksimum 10 MB boyutundadır.
- Rapor dosyaları `uploads/` klasörü altında saklanır.
- Şifreler SHA-512 ve rastgele 64 karakterlik tuz ile saklanır.

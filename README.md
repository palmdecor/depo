# Kredi.ltd AI Kredi Asistanı (PHP 8.1 Uyumlu)

Bu depo, kredi.ltd AI kredi analiz ve sohbet asistanının saf PHP (7.4/8.1 uyumlu) sürümünü içerir. Uygulama klasik paylaşımlı Linux + Nginx barındırma ortamlarında çalışacak şekilde tasarlanmıştır ve herhangi bir framework veya paket yöneticisi gerektirmez.

## Özellikler

- Kullanıcı kayıt ve giriş işlemleri (JWT + PHP oturumu)
- Kredi analizi formu ve AI tabanlı öneriler
- OpenAI Chat Completion API üzerinden sohbet asistanı
- Mock banka teklifleri listesi
- Kullanıcıya özel dashboard ve geçmiş analiz listesi

## Klasör Yapısı

```
├── public/            # Web kök dizini
│   ├── index.php      # Ana kredi formu ve sohbet arayüzü
│   ├── login.php      # Giriş sayfası
│   ├── register.php   # Kayıt sayfası
│   ├── dashboard.php  # Kullanıcı paneli
│   └── api/           # REST uç noktaları (analyze, chat, offers, auth)
├── lib/               # Yardımcı sınıflar (Auth, Database, OpenAIClient)
├── config/            # Ortam değişkeni yükleyicisi
├── sql/schema.sql     # PostgreSQL tablo tanımları
├── bootstrap.php      # Global bootstrap ve autoload
└── .env.example       # Örnek ortam değişkenleri
```

## Kurulum

1. Depoyu sunucunuza kopyalayın ve web kök dizini olarak `public/` klasörünü işaretleyin.
2. `.env.example` dosyasını `.env` olarak kopyalayın ve veritabanı / OpenAI anahtarı bilgilerini girin.
3. `sql/schema.sql` dosyasını PostgreSQL veritabanınıza uygulayın.
4. Nginx yapılandırmasında PHP-FPM yönlendirmesini aşağıdaki gibi ayarlayın:

```
server {
    listen 80;
    server_name kredi.ltd;
    root /var/www/kredi/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock; # veya php7.4-fpm
    }

    location ~ /\.ht {
        deny all;
    }
}
```

5. PHP `curl` uzantısının etkin olduğundan emin olun (OpenAI istekleri için gereklidir).
6. Oturumların çalışması için `session.save_path` dizininin yazma izinleri olduğundan emin olun.

## API Kullanımı

Tüm API uç noktaları `/public/api/` altında yer alır ve `Authorization: Bearer <JWT>` başlığı gerektirir.

- `POST /api/register.php` – JSON: `{ "name": "...", "email": "...", "password": "..." }`
- `POST /api/login.php` – JSON: `{ "email": "...", "password": "..." }`
- `POST /api/analyze.php` – JSON: `{ "income": 0, "credit_type": "", "term": 0, "amount": 0 }`
- `POST /api/chat.php` – JSON: `{ "message": "..." }`
- `GET /api/offers.php`

Başarılı kimlik doğrulama sonrası dönen JWT token hem JavaScript arayüzünde hem de API istemcilerinde kullanılabilir.

## Geliştirme İpuçları

- `APP_ENV=local` değerini ayarlarsanız PHP hata ekranlarını etkinleştirmek için `display_errors` değerini `.htaccess` veya `php.ini` üzerinden açabilirsiniz.
- Tailwind CSS CDN üzerinden yüklendiği için derleme süreci gerekmez.
- `lib/OpenAIClient.php` dosyasında OpenAI model adı ve API URL'si gerektiğinde değiştirilebilir.

## Lisans

Bu proje kredi.ltd ekibi için hazırlanmıştır.

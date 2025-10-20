# DataHub Backend

FastAPI + SQLAlchemy tabanlı DataHub API uygulaması.

## Kurulum

```bash
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env
```

`.env` dosyasında veritabanı bağlantısı ve JWT ayarlarını özelleştirin.

## Veritabanı

MySQL 8.x kullanın ve `datahub` adında bir veritabanı oluşturun. Varsayılan bağlantı:

```
mysql+pymysql://datauser:gizlisifre@localhost:3306/datahub
```

Tablolar uygulama ilk çalıştığında otomatik olarak oluşturulur.

## Çalıştırma

```bash
uvicorn main:app --reload
```

## Önemli Endpointler

- `POST /auth/register` – kullanıcı oluşturma (admin rolü dahil)
- `POST /auth/login` – JWT üretir
- `POST /uploads/excel` – üyeler Excel dosyası yükler
- `POST /admin/uploads/{id}/approve` – admin onayı
- `POST /operator/purchase/{id}` – operatör satın alma işlemi

Tüm satın alma işlemleri transaction güvenliğinde gerçekleşir ve komisyonlar
sistem ayarlarından yönetilir.

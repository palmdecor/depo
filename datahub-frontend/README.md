# DataHub Frontend

Next.js + TailwindCSS tabanlı kontrol paneli uygulaması.

## Kurulum

```bash
npm install
```

Gerekirse API adresini `.env.local` dosyasında tanımlayın:

```
NEXT_PUBLIC_API_URL=http://localhost:8000
```

## Çalıştırma

```bash
npm run dev
```

### Paneller

- `/dashboard/admin` – kullanıcı, firma, komisyon ve bakiye yönetimi
- `/dashboard/operator` – bakiye görüntüleme, veri satın alma
- `/dashboard/user` – Excel yükleme ve kazanç takibi

Tüm paneller JWT ile korunan FastAPI API'sine bağlanır.

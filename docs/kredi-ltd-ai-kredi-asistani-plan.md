# Kredi.ltd AI Kredi Asistanı Teknik Planı

## 1. Ürün Özeti
Kredi.ltd AI Kredi Asistanı, kullanıcıların gelir, kredi tipi ve finansal geçmiş verilerinden yararlanarak kişiselleştirilmiş kredi analizleri sunan bir web platformudur. Next.js tabanlı modern bir arayüz, FastAPI destekli ölçeklenebilir bir API katmanı ve OpenAI GPT-4o ailesi modelleri ile güçlendirilmiş yapay zeka modüllerini bir araya getirir.

## 2. Mimari Genel Bakış
- **İstemci (Next.js + TypeScript + Tailwind CSS)**: Formlar, dashboard ve AI sohbet deneyimi için zengin kullanıcı arayüzü.
- **API Katmanı (FastAPI)**: Analiz, sohbet ve kredi teklifi uç noktaları için REST API.
- **Veritabanı (PostgreSQL)**: Kullanıcı, kredi profili ve oturum verilerinin kalıcı saklanması.
- **Yapay Zeka Hizmeti (OpenAI GPT-4o/GPT-4o-mini)**: Kredi uygunluk skoru, faiz tahmini ve tavsiye metinleri üretimi.
- **İzleme & Günlükleme**: Render/Railway logları + opsiyonel Sentry/Datadog entegrasyonu.
- **Dağıtım**: Frontend için Vercel, backend için Render veya Railway, veritabanı için Supabase/Neon.

### Veri Akışı
1. Kullanıcı, Next.js arayüzünde kredi formunu doldurur veya AI asistana mesaj gönderir.
2. İstemci, JWT ile yetkilendirilen FastAPI uç noktalarına istek atar.
3. FastAPI, verileri doğrular ve OpenAI API'sini çağırarak analiz üretir.
4. Elde edilen sonuçlar PostgreSQL'e kaydedilir ve istemciye JSON olarak döndürülür.
5. Frontend, Chart.js ve Tailwind bileşenleriyle sonuçları görselleştirir.

## 3. Modül Bazlı Detaylar
### 3.1 Frontend Modülleri
| Modül | Açıklama | Kritik Noktalar |
| --- | --- | --- |
| `CreditForm` | Gelir, kredi tipi, vade ve tutar girişlerini alır. | Form doğrulama (React Hook Form + Zod), JWT ekleme. |
| `ChatWidget` | AI destekli sohbet arayüzü. | WebSocket veya uzun polling, yazım sırasında yükleme durumları. |
| `OfferCard` | Banka teklif kartları ve skorlar. | Grafik verisi bağlama, responsive tasarım. |
| `Dashboard` | Geçmiş analizleri ve istatistikler. | SSR/ISR stratejisi, Chart.js ile grafikler. |
| `Auth Pages` (`login`, `register`) | Kullanıcı kimlik doğrulama akışı. | Form doğrulama, Supabase Auth entegrasyonu opsiyonel. |

### 3.2 Backend Modülleri
| Uç Nokta | Metod | Açıklama | Notlar |
| --- | --- | --- | --- |
| `/api/analyze` | POST | Kredi analizi üretir. | Pydantic şemaları, OpenAI çağrısı, sonuçların DB'ye yazılması. |
| `/api/chat` | POST | AI sohbet yanıtı döndürür. | Konversasyon geçmişi saklama, token limitleri. |
| `/api/offers` | GET | Banka tekliflerini döndürür. | Mock veri -> ileride entegrasyon. |
| `/api/user` | GET/PUT | Kullanıcı profil bilgileri. | JWT ile koruma. |

### 3.3 AI Modülü
- **Prompt Şablonu**: `Kullanıcının aylık geliri: {{income}}₺...`
- **Model**: `gpt-4o-mini` ana seçim, gerektiğinde `gpt-4o`.
- **Çıktılar**: `ai_score`, `recommended_banks`, `interest_rate_estimate`, `monthly_payment`, `ai_comment`.
- **Guardrails**: Token sınırları, içerik filtreleri, loglama.

## 4. Veritabanı Şeması
```sql
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE credit_profiles (
  id SERIAL PRIMARY KEY,
  user_id INT REFERENCES users(id),
  income DECIMAL,
  credit_type VARCHAR(50),
  term INT,
  amount DECIMAL,
  ai_score INT,
  recommendations TEXT,
  created_at TIMESTAMP DEFAULT NOW()
);
```

Ek tablolar:
- `sessions`: Refresh token ve cihaz yönetimi.
- `offers_cache`: Güncel kredi teklifleri için cache tablosu.

## 5. Güvenlik
- **Kimlik Doğrulama**: JWT access/refresh token, opsiyonel Supabase Auth.
- **Veri Koruma**: HTTPS zorunluluğu, gizli veriler için KMS/secrets manager.
- **Input Validasyonu**: Frontend'de Zod, backend'de Pydantic.
- **Loglama**: Kişisel veriler için maskeleme, KVKK uyumluluğu.

## 6. DevOps & Dağıtım
- **CI/CD**: GitHub Actions ile lint/test/deploy pipeline.
- **Ortamlar**: `development`, `staging`, `production`.
- **Konfigürasyon**: `.env` yönetimi (Vercel + Render Secrets). OpenAI anahtarı `OPENAI_API_KEY`.
- **İzleme**: Sentry (frontend/backend), Vercel Analytics, Render Metrics.

## 7. MVP Yol Haritası
| Faz | Görev | Süre |
| --- | --- | --- |
| 1 | UI/UX tasarımı | 1 hafta |
| 2 | Next.js frontend geliştirme | 1 hafta |
| 3 | FastAPI backend | 2 hafta |
| 4 | OpenAI entegrasyonu | 3 gün |
| 5 | Kredi karşılaştırma mock modülü | 1 hafta |
| 6 | Test & Deploy | 1 hafta |

## 8. Açık Sorular & Sonraki Adımlar
- Banka teklifleri için gerçek API sağlayıcısı belirlenecek mi?
- Kullanıcı kimlik doğrulamada üçüncü parti servis (ör. Supabase Auth) tercih edilecek mi?
- Finansal tavsiyeler için hukuki uyumluluk danışmanlığı gereklilikleri?
- Model çıktılarının doğruluğunu artırmak için ek veri kaynakları (ör. Findeks) entegre edilecek mi?

Bu plan, MVP'yi teslim etmek için gerekli bileşenleri, entegrasyonları ve yol haritasını özetler. Her faz için ayrıntılı görev listeleri ve issue'lar üretilebilir.

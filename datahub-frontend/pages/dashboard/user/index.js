import { useEffect, useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { API_BASE_URL, apiRequest } from '@/lib/api';

export default function UserDashboard() {
  const [token, setToken] = useState(null);
  const [loginForm, setLoginForm] = useState({ email: '', password: '' });
  const [error, setError] = useState(null);
  const [message, setMessage] = useState(null);
  const [profile, setProfile] = useState(null);
  const [uploads, setUploads] = useState([]);
  const [uploadForm, setUploadForm] = useState({ title: '', price: '', description: '' });
  const [file, setFile] = useState(null);

  useEffect(() => {
    if (token) {
      refreshData();
    }
  }, [token]);

  const refreshData = async () => {
    try {
      const [me, myUploads] = await Promise.all([
        apiRequest('/users/me', { token }),
        apiRequest('/uploads/', { token }),
      ]);
      setProfile(me);
      setUploads(myUploads);
    } catch (err) {
      setError(err.message);
    }
  };

  const handleLogin = async (event) => {
    event.preventDefault();
    setError(null);
    const formData = new URLSearchParams();
    formData.append('username', loginForm.email);
    formData.append('password', loginForm.password);
    try {
      const response = await fetch(`${API_BASE_URL}/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData,
      });
      if (!response.ok) {
        throw new Error('Giriş başarısız');
      }
      const data = await response.json();
      setToken(data.access_token);
      setMessage('Giriş başarılı.');
    } catch (err) {
      setError(err.message);
    }
  };

  const handleUpload = async (event) => {
    event.preventDefault();
    if (!file) {
      setError('Lütfen bir Excel dosyası seçin');
      return;
    }
    const formData = new FormData();
    formData.append('title', uploadForm.title);
    formData.append('price_per_record', uploadForm.price);
    formData.append('description', uploadForm.description);
    formData.append('file', file);
    try {
      await apiRequest('/uploads/excel', {
        method: 'POST',
        token,
        data: formData,
        isFormData: true,
      });
      setMessage('Excel yüklemesi tamamlandı. Admin onayı bekleniyor.');
      setUploadForm({ title: '', price: '', description: '' });
      setFile(null);
      await refreshData();
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <DashboardLayout
      title="Üye Paneli"
      description="Excel verilerini yükle ve kazançlarını takip et"
    >
      <div className="space-y-8">
        {!token && (
          <form onSubmit={handleLogin} className="grid gap-4 md:grid-cols-4">
            <input
              required
              type="email"
              placeholder="E-posta"
              className="input"
              value={loginForm.email}
              onChange={(e) => setLoginForm({ ...loginForm, email: e.target.value })}
            />
            <input
              required
              type="password"
              placeholder="Parola"
              className="input"
              value={loginForm.password}
              onChange={(e) => setLoginForm({ ...loginForm, password: e.target.value })}
            />
            <button type="submit" className="rounded-md bg-sky-600 px-4 py-2 font-semibold text-white">
              Giriş Yap
            </button>
          </form>
        )}

        {error && <p className="rounded bg-red-100 p-3 text-red-700">{error}</p>}
        {message && <p className="rounded bg-emerald-100 p-3 text-emerald-700">{message}</p>}

        {token && profile && (
          <div className="space-y-10">
            <section className="grid gap-4 rounded-lg border border-slate-200 p-4 sm:grid-cols-3">
              <div>
                <p className="text-sm text-slate-500">Üye</p>
                <p className="text-lg font-semibold text-slate-800">{profile.full_name}</p>
              </div>
              <div>
                <p className="text-sm text-slate-500">E-posta</p>
                <p className="text-lg font-semibold text-slate-800">{profile.email}</p>
              </div>
              <div>
                <p className="text-sm text-slate-500">Bakiyen</p>
                <p className="text-lg font-semibold text-emerald-600">₺{profile.balance}</p>
              </div>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Excel Yükle</h2>
              <form onSubmit={handleUpload} className="grid gap-3 md:grid-cols-2">
                <input
                  required
                  className="input"
                  placeholder="Başlık"
                  value={uploadForm.title}
                  onChange={(e) => setUploadForm({ ...uploadForm, title: e.target.value })}
                />
                <input
                  required
                  className="input"
                  placeholder="Kayıt Başına Fiyat"
                  value={uploadForm.price}
                  onChange={(e) => setUploadForm({ ...uploadForm, price: e.target.value })}
                />
                <textarea
                  className="input h-24"
                  placeholder="Açıklama"
                  value={uploadForm.description}
                  onChange={(e) => setUploadForm({ ...uploadForm, description: e.target.value })}
                />
                <input
                  required
                  type="file"
                  accept=".xlsx,.xls"
                  className="input"
                  onChange={(e) => setFile(e.target.files?.[0] ?? null)}
                />
                <button
                  type="submit"
                  className="md:col-span-2 rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white"
                >
                  Yüklemeyi Gönder
                </button>
              </form>
              <p className="text-sm text-slate-500">
                Dosya başlıkları <strong>Ad, Soyad, Telefon, Kategori</strong> olmalıdır.
              </p>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Yüklemelerin</h2>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-slate-200 text-sm">
                  <thead className="bg-slate-50">
                    <tr>
                      <th className="px-3 py-2 text-left font-semibold">Başlık</th>
                      <th className="px-3 py-2 text-left font-semibold">Kayıt</th>
                      <th className="px-3 py-2 text-left font-semibold">Fiyat</th>
                      <th className="px-3 py-2 text-left font-semibold">Durum</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {uploads.map((upload) => (
                      <tr key={upload.id}>
                        <td className="px-3 py-2">{upload.title}</td>
                        <td className="px-3 py-2">{upload.total_records}</td>
                        <td className="px-3 py-2">₺{upload.price_per_record}</td>
                        <td className="px-3 py-2">
                          {upload.is_approved ? (
                            <span className="rounded bg-emerald-100 px-2 py-1 text-emerald-700">Satışta</span>
                          ) : (
                            <span className="rounded bg-amber-100 px-2 py-1 text-amber-700">Onay Bekliyor</span>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </section>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}

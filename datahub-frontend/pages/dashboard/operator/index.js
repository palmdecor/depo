import { useEffect, useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { API_BASE_URL, apiRequest } from '@/lib/api';

export default function OperatorDashboard() {
  const [token, setToken] = useState(null);
  const [loginForm, setLoginForm] = useState({ email: '', password: '' });
  const [error, setError] = useState(null);
  const [message, setMessage] = useState(null);
  const [uploads, setUploads] = useState([]);
  const [selectedUpload, setSelectedUpload] = useState(null);
  const [records, setRecords] = useState([]);
  const [profile, setProfile] = useState(null);

  useEffect(() => {
    if (token) {
      refreshData();
    }
  }, [token]);

  const refreshData = async () => {
    try {
      const [me, availableUploads] = await Promise.all([
        apiRequest('/users/me', { token }),
        apiRequest('/uploads/', { token }),
      ]);
      setProfile(me);
      setUploads(availableUploads);
      if (selectedUpload) {
        await loadRecords(selectedUpload, token);
      }
    } catch (err) {
      setError(err.message);
    }
  };

  const loadRecords = async (uploadId, authToken = token) => {
    try {
      const data = await apiRequest(`/operator/records/${uploadId}`, { token: authToken });
      setRecords(data);
      setSelectedUpload(uploadId);
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

  const purchaseUpload = async (uploadId) => {
    try {
      const result = await apiRequest(`/operator/purchase/${uploadId}`, {
        method: 'POST',
        token,
      });
      setMessage(`Satın alma tamamlandı. Toplam: ${result.total_price}`);
      await refreshData();
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <DashboardLayout
      title="Operatör Paneli"
      description="Bakiyeni görüntüle, kayıtları incele ve satın alma işlemlerini tamamla"
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
                <p className="text-sm text-slate-500">Operatör</p>
                <p className="text-lg font-semibold text-slate-800">{profile.full_name}</p>
              </div>
              <div>
                <p className="text-sm text-slate-500">E-posta</p>
                <p className="text-lg font-semibold text-slate-800">{profile.email}</p>
              </div>
              <div>
                <p className="text-sm text-slate-500">Bakiye</p>
                <p className="text-lg font-semibold text-emerald-600">₺{profile.balance}</p>
              </div>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Onaylı Veri Setleri</h2>
              <div className="grid gap-4 md:grid-cols-2">
                {uploads.map((upload) => (
                  <div key={upload.id} className="rounded-lg border border-slate-200 p-4 shadow-sm">
                    <h3 className="text-lg font-semibold text-slate-800">{upload.title}</h3>
                    <p className="mt-2 text-sm text-slate-500">Kayıt Sayısı: {upload.total_records}</p>
                    <p className="mt-1 text-sm text-slate-500">
                      Fiyat/Kayıt: ₺{upload.price_per_record}
                    </p>
                    <div className="mt-4 flex gap-3">
                      <button
                        className="rounded bg-slate-800 px-3 py-2 text-sm font-semibold text-white"
                        onClick={() => loadRecords(upload.id)}
                      >
                        Kayıtları Görüntüle
                      </button>
                      <button
                        className="rounded bg-emerald-600 px-3 py-2 text-sm font-semibold text-white"
                        onClick={() => purchaseUpload(upload.id)}
                      >
                        Satın Al
                      </button>
                    </div>
                  </div>
                ))}
                {uploads.length === 0 && (
                  <p className="text-sm text-slate-500">Onaylanmış yükleme bulunmuyor.</p>
                )}
              </div>
            </section>

            {selectedUpload && (
              <section className="space-y-4">
                <h2 className="text-xl font-semibold text-slate-700">Kayıtlar</h2>
                <div className="overflow-x-auto">
                  <table className="min-w-full divide-y divide-slate-200 text-sm">
                    <thead className="bg-slate-50">
                      <tr>
                        <th className="px-3 py-2 text-left font-semibold">İsim</th>
                        <th className="px-3 py-2 text-left font-semibold">Soyisim</th>
                        <th className="px-3 py-2 text-left font-semibold">Telefon</th>
                        <th className="px-3 py-2 text-left font-semibold">Kategori</th>
                        <th className="px-3 py-2 text-left font-semibold">Durum</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {records.map((record) => (
                        <tr key={record.id}>
                          <td className="px-3 py-2">{record.first_name}</td>
                          <td className="px-3 py-2">{record.last_name}</td>
                          <td className="px-3 py-2 font-mono">{record.phone}</td>
                          <td className="px-3 py-2">{record.category}</td>
                          <td className="px-3 py-2">{record.is_sold ? 'Satıldı' : 'Satışta'}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </section>
            )}
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}

import { useEffect, useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { API_BASE_URL, apiRequest } from '@/lib/api';

export default function AdminDashboard() {
  const [token, setToken] = useState(null);
  const [loginForm, setLoginForm] = useState({ email: '', password: '' });
  const [error, setError] = useState(null);
  const [users, setUsers] = useState([]);
  const [uploads, setUploads] = useState([]);
  const [firms, setFirms] = useState([]);
  const [newUser, setNewUser] = useState({ email: '', full_name: '', password: '', role: 'user' });
  const [balanceForm, setBalanceForm] = useState({ userId: '', amount: '' });
  const [firmForm, setFirmForm] = useState({ name: '', owner_user_id: '' });
  const [commission, setCommission] = useState({ admin: '0.20', member: '0.80' });
  const [message, setMessage] = useState(null);

  useEffect(() => {
    if (token) {
      Promise.all([
        apiRequest('/users/', { token }).then(setUsers),
        apiRequest('/uploads/', { token }).then(setUploads),
        apiRequest('/operator-firms/', { token }).then(setFirms),
      ]).catch((err) => setError(err.message));
    }
  }, [token]);

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
      setMessage('Giriş başarılı. Yönetim araçları açıldı.');
    } catch (err) {
      setError(err.message);
    }
  };

  const handleCreateUser = async (event) => {
    event.preventDefault();
    try {
      await apiRequest('/auth/register', {
        method: 'POST',
        data: newUser,
        token,
      });
      setMessage('Yeni kullanıcı oluşturuldu');
      setNewUser({ email: '', full_name: '', password: '', role: 'user' });
      const updatedUsers = await apiRequest('/users/', { token });
      setUsers(updatedUsers);
    } catch (err) {
      setError(err.message);
    }
  };

  const handleBalanceUpdate = async (event) => {
    event.preventDefault();
    try {
      await apiRequest(`/admin/users/${balanceForm.userId}/balance?amount=${balanceForm.amount}`, {
        method: 'POST',
        token,
      });
      setMessage('Bakiye güncellendi');
      setBalanceForm({ userId: '', amount: '' });
    } catch (err) {
      setError(err.message);
    }
  };

  const handleFirmCreate = async (event) => {
    event.preventDefault();
    try {
      await apiRequest('/operator-firms/', {
        method: 'POST',
        data: { ...firmForm, owner_user_id: Number(firmForm.owner_user_id) },
        token,
      });
      setMessage('Operatör firması oluşturuldu');
      setFirmForm({ name: '', owner_user_id: '' });
      const updated = await apiRequest('/operator-firms/', { token });
      setFirms(updated);
    } catch (err) {
      setError(err.message);
    }
  };

  const handleCommissionUpdate = async (event) => {
    event.preventDefault();
    try {
      await apiRequest('/admin/settings', {
        method: 'POST',
        token,
        data: { key: 'admin_commission', value: commission.admin },
      });
      await apiRequest('/admin/settings', {
        method: 'POST',
        token,
        data: { key: 'member_commission', value: commission.member },
      });
      setMessage('Komisyon oranları güncellendi');
    } catch (err) {
      setError(err.message);
    }
  };

  const approveUpload = async (uploadId) => {
    try {
      await apiRequest(`/admin/uploads/${uploadId}/approve`, { method: 'POST', token });
      setMessage('Yükleme onaylandı');
      const updatedUploads = await apiRequest('/uploads/', { token });
      setUploads(updatedUploads);
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <DashboardLayout
      title="Admin Paneli"
      description="Kullanıcı ve firma yönetimi, komisyon oranları ve bakiye işlemleri"
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

        {token && (
          <div className="space-y-10">
            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Yeni Kullanıcı Oluştur</h2>
              <form onSubmit={handleCreateUser} className="grid gap-3 md:grid-cols-2">
                <input
                  required
                  className="input"
                  placeholder="E-posta"
                  value={newUser.email}
                  onChange={(e) => setNewUser({ ...newUser, email: e.target.value })}
                />
                <input
                  required
                  className="input"
                  placeholder="Ad Soyad"
                  value={newUser.full_name}
                  onChange={(e) => setNewUser({ ...newUser, full_name: e.target.value })}
                />
                <input
                  required
                  className="input"
                  type="password"
                  placeholder="Parola"
                  value={newUser.password}
                  onChange={(e) => setNewUser({ ...newUser, password: e.target.value })}
                />
                <select
                  className="input"
                  value={newUser.role}
                  onChange={(e) => setNewUser({ ...newUser, role: e.target.value })}
                >
                  <option value="user">Üye</option>
                  <option value="operator">Operatör</option>
                  <option value="admin">Admin</option>
                </select>
                <button
                  type="submit"
                  className="md:col-span-2 rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white"
                >
                  Kullanıcı Oluştur
                </button>
              </form>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Operatör Firması Oluştur</h2>
              <form onSubmit={handleFirmCreate} className="grid gap-3 md:grid-cols-3">
                <input
                  required
                  className="input"
                  placeholder="Firma Adı"
                  value={firmForm.name}
                  onChange={(e) => setFirmForm({ ...firmForm, name: e.target.value })}
                />
                <input
                  required
                  className="input"
                  placeholder="Sahip Kullanıcı ID"
                  value={firmForm.owner_user_id}
                  onChange={(e) => setFirmForm({ ...firmForm, owner_user_id: e.target.value })}
                />
                <button className="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white">
                  Firma Oluştur
                </button>
              </form>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Komisyon Oranları</h2>
              <form onSubmit={handleCommissionUpdate} className="grid gap-3 md:grid-cols-3">
                <input
                  required
                  className="input"
                  placeholder="Admin Komisyonu"
                  value={commission.admin}
                  onChange={(e) => setCommission({ ...commission, admin: e.target.value })}
                />
                <input
                  required
                  className="input"
                  placeholder="Üye Komisyonu"
                  value={commission.member}
                  onChange={(e) => setCommission({ ...commission, member: e.target.value })}
                />
                <button className="rounded-md bg-slate-800 px-4 py-2 font-semibold text-white">
                  Kaydet
                </button>
              </form>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Bakiye Güncelle</h2>
              <form onSubmit={handleBalanceUpdate} className="grid gap-3 md:grid-cols-3">
                <input
                  required
                  className="input"
                  placeholder="Kullanıcı ID"
                  value={balanceForm.userId}
                  onChange={(e) => setBalanceForm({ ...balanceForm, userId: e.target.value })}
                />
                <input
                  required
                  className="input"
                  placeholder="Yeni Bakiye"
                  value={balanceForm.amount}
                  onChange={(e) => setBalanceForm({ ...balanceForm, amount: e.target.value })}
                />
                <button className="rounded-md bg-amber-600 px-4 py-2 font-semibold text-white">
                  Güncelle
                </button>
              </form>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Bekleyen Yüklemeler</h2>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-slate-200 text-sm">
                  <thead className="bg-slate-50">
                    <tr>
                      <th className="px-3 py-2 text-left font-semibold">ID</th>
                      <th className="px-3 py-2 text-left font-semibold">Başlık</th>
                      <th className="px-3 py-2 text-left font-semibold">Üye</th>
                      <th className="px-3 py-2 text-left font-semibold">Durum</th>
                      <th className="px-3 py-2" />
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {uploads.map((upload) => (
                      <tr key={upload.id}>
                        <td className="px-3 py-2">{upload.id}</td>
                        <td className="px-3 py-2">{upload.title}</td>
                        <td className="px-3 py-2">{upload.user_id}</td>
                        <td className="px-3 py-2">
                          {upload.is_approved ? (
                            <span className="rounded bg-emerald-100 px-2 py-1 text-emerald-700">Onaylı</span>
                          ) : (
                            <span className="rounded bg-amber-100 px-2 py-1 text-amber-700">Beklemede</span>
                          )}
                        </td>
                        <td className="px-3 py-2 text-right">
                          {!upload.is_approved && (
                            <button
                              onClick={() => approveUpload(upload.id)}
                              className="rounded-md bg-emerald-600 px-3 py-1 text-xs font-semibold text-white"
                            >
                              Onayla
                            </button>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Kullanıcılar</h2>
              <ul className="space-y-2 text-sm">
                {users.map((user) => (
                  <li key={user.id} className="rounded border border-slate-200 p-3">
                    <div className="font-semibold">{user.full_name}</div>
                    <div className="text-slate-500">{user.email}</div>
                    <div className="text-slate-500">Rol: {user.role}</div>
                    <div className="text-slate-500">Bakiye: {user.balance}</div>
                  </li>
                ))}
              </ul>
            </section>

            <section className="space-y-4">
              <h2 className="text-xl font-semibold text-slate-700">Operatör Firmaları</h2>
              <ul className="space-y-2 text-sm">
                {firms.map((firm) => (
                  <li key={firm.id} className="rounded border border-slate-200 p-3">
                    <div className="font-semibold">{firm.name}</div>
                    <div className="text-slate-500">Sahip Kullanıcı: {firm.owner_user_id}</div>
                    <div className="text-slate-500">Bakiye: {firm.balance}</div>
                  </li>
                ))}
              </ul>
            </section>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}


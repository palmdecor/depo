import Link from 'next/link';

const dashboards = [
  {
    href: '/dashboard/admin',
    title: 'Admin Paneli',
    description: 'Kullanıcıları ve firmaları yönet, komisyon oranlarını ayarla.'
  },
  {
    href: '/dashboard/operator',
    title: 'Operatör Paneli',
    description: 'Bakiyeni görüntüle ve onaylanan dataları satın al.'
  },
  {
    href: '/dashboard/user',
    title: 'Üye Paneli',
    description: 'Excel dosyaları yükle ve kazançlarını takip et.'
  }
];

export default function Home() {
  return (
    <div className="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white">
      <div className="mx-auto flex max-w-5xl flex-col items-center px-6 py-24 text-center">
        <h1 className="text-4xl font-bold sm:text-6xl">DataHub Platformuna Hoş Geldiniz</h1>
        <p className="mt-6 max-w-2xl text-lg text-slate-200">
          FastAPI + MySQL + Next.js tabanlı veri pazaryeri yönetim sistemi.
        </p>
        <div className="mt-12 grid w-full gap-6 sm:grid-cols-3">
          {dashboards.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              className="group rounded-xl bg-white/10 p-6 text-left shadow-xl transition hover:bg-white/20"
            >
              <h2 className="text-xl font-semibold text-white">{item.title}</h2>
              <p className="mt-3 text-sm text-slate-200">{item.description}</p>
              <span className="mt-4 inline-flex items-center text-sm font-medium text-sky-300">
                Panele Git →
              </span>
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}

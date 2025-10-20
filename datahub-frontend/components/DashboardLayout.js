export default function DashboardLayout({ title, description, children }) {
  return (
    <div className="min-h-screen">
      <header className="bg-white shadow">
        <div className="mx-auto max-w-6xl px-6 py-6">
          <h1 className="text-3xl font-bold text-slate-800">{title}</h1>
          {description && <p className="mt-2 text-slate-500">{description}</p>}
        </div>
      </header>
      <main className="mx-auto max-w-6xl px-6 py-10">
        <div className="rounded-lg bg-white p-6 shadow-lg ring-1 ring-slate-200">
          {children}
        </div>
      </main>
    </div>
  );
}

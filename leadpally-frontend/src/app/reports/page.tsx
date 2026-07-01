'use client';

import { useState } from 'react';
import api from '@/lib/api';

type Kpis = {
  total_leads: number;
  hot_leads: number;
  open_deals: number;
  won_deals: number;
  pipeline_value: string | number;
  won_value: string | number;
  campaigns: number;
};

type Dashboard = {
  kpis: Kpis;
  lead_status: { status: string; total: number }[];
  deal_status: { status: string; total: number }[];
  campaign_status: { status: string; total: number }[];
};

export default function ReportsPage() {
  const [dashboard, setDashboard] = useState<Dashboard | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  async function loadReports() {
    setLoading(true);
    setError('');
    try {
      const response = await api.get('/v1/reports/dashboard');
      setDashboard(response.data);
    } catch {
      setError('Could not load reports. Make sure you are logged in and have an active team.');
    } finally {
      setLoading(false);
    }
  }

  const kpis = dashboard?.kpis;

  return (
    <main className="min-h-screen bg-slate-50 p-6 md:p-8">
      <div className="mx-auto max-w-7xl">
        <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
          <div>
            <p className="text-sm font-semibold uppercase tracking-wide text-slate-500">Reports & Analytics</p>
            <h1 className="mt-2 text-3xl font-bold text-slate-900">Business Dashboard</h1>
            <p className="mt-2 text-slate-500">Track leads, deals, revenue, campaigns, and team performance.</p>
          </div>
          <button onClick={loadReports} className="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white disabled:opacity-50" disabled={loading} type="button">
            {loading ? 'Loading...' : 'Load reports'}
          </button>
        </div>

        {error && <p className="mt-4 rounded-xl bg-red-50 p-4 text-sm text-red-600">{error}</p>}

        <div className="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <KpiCard label="Total Leads" value={kpis?.total_leads ?? 0} />
          <KpiCard label="Hot Leads" value={kpis?.hot_leads ?? 0} />
          <KpiCard label="Open Deals" value={kpis?.open_deals ?? 0} />
          <KpiCard label="Won Deals" value={kpis?.won_deals ?? 0} />
          <KpiCard label="Pipeline Value" value={`₦${kpis?.pipeline_value ?? 0}`} />
          <KpiCard label="Won Revenue" value={`₦${kpis?.won_value ?? 0}`} />
          <KpiCard label="Campaigns" value={kpis?.campaigns ?? 0} />
          <a href="/api/v1/reports/export" className="rounded-2xl bg-slate-900 p-5 text-white shadow-sm">
            <p className="text-sm text-slate-300">Export</p>
            <p className="mt-2 text-2xl font-bold">CSV</p>
          </a>
        </div>

        <div className="mt-8 grid gap-4 lg:grid-cols-3">
          <ReportList title="Lead Status" rows={dashboard?.lead_status ?? []} labelKey="status" />
          <ReportList title="Deal Status" rows={dashboard?.deal_status ?? []} labelKey="status" />
          <ReportList title="Campaign Status" rows={dashboard?.campaign_status ?? []} labelKey="status" />
        </div>
      </div>
    </main>
  );
}

function KpiCard({ label, value }: { label: string; value: string | number }) {
  return (
    <div className="rounded-2xl bg-white p-5 shadow-sm">
      <p className="text-sm text-slate-500">{label}</p>
      <p className="mt-2 text-3xl font-bold text-slate-900">{value}</p>
    </div>
  );
}

function ReportList({ title, rows, labelKey }: { title: string; rows: Record<string, string | number>[]; labelKey: string }) {
  return (
    <section className="rounded-2xl bg-white p-5 shadow-sm">
      <h2 className="font-bold text-slate-900">{title}</h2>
      <div className="mt-4 space-y-3">
        {rows.map((row, index) => (
          <div key={`${row[labelKey]}-${index}`} className="flex items-center justify-between rounded-xl border border-slate-100 p-3">
            <span className="text-sm font-medium capitalize text-slate-700">{row[labelKey]}</span>
            <span className="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{row.total}</span>
          </div>
        ))}
        {rows.length === 0 && <p className="text-sm text-slate-500">No data loaded yet.</p>}
      </div>
    </section>
  );
}

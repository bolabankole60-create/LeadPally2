'use client';

import { FormEvent, useState } from 'react';
import api from '@/lib/api';

type Result = {
  id: number;
  name: string;
  phone?: string;
  website?: string;
  address?: string;
  rating?: string;
  reviews_count?: number;
};

export default function SearchPage() {
  const [keyword, setKeyword] = useState('');
  const [city, setCity] = useState('Lagos');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [results, setResults] = useState<Result[]>([]);

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true);
    setError('');

    try {
      const response = await api.post('/v1/search', { keyword, city });
      setResults(response.data.results ?? []);
    } catch {
      setError('Search failed. Make sure you are logged in and have an active team.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="min-h-screen bg-slate-50 p-6 md:p-8">
      <div className="mx-auto max-w-6xl">
        <div className="rounded-3xl bg-slate-900 p-8 text-white shadow-sm">
          <p className="text-sm font-semibold uppercase tracking-wide text-slate-300">Lead Search</p>
          <h1 className="mt-3 text-3xl font-bold md:text-5xl">Find business leads faster</h1>
          <p className="mt-4 max-w-2xl text-slate-300">Search businesses by keyword and city, then turn results into leads for outreach.</p>
        </div>

        <form onSubmit={submit} className="mt-6 grid gap-4 rounded-2xl bg-white p-5 shadow-sm md:grid-cols-[1fr_220px_160px]">
          <input className="rounded-xl border border-slate-200 p-3 outline-none focus:border-slate-900" placeholder="restaurants, salons, hotels" value={keyword} onChange={(event) => setKeyword(event.target.value)} required />
          <input className="rounded-xl border border-slate-200 p-3 outline-none focus:border-slate-900" placeholder="City" value={city} onChange={(event) => setCity(event.target.value)} required />
          <button className="rounded-xl bg-slate-900 p-3 font-semibold text-white disabled:opacity-50" disabled={loading}>{loading ? 'Searching...' : 'Search'}</button>
        </form>

        {error && <p className="mt-4 rounded-xl bg-red-50 p-4 text-sm text-red-600">{error}</p>}

        {!loading && results.length === 0 && !error && (
          <div className="mt-8 rounded-2xl bg-white p-8 text-center shadow-sm">
            <h2 className="text-xl font-semibold text-slate-900">No search results yet</h2>
            <p className="mt-2 text-slate-500">Enter a keyword and city to find businesses.</p>
          </div>
        )}

        {loading && (
          <div className="mt-8 grid gap-4 md:grid-cols-2">
            {[1, 2, 3, 4].map((item) => (
              <div key={item} className="h-40 animate-pulse rounded-2xl bg-white shadow-sm" />
            ))}
          </div>
        )}

        <div className="mt-8 grid gap-4 md:grid-cols-2">
          {results.map((result) => (
            <div key={result.id} className="rounded-2xl bg-white p-6 shadow-sm">
              <div className="flex items-start justify-between gap-4">
                <div>
                  <h2 className="text-xl font-semibold text-slate-900">{result.name}</h2>
                  <p className="mt-2 text-sm text-slate-500">{result.address}</p>
                </div>
                <span className="rounded-full bg-green-50 px-3 py-1 text-sm font-semibold text-green-700">{result.rating ?? 'N/A'} ★</span>
              </div>

              <p className="mt-4 text-sm text-slate-600">Reviews: {result.reviews_count ?? 0}</p>
              {result.phone && <p className="mt-2 text-sm text-slate-600">Phone: {result.phone}</p>}

              <div className="mt-5 flex flex-wrap gap-3">
                {result.phone && <a className="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700" href={`tel:${result.phone}`}>Call</a>}
                {result.website && <a className="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" href={result.website} target="_blank">Visit website</a>}
                <button className="rounded-lg border px-4 py-2 text-sm font-semibold text-slate-700" type="button">Save lead</button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </main>
  );
}

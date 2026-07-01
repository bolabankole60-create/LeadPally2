'use client';
import { FormEvent, useState } from 'react';
import { useRouter } from 'next/navigation';
import api from '@/lib/api';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault(); setLoading(true); setError('');
    try { const response = await api.post('/v1/auth/login', { email, password }); window.localStorage.setItem('leadpally_token', response.data.token); router.push('/dashboard'); }
    catch { setError('Invalid login details. Please try again.'); }
    finally { setLoading(false); }
  }
  return <main className="flex min-h-screen items-center justify-center bg-slate-50 px-4"><form onSubmit={handleSubmit} className="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm"><h1 className="text-2xl font-bold text-slate-900">Login to LeadPally</h1><p className="mt-2 text-sm text-slate-500">Continue to your dashboard.</p>{error && <p className="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-600">{error}</p>}<label className="mt-6 block text-sm font-medium text-slate-700">Email</label><input className="mt-2 w-full rounded-lg border p-3" type="email" value={email} onChange={(e) => setEmail(e.target.value)} required /><label className="mt-4 block text-sm font-medium text-slate-700">Password</label><input className="mt-2 w-full rounded-lg border p-3" type="password" value={password} onChange={(e) => setPassword(e.target.value)} required /><button className="mt-6 w-full rounded-lg bg-slate-900 p-3 font-semibold text-white disabled:opacity-50" disabled={loading}>{loading ? 'Logging in...' : 'Login'}</button></form></main>;
}

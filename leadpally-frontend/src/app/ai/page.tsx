'use client';

import { FormEvent, useState } from 'react';
import api from '@/lib/api';

type Conversation = { id: number; title: string; purpose: string };
type AiMessage = { id: number; role: string; content: string };

const tools = [
  ['assistant', 'AI Assistant'],
  ['email', 'Email'],
  ['whatsapp', 'WhatsApp'],
  ['call_script', 'Call Script'],
  ['lead_insight', 'Lead Insight'],
  ['next_action', 'Next Action'],
  ['meeting_summary', 'Meeting Summary'],
  ['follow_up', 'Follow-up'],
] as const;

export default function AiPage() {
  const [conversations, setConversations] = useState<Conversation[]>([]);
  const [activeConversation, setActiveConversation] = useState<Conversation | null>(null);
  const [messages, setMessages] = useState<AiMessage[]>([]);
  const [title, setTitle] = useState('Sales Assistant');
  const [tool, setTool] = useState('assistant');
  const [message, setMessage] = useState('Which lead should I follow up with today?');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  async function loadConversations() {
    setLoading(true);
    setError('');
    try {
      const response = await api.get('/v1/ai/conversations');
      setConversations(response.data.conversations ?? []);
    } catch {
      setError('Could not load AI conversations. Make sure you are logged in and have an active team.');
    } finally {
      setLoading(false);
    }
  }

  async function createConversation(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setError('');
    try {
      const response = await api.post('/v1/ai/conversations', { title, purpose: 'sales_assistant' });
      setActiveConversation(response.data.conversation);
      setConversations((current) => [response.data.conversation, ...current]);
      setMessages([]);
    } catch {
      setError('Could not create AI conversation.');
    }
  }

  async function openConversation(conversation: Conversation) {
    setError('');
    try {
      const response = await api.get(`/v1/ai/conversations/${conversation.id}`);
      setActiveConversation(response.data.conversation);
      setMessages(response.data.conversation.messages ?? []);
    } catch {
      setError('Could not open conversation.');
    }
  }

  async function sendMessage(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    if (!activeConversation) {
      setError('Create or select a conversation first.');
      return;
    }

    const userMessage: AiMessage = { id: Date.now(), role: 'user', content: message };
    setMessages((current) => [...current, userMessage]);
    setMessage('');

    try {
      const response = await api.post(`/v1/ai/conversations/${activeConversation.id}/chat`, { message: userMessage.content, tool });
      setMessages((current) => [...current, response.data.message]);
    } catch {
      setError('Could not get AI response.');
    }
  }

  return (
    <main className="min-h-screen bg-slate-50 p-6 md:p-8">
      <div className="mx-auto max-w-7xl">
        <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
          <div>
            <p className="text-sm font-semibold uppercase tracking-wide text-slate-500">AI Sales Assistant</p>
            <h1 className="mt-2 text-3xl font-bold text-slate-900">LeadPally AI</h1>
            <p className="mt-2 text-slate-500">Draft emails, WhatsApp messages, call scripts, insights, and next actions.</p>
          </div>
          <button onClick={loadConversations} className="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white disabled:opacity-50" disabled={loading} type="button">
            {loading ? 'Loading...' : 'Load conversations'}
          </button>
        </div>

        {error && <p className="mt-4 rounded-xl bg-red-50 p-4 text-sm text-red-600">{error}</p>}

        <div className="mt-6 grid gap-4 lg:grid-cols-[320px_1fr]">
          <aside className="rounded-2xl bg-white p-5 shadow-sm">
            <form onSubmit={createConversation}>
              <h2 className="font-bold text-slate-900">New conversation</h2>
              <input className="mt-4 w-full rounded-xl border p-3" value={title} onChange={(event) => setTitle(event.target.value)} required />
              <button className="mt-3 w-full rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white">Create</button>
            </form>

            <div className="mt-6 space-y-3">
              {conversations.map((conversation) => (
                <button key={conversation.id} onClick={() => openConversation(conversation)} className="w-full rounded-xl border border-slate-100 p-4 text-left hover:bg-slate-50" type="button">
                  <p className="font-semibold text-slate-900">{conversation.title}</p>
                  <p className="mt-1 text-xs text-slate-500">{conversation.purpose}</p>
                </button>
              ))}
              {conversations.length === 0 && <p className="text-sm text-slate-500">No conversations loaded yet.</p>}
            </div>
          </aside>

          <section className="rounded-2xl bg-white p-5 shadow-sm">
            <div className="flex flex-col justify-between gap-3 md:flex-row md:items-center">
              <div>
                <h2 className="text-lg font-bold text-slate-900">{activeConversation?.title ?? 'Select a conversation'}</h2>
                <p className="text-sm text-slate-500">Choose a tool and send a prompt.</p>
              </div>
              <select className="rounded-xl border p-3" value={tool} onChange={(event) => setTool(event.target.value)}>
                {tools.map(([value, label]) => <option key={value} value={value}>{label}</option>)}
              </select>
            </div>

            <div className="mt-6 min-h-[360px] space-y-4 rounded-2xl bg-slate-50 p-4">
              {messages.map((item) => (
                <div key={item.id} className={`rounded-2xl p-4 ${item.role === 'user' ? 'ml-auto max-w-xl bg-slate-900 text-white' : 'mr-auto max-w-2xl bg-white text-slate-800 shadow-sm'}`}>
                  <p className="whitespace-pre-wrap text-sm leading-6">{item.content}</p>
                </div>
              ))}
              {messages.length === 0 && <p className="text-sm text-slate-500">Start a conversation to get AI help.</p>}
            </div>

            <form onSubmit={sendMessage} className="mt-4 grid gap-3 md:grid-cols-[1fr_140px]">
              <textarea className="h-24 rounded-xl border p-3" value={message} onChange={(event) => setMessage(event.target.value)} required />
              <button className="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white">Send</button>
            </form>
          </section>
        </div>
      </div>
    </main>
  );
}

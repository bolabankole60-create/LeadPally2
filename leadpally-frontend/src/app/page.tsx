const features = [
  ['Lead Management', 'Capture, organize, and manage every lead in one clean workspace.'],
  ['Sales Pipeline', 'Track prospects from first contact to closed deal with clear stages.'],
  ['Follow-up Tracking', 'Stay on top of calls, messages, reminders, and next actions.'],
  ['Team Collaboration', 'Assign leads, add notes, and keep your team aligned.'],
  ['Email Center', 'Manage sales communication without jumping between tools.'],
  ['Reports & Analytics', 'See what is working and where your sales process needs attention.'],
  ['Campaign Management', 'Track campaigns and know which sources bring quality leads.'],
  ['Workflow Automation', 'Reduce repetitive tasks and keep your process moving.'],
];

const audiences = ['Agencies', 'Freelancers', 'Sales Teams', 'Real Estate', 'Consultants', 'SMEs', 'Service Businesses', 'Online Businesses'];

export default function Home() {
  return (
    <main className="min-h-screen bg-white text-slate-950">
      <div className="bg-purple-900 px-4 py-3 text-center text-sm font-semibold text-white">
        Limited early access slots are open. Secure your spot before pricing changes.
      </div>

      <header className="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-5">
          <a href="#top" className="text-2xl font-black tracking-tight text-purple-800">LeadPally</a>
          <nav className="hidden items-center gap-8 text-sm font-semibold text-slate-600 md:flex">
            <a href="#features" className="hover:text-purple-800">Features</a>
            <a href="#pricing" className="hover:text-purple-800">Pricing</a>
            <a href="#reviews" className="hover:text-purple-800">Reviews</a>
            <a href="#faq" className="hover:text-purple-800">FAQ</a>
            <a href="/dashboard" className="hover:text-purple-800">Dashboard</a>
          </nav>
          <div className="flex items-center gap-3">
            <a href="/login" className="hidden text-sm font-bold text-slate-700 hover:text-purple-800 sm:inline">Login</a>
            <a href="/register" className="rounded-full bg-purple-800 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-purple-200 hover:bg-purple-900">Get Early Access</a>
          </div>
        </div>
      </header>

      <section id="top" className="overflow-hidden bg-gradient-to-br from-purple-50 via-white to-slate-50 py-20 lg:py-28">
        <div className="mx-auto grid max-w-7xl items-center gap-12 px-5 lg:grid-cols-[1fr_0.95fr]">
          <div>
            <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-purple-200 bg-white px-4 py-2 text-sm font-bold text-purple-800 shadow-sm">
              <span className="h-2 w-2 rounded-full bg-purple-700" /> Built for businesses that sell every day
            </div>
            <h1 className="max-w-4xl text-5xl font-black leading-tight tracking-tight text-slate-950 md:text-7xl">
              Stop Losing Leads That Should Become Customers.
            </h1>
            <p className="mt-6 max-w-2xl text-lg leading-8 text-slate-600 md:text-xl">
              LeadPally helps businesses find, manage, follow up, and close leads faster with a simple lead management and sales pipeline system.
            </p>
            <div className="mt-8 flex flex-col gap-4 sm:flex-row">
              <a href="/register" className="rounded-full bg-purple-800 px-8 py-4 text-center text-base font-black text-white shadow-xl shadow-purple-200 hover:bg-purple-900">Start Free Trial</a>
              <a href="/login" className="rounded-full border-2 border-slate-300 bg-white px-8 py-4 text-center text-base font-black text-slate-800 hover:border-purple-800 hover:text-purple-800">Login / Try Demo</a>
            </div>
            <div className="mt-6 flex flex-wrap gap-3 text-sm font-semibold text-slate-500">
              <span>✓ No card needed</span><span>✓ Setup in minutes</span><span>✓ Built for growing teams</span>
            </div>
          </div>

          <div className="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-2xl shadow-purple-100">
            <div className="rounded-[1.5rem] bg-slate-950 p-4 text-white">
              <div className="mb-4 flex items-center justify-between">
                <div>
                  <p className="text-xs text-slate-400">LeadPally dashboard</p>
                  <h3 className="text-lg font-black">Sales overview</h3>
                </div>
                <span className="rounded-full bg-purple-700 px-3 py-1 text-xs font-bold">Live</span>
              </div>
              <div className="grid gap-3 sm:grid-cols-2">
                <div className="rounded-2xl bg-white p-4 text-slate-950"><p className="text-xs font-bold text-slate-500">Total leads</p><p className="mt-1 text-3xl font-black">2,350</p></div>
                <div className="rounded-2xl bg-white p-4 text-slate-950"><p className="text-xs font-bold text-slate-500">Follow-ups</p><p className="mt-1 text-3xl font-black">560</p></div>
                <div className="rounded-2xl bg-white p-4 text-slate-950"><p className="text-xs font-bold text-slate-500">Open deals</p><p className="mt-1 text-3xl font-black">156</p></div>
                <div className="rounded-2xl bg-white p-4 text-slate-950"><p className="text-xs font-bold text-slate-500">New leads</p><p className="mt-1 text-3xl font-black">42</p></div>
              </div>
              <div className="mt-4 rounded-2xl bg-white p-4 text-slate-950">
                <div className="mb-3 flex items-center justify-between"><p className="font-black">Pipeline</p><p className="text-xs font-bold text-purple-800">This month</p></div>
                <div className="space-y-3">
                  {[80, 62, 48, 34].map((width, index) => (
                    <div key={width} className="flex items-center gap-3">
                      <span className="w-20 text-xs font-bold text-slate-500">Stage {index + 1}</span>
                      <div className="h-3 flex-1 rounded-full bg-slate-100"><div className="h-3 rounded-full bg-purple-700" style={{ width: `${width}%` }} /></div>
                    </div>
                  ))}
                </div>
              </div>
              <div className="mt-4 grid gap-3 sm:grid-cols-2">
                <div className="rounded-2xl bg-purple-700 p-4"><p className="text-xs font-bold text-purple-100">Recent activity</p><p className="mt-2 text-sm font-semibold">12 leads moved forward today</p></div>
                <div className="rounded-2xl bg-slate-800 p-4"><p className="text-xs font-bold text-slate-300">Top source</p><p className="mt-2 text-sm font-semibold">Website forms</p></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="border-y border-slate-100 bg-white py-12">
        <div className="mx-auto grid max-w-6xl grid-cols-2 gap-6 px-5 text-center md:grid-cols-4">
          {['1,000+ leads managed', '60s to add a lead', '98% follow-ups tracked', '0 lost opportunities'].map((stat) => (
            <div key={stat} className="rounded-2xl bg-slate-50 p-5"><p className="text-2xl font-black text-purple-800">{stat.split(' ')[0]}</p><p className="mt-1 text-sm font-bold text-slate-600">{stat.substring(stat.indexOf(' ') + 1)}</p></div>
          ))}
        </div>
      </section>

      <section id="pricing" className="bg-slate-950 py-20 text-white">
        <div className="mx-auto max-w-7xl px-5">
          <div className="mx-auto mb-10 max-w-3xl text-center">
            <p className="font-black uppercase tracking-[0.25em] text-purple-300">Early access deal</p>
            <h2 className="mt-3 text-4xl font-black md:text-5xl">One-time access. Long-term value.</h2>
            <p className="mt-4 text-slate-300">Limited beta slots. Secure your access before the price goes up.</p>
          </div>
          <div className="mx-auto grid max-w-5xl gap-6 md:grid-cols-[1fr_1.15fr_1fr]">
            <div className="rounded-3xl border border-white/10 bg-white/5 p-6"><h3 className="text-2xl font-black">Why now?</h3><p className="mt-3 text-sm leading-7 text-slate-300">Get in early, organize your leads, and build a repeatable sales process before your pipeline gets messy.</p></div>
            <div className="rounded-3xl bg-white p-8 text-slate-950 shadow-2xl">
              <p className="text-sm font-black text-purple-800">Lifetime beta access</p>
              <div className="mt-4 flex items-end gap-3"><span className="text-5xl font-black">₦59,000</span><span className="pb-2 text-slate-400 line-through">₦118,000</span></div>
              <p className="mt-2 text-sm font-semibold text-slate-500">One-time payment during beta</p>
              <a href="/register" className="mt-6 block rounded-full bg-purple-800 px-6 py-4 text-center font-black text-white hover:bg-purple-900">Get Early Access Now</a>
              <p className="mt-3 text-center text-xs font-semibold text-slate-500">Secure payment. Limited slots.</p>
            </div>
            <div className="rounded-3xl border border-white/10 bg-white/5 p-6"><h3 className="text-2xl font-black">Included</h3><ul className="mt-4 space-y-3 text-sm font-semibold text-slate-300">{['CRM dashboard','Lead management','Sales pipeline','Team collaboration','Reports','Workflow automation'].map((item) => <li key={item}>✓ {item}</li>)}</ul></div>
          </div>
        </div>
      </section>

      <section id="reviews" className="bg-white py-20">
        <div className="mx-auto max-w-7xl px-5">
          <h2 className="mx-auto max-w-3xl text-center text-4xl font-black md:text-5xl">Loved by sales teams and growing businesses</h2>
          <div className="mt-10 grid gap-6 md:grid-cols-3">
            {['LeadPally helped us organize our follow-ups.', 'We finally know which leads to focus on.', 'Our sales process is clearer and easier to manage.'].map((quote, index) => (
              <div key={quote} className="rounded-3xl border border-slate-100 bg-slate-50 p-8 shadow-sm"><p className="text-purple-700">★★★★★</p><p className="mt-4 leading-7 text-slate-700">&ldquo;{quote}&rdquo;</p><p className="mt-6 font-black text-slate-950">Customer {index + 1}</p><p className="text-sm font-semibold text-slate-500">Growing business</p></div>
            ))}
          </div>
        </div>
      </section>

      <section id="features" className="bg-slate-50 py-20">
        <div className="mx-auto max-w-7xl px-5">
          <div className="mx-auto max-w-3xl text-center"><h2 className="text-4xl font-black md:text-5xl">Everything you need to fill and manage your pipeline</h2><p className="mt-4 text-slate-600">A simple system for managing leads, follow-ups, campaigns, and team activity.</p></div>
          <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {features.map(([title, desc]) => <div key={title} className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"><div className="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-100 text-xl text-purple-800">✓</div><h3 className="font-black">{title}</h3><p className="mt-2 text-sm leading-6 text-slate-600">{desc}</p></div>)}
          </div>
        </div>
      </section>

      <section className="bg-white py-20">
        <div className="mx-auto max-w-7xl px-5">
          <h2 className="mx-auto max-w-3xl text-center text-4xl font-black md:text-5xl">There is the hard way. And then there is LeadPally.</h2>
          <div className="mt-10 grid gap-6 md:grid-cols-2">
            <div className="rounded-3xl border border-red-100 bg-red-50 p-8"><h3 className="text-2xl font-black text-red-700">The old way</h3><ul className="mt-5 space-y-4 text-slate-700"><li>✕ Leads scattered across WhatsApp, notes, and spreadsheets</li><li>✕ Follow-ups forgotten</li><li>✕ No clear pipeline</li><li>✕ No visibility into performance</li></ul></div>
            <div className="rounded-3xl border-2 border-purple-200 bg-purple-50 p-8 shadow-xl"><h3 className="text-2xl font-black text-purple-800">The LeadPally way</h3><ul className="mt-5 space-y-4 text-slate-700"><li>✓ All leads in one CRM</li><li>✓ Clear follow-up tracking</li><li>✓ Organized sales pipeline</li><li>✓ Reports and team visibility</li></ul></div>
          </div>
        </div>
      </section>

      <section className="bg-slate-50 py-20">
        <div className="mx-auto max-w-7xl px-5">
          <h2 className="text-center text-4xl font-black md:text-5xl">Built for every team that sells</h2>
          <div className="mt-10 grid grid-cols-2 gap-4 md:grid-cols-4">
            {audiences.map((item) => <div key={item} className="rounded-2xl bg-white p-5 text-center font-black shadow-sm">{item}</div>)}
          </div>
        </div>
      </section>

      <section className="bg-white py-20">
        <div className="mx-auto max-w-6xl px-5 text-center">
          <h2 className="text-4xl font-black md:text-5xl">How LeadPally works</h2>
          <div className="mt-10 grid gap-6 md:grid-cols-3">
            {['Add or import your leads', 'Track pipeline and follow-ups', 'Close more deals with a simple process'].map((step, index) => <div key={step} className="rounded-3xl border border-slate-100 bg-white p-8 shadow-sm"><div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-800 text-xl font-black text-white">{index + 1}</div><p className="font-black">{step}</p></div>)}
          </div>
        </div>
      </section>

      <section className="bg-gradient-to-r from-purple-900 to-slate-950 py-20 text-white">
        <div className="mx-auto max-w-4xl px-5 text-center"><h2 className="text-4xl font-black md:text-6xl">Your next customer should not slip away.</h2><p className="mt-5 text-purple-100">Start organizing your leads and follow-ups with LeadPally.</p><div className="mt-8 flex flex-col justify-center gap-4 sm:flex-row"><a href="/register" className="rounded-full bg-white px-8 py-4 font-black text-purple-900">Get Early Access</a><a href="/login" className="rounded-full border border-white/40 px-8 py-4 font-black text-white">Try Free First</a></div></div>
      </section>

      <section id="faq" className="bg-white py-20">
        <div className="mx-auto max-w-3xl px-5">
          <h2 className="text-center text-4xl font-black md:text-5xl">Frequently asked questions</h2>
          <div className="mt-10 space-y-4">
            {[['What is LeadPally?', 'LeadPally is a lead management and CRM platform for managing prospects, follow-ups, and sales pipelines.'], ['Can I use it without technical knowledge?', 'Yes. LeadPally is designed to be simple for business owners and sales teams.'], ['Can my team use it?', 'Yes. Teams can collaborate, manage leads, and track performance together.'], ['Can I export my data?', 'Export tools are planned as part of the reporting workflow.']].map(([q, a]) => <details key={q} className="rounded-2xl border border-slate-200 bg-slate-50 p-5"><summary className="cursor-pointer font-black">{q}</summary><p className="mt-3 leading-7 text-slate-600">{a}</p></details>)}
          </div>
        </div>
      </section>

      <footer className="bg-slate-950 py-12 text-white">
        <div className="mx-auto grid max-w-7xl gap-8 px-5 md:grid-cols-4">
          <div className="md:col-span-2"><h3 className="text-2xl font-black">LeadPally</h3><p className="mt-3 max-w-sm text-sm leading-7 text-slate-400">Lead management and sales pipeline platform for growing businesses.</p></div>
          <div><h4 className="font-black">Company</h4><ul className="mt-3 space-y-2 text-sm text-slate-400"><li><a href="#features">Features</a></li><li><a href="#pricing">Pricing</a></li><li><a href="/login">Login</a></li><li><a href="/dashboard">Dashboard</a></li></ul></div>
          <div><h4 className="font-black">Legal</h4><ul className="mt-3 space-y-2 text-sm text-slate-400"><li>Privacy Policy</li><li>Terms</li></ul></div>
        </div>
        <p className="mx-auto mt-10 max-w-7xl px-5 text-sm text-slate-500">© {new Date().getFullYear()} LeadPally. All rights reserved.</p>
      </footer>
    </main>
  );
}

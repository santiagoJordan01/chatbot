import { useEffect, useState } from 'react';
import { api, ensureSession, rows } from '../api/client';
import { useUiStore } from '../state/uiStore';

const stages = [
    ['nuevo', 'Nuevo'],
    ['calificado', 'Calificado'],
    ['cita', 'Cita'],
    ['ganado', 'Ganado'],
];

export function LeadsPage() {
    const leadComposerOpen = useUiStore((s) => s.leadComposerOpen);
    const closeLeadComposer = useUiStore((s) => s.closeLeadComposer);
    const openLeadComposer = useUiStore((s) => s.openLeadComposer);
    const [leads, setLeads] = useState([]);
    const [form, setForm] = useState({ name: '', phone: '', stage: 'nuevo' });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(true);

    async function load() {
        setLoading(true);
        setError('');
        try {
            await ensureSession();
            const data = await api('/leads?per_page=100');
            setLeads(rows(data));
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        load();
    }, []);

    async function createLead(event) {
        event.preventDefault();
        setError('');
        try {
            await ensureSession();
            const me = await api('/auth/me');
            await api('/leads', {
                method: 'POST',
                body: {
                    business_id: me.business_id,
                    name: form.name,
                    phone: form.phone,
                    stage: form.stage,
                    source: 'inbox',
                },
            });
            setForm({ name: '', phone: '', stage: 'nuevo' });
            closeLeadComposer();
            await load();
        } catch (err) {
            setError(err.message);
        }
    }

    return (
        <div className="space-y-4">
            <div className="flex justify-end">
                <button type="button" className="rounded-lg bg-ink px-3 py-2 text-sm text-white" onClick={openLeadComposer}>
                    Nuevo Lead
                </button>
            </div>

            {(leadComposerOpen || error) && (
                <form onSubmit={createLead} className="grid gap-2 rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft md:grid-cols-4">
                    <input className="rounded-lg border border-slate-200 px-3 py-2 text-sm" required placeholder="Nombre" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
                    <input className="rounded-lg border border-slate-200 px-3 py-2 text-sm" required placeholder="Teléfono" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} />
                    <select className="rounded-lg border border-slate-200 px-3 py-2 text-sm" value={form.stage} onChange={(e) => setForm({ ...form, stage: e.target.value })}>
                        {stages.map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                    </select>
                    <button type="submit" className="rounded-lg bg-ink px-3 py-2 text-sm text-white">Guardar</button>
                    {error && <p className="md:col-span-4 text-sm text-red-500">{error}</p>}
                </form>
            )}

            {loading && <p className="text-sm text-slate-500">Cargando leads...</p>}

            <div className="grid gap-4 xl:grid-cols-4">
                {stages.map(([value, title]) => {
                    const column = leads.filter((lead) => (lead.stage || 'nuevo') === value);
                    return (
                        <section key={value} className="rounded-2xl border border-slate-200/70 bg-white/80 p-3 shadow-soft">
                            <header className="mb-3 flex items-center justify-between">
                                <h2 className="font-heading text-sm font-semibold text-slate-900">{title}</h2>
                                <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{column.length}</span>
                            </header>
                            <div className="space-y-3">
                                {column.length === 0 && <p className="text-xs text-slate-400">Sin leads</p>}
                                {column.map((lead) => (
                                    <article key={lead.id} className="rounded-xl border border-slate-200 bg-white p-3">
                                        <p className="font-medium text-slate-900">{lead.name}</p>
                                        <p className="mt-1 text-xs text-slate-500">{lead.phone}</p>
                                        <p className="mt-2 text-xs text-slate-500">Score: {lead.score ?? 0}</p>
                                    </article>
                                ))}
                            </div>
                        </section>
                    );
                })}
            </div>
        </div>
    );
}

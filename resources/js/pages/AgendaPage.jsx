import { useEffect, useState } from 'react';
import { api, ensureSession, rows } from '../api/client';

const services = ['limpieza dental', 'consulta general', 'consulta estética', 'control postquirúrgico'];

function formatWhen(value) {
    if (!value) return '';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleString('es-CO', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}

const statusLabel = {
    scheduled: 'Programada',
    confirmed: 'Confirmada',
    canceled: 'Cancelada',
    rescheduled: 'Reprogramada',
};

export function AgendaPage() {
    const [appointments, setAppointments] = useState([]);
    const [leads, setLeads] = useState([]);
    const [open, setOpen] = useState(false);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(true);
    const [form, setForm] = useState({ lead_id: '', service_type: services[0], starts_at: '' });

    async function load() {
        setLoading(true);
        setError('');
        try {
            await ensureSession();
            const [appointmentData, leadData] = await Promise.all([
                api('/appointments?per_page=100'),
                api('/leads?per_page=100'),
            ]);
            setAppointments(rows(appointmentData));
            setLeads(rows(leadData));
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        load();
    }, []);

    async function createAppointment(event) {
        event.preventDefault();
        setError('');
        try {
            await ensureSession();
            const me = await api('/auth/me');
            const starts = new Date(form.starts_at);
            const ends = new Date(starts.getTime() + 90 * 60 * 1000);
            await api('/appointments', {
                method: 'POST',
                body: {
                    business_id: me.business_id,
                    lead_id: Number(form.lead_id),
                    service_type: form.service_type,
                    starts_at: starts.toISOString(),
                    ends_at: ends.toISOString(),
                },
            });
            setForm({ lead_id: '', service_type: services[0], starts_at: '' });
            setOpen(false);
            await load();
        } catch (err) {
            setError(err.message);
        }
    }

    return (
        <section className="rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft">
            <header className="mb-4 flex items-center justify-between">
                <h2 className="font-heading text-lg font-semibold">Agenda del dia</h2>
                <button type="button" className="rounded-lg bg-ink px-3 py-2 text-sm text-white" onClick={() => setOpen(true)}>Nueva cita</button>
            </header>

            {open && (
                <form onSubmit={createAppointment} className="mb-4 grid gap-2 md:grid-cols-4">
                    <select className="rounded-lg border border-slate-200 px-3 py-2 text-sm" required value={form.lead_id} onChange={(e) => setForm({ ...form, lead_id: e.target.value })}>
                        <option value="">Lead</option>
                        {leads.map((lead) => <option key={lead.id} value={lead.id}>{lead.name}</option>)}
                    </select>
                    <select className="rounded-lg border border-slate-200 px-3 py-2 text-sm" value={form.service_type} onChange={(e) => setForm({ ...form, service_type: e.target.value })}>
                        {services.map((service) => <option key={service} value={service}>{service}</option>)}
                    </select>
                    <input className="rounded-lg border border-slate-200 px-3 py-2 text-sm" required type="datetime-local" value={form.starts_at} onChange={(e) => setForm({ ...form, starts_at: e.target.value })} />
                    <button type="submit" className="rounded-lg bg-ink px-3 py-2 text-sm text-white">Guardar</button>
                </form>
            )}

            {error && <p className="mb-3 text-sm text-red-500">{error}</p>}
            {loading && <p className="mb-3 text-sm text-slate-500">Cargando agenda...</p>}

            <div className="overflow-hidden rounded-xl border border-slate-200">
                <table className="w-full text-left text-sm">
                    <thead className="bg-slate-50 text-slate-600">
                        <tr>
                            <th className="px-3 py-2">Hora</th>
                            <th className="px-3 py-2">Cliente</th>
                            <th className="px-3 py-2">Servicio</th>
                            <th className="px-3 py-2">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        {appointments.length === 0 && (
                            <tr className="border-t border-slate-200">
                                <td className="px-3 py-3 text-slate-400" colSpan={4}>No hay citas</td>
                            </tr>
                        )}
                        {appointments.map((item) => (
                            <tr key={item.id} className="border-t border-slate-200">
                                <td className="px-3 py-2">{formatWhen(item.starts_at)}</td>
                                <td className="px-3 py-2">{item.lead_name || 'Sin nombre'}</td>
                                <td className="px-3 py-2">{item.service_type}</td>
                                <td className="px-3 py-2">
                                    <span className="rounded-full bg-emerald-100 px-2 py-1 text-xs text-emerald-700">{statusLabel[item.status] || item.status}</span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </section>
    );
}

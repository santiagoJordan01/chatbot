import { useEffect, useState } from 'react';
import { api, ensureSession, rows } from '../api/client';

export function DashboardPage() {
    const [cards, setCards] = useState([
        { title: 'Leads', value: '—' },
        { title: 'Citas', value: '—' },
        { title: 'Citas confirmadas', value: '—' },
        { title: 'Conversaciones', value: '—' },
    ]);
    const [error, setError] = useState('');

    useEffect(() => {
        let active = true;

        async function load() {
            try {
                await ensureSession();
                const [leadData, appointmentData, conversationData] = await Promise.all([
                    api('/leads?per_page=100'),
                    api('/appointments?per_page=100'),
                    api('/conversations?per_page=100'),
                ]);
                if (!active) return;
                const leads = rows(leadData);
                const appointments = rows(appointmentData);
                const conversations = rows(conversationData);
                setCards([
                    { title: 'Leads', value: String(leads.length) },
                    { title: 'Citas', value: String(appointments.length) },
                    { title: 'Citas confirmadas', value: String(appointments.filter((item) => item.status === 'confirmed').length) },
                    { title: 'Conversaciones', value: String(conversations.length) },
                ]);
            } catch (err) {
                if (active) setError(err.message);
            }
        }

        load();
        return () => {
            active = false;
        };
    }, []);

    return (
        <div className="space-y-4">
            {error && <p className="text-sm text-red-500">{error}</p>}
            <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                {cards.map((card) => (
                    <article key={card.title} className="rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft">
                        <p className="text-sm text-slate-500">{card.title}</p>
                        <p className="mt-2 font-heading text-3xl font-semibold text-slate-900">{card.value}</p>
                    </article>
                ))}
            </section>
        </div>
    );
}

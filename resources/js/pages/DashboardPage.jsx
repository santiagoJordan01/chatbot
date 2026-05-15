import { kpiCards } from '../data/mockData';

export function DashboardPage() {
    return (
        <div className="space-y-4">
            <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                {kpiCards.map((card) => (
                    <article key={card.title} className="rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft">
                        <p className="text-sm text-slate-500">{card.title}</p>
                        <p className="mt-2 font-heading text-3xl font-semibold text-slate-900">{card.value}</p>
                        <p className="mt-1 text-xs text-success">{card.trend}</p>
                    </article>
                ))}
            </section>

            <section className="grid gap-4 xl:grid-cols-[1.4fr_1fr]">
                <article className="rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft">
                    <h2 className="font-heading text-lg font-semibold">Rendimiento semanal</h2>
                    <div className="mt-4 h-64 rounded-xl bg-gradient-to-br from-cyan-100 via-white to-emerald-100 p-4">
                        <p className="text-sm text-slate-600">Espacio para chart de conversion y volumen.</p>
                    </div>
                </article>
                <article className="rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft">
                    <h2 className="font-heading text-lg font-semibold">Actividad del equipo</h2>
                    <ul className="mt-4 space-y-3 text-sm text-slate-600">
                        <li>Andrea cerro 6 conversaciones en menos de 5 minutos.</li>
                        <li>Se confirmaron 11 citas por WhatsApp en la manana.</li>
                        <li>3 leads calientes pendientes de contacto humano.</li>
                    </ul>
                </article>
            </section>
        </div>
    );
}

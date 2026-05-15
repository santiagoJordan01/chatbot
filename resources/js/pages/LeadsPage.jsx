import { leadColumns } from '../data/mockData';

export function LeadsPage() {
    const columns = [
        ['Nuevo', leadColumns.nuevo],
        ['Calificado', leadColumns.calificado],
        ['Cita', leadColumns.cita],
        ['Ganado', leadColumns.ganado],
    ];

    return (
        <div className="grid gap-4 xl:grid-cols-4">
            {columns.map(([title, leads]) => (
                <section key={title} className="rounded-2xl border border-slate-200/70 bg-white/80 p-3 shadow-soft">
                    <header className="mb-3 flex items-center justify-between">
                        <h2 className="font-heading text-sm font-semibold text-slate-900">{title}</h2>
                        <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{leads.length}</span>
                    </header>
                    <div className="space-y-3">
                        {leads.map((lead) => (
                            <article key={lead.id} className="rounded-xl border border-slate-200 bg-white p-3">
                                <p className="font-medium text-slate-900">{lead.name}</p>
                                <p className="mt-2 text-xs text-slate-500">Score: {lead.score}</p>
                            </article>
                        ))}
                    </div>
                </section>
            ))}
        </div>
    );
}

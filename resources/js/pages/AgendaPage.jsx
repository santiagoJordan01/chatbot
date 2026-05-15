import { agendaItems } from '../data/mockData';

export function AgendaPage() {
    return (
        <section className="rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft">
            <header className="mb-4 flex items-center justify-between">
                <h2 className="font-heading text-lg font-semibold">Agenda del dia</h2>
                <button type="button" className="rounded-lg bg-ink px-3 py-2 text-sm text-white">Nueva cita</button>
            </header>
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
                        {agendaItems.map((item) => (
                            <tr key={item.id} className="border-t border-slate-200">
                                <td className="px-3 py-2">{item.hour}</td>
                                <td className="px-3 py-2">{item.client}</td>
                                <td className="px-3 py-2">{item.service}</td>
                                <td className="px-3 py-2">
                                    <span className="rounded-full bg-emerald-100 px-2 py-1 text-xs text-emerald-700">{item.status}</span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </section>
    );
}

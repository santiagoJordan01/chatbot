import { conversations, messages } from '../data/mockData';
import { useUiStore } from '../state/uiStore';

export function InboxPage() {
    const activeConversationId = useUiStore((s) => s.activeConversationId);
    const setActiveConversation = useUiStore((s) => s.setActiveConversation);

    return (
        <div className="grid h-[calc(100vh-170px)] gap-4 lg:grid-cols-[320px_minmax(0,1fr)_300px]">
            <section className="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-soft">
                <header className="border-b border-slate-200 px-4 py-3 font-heading text-sm font-semibold">Conversaciones</header>
                <div className="space-y-1 p-2">
                    {conversations.map((chat) => (
                        <button
                            key={chat.id}
                            type="button"
                            onClick={() => setActiveConversation(chat.id)}
                            className={`w-full rounded-xl px-3 py-2 text-left ${
                                activeConversationId === chat.id ? 'bg-cyan-50' : 'hover:bg-slate-100'
                            }`}
                        >
                            <div className="flex items-center justify-between">
                                <p className="font-medium text-slate-900">{chat.name}</p>
                                <p className="text-xs text-slate-500">{chat.time}</p>
                            </div>
                            <p className="mt-1 truncate text-xs text-slate-500">{chat.last}</p>
                        </button>
                    ))}
                </div>
            </section>

            <section className="flex flex-col rounded-2xl border border-slate-200/70 bg-white/80 shadow-soft">
                <header className="border-b border-slate-200 px-4 py-3 font-heading text-sm font-semibold">Andrea Ruiz</header>
                <div className="flex-1 space-y-3 overflow-y-auto p-4">
                    {messages.map((msg) => (
                        <div key={msg.id} className={`flex ${msg.from === 'customer' ? 'justify-start' : 'justify-end'}`}>
                            <div
                                className={`max-w-[75%] rounded-2xl px-3 py-2 text-sm ${
                                    msg.from === 'customer' ? 'bg-slate-100 text-slate-700' : 'bg-ink text-white'
                                }`}
                            >
                                <p>{msg.text}</p>
                                <p className="mt-1 text-[10px] opacity-70">{msg.time}</p>
                            </div>
                        </div>
                    ))}
                </div>
                <footer className="border-t border-slate-200 p-3">
                    <div className="flex gap-2">
                        <input className="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Escribe respuesta..." />
                        <button type="button" className="rounded-xl bg-ink px-4 py-2 text-sm text-white">Enviar</button>
                    </div>
                </footer>
            </section>

            <section className="rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft">
                <h3 className="font-heading text-base font-semibold">Ficha CRM</h3>
                <dl className="mt-4 space-y-2 text-sm">
                    <div className="flex justify-between"><dt className="text-slate-500">Lead score</dt><dd className="font-medium">82</dd></div>
                    <div className="flex justify-between"><dt className="text-slate-500">Estado</dt><dd className="font-medium">Calificado</dd></div>
                    <div className="flex justify-between"><dt className="text-slate-500">Ultima cita</dt><dd className="font-medium">Pendiente</dd></div>
                </dl>
            </section>
        </div>
    );
}

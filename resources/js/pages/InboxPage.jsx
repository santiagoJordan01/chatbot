import { useEffect, useRef, useState } from 'react';
import { ensureSession } from '../api/client';
import { conversations as seedConversations, messages as seedMessages } from '../data/mockData';
import { useUiStore } from '../state/uiStore';

const starters = {
    1: seedMessages,
    2: [{ id: 'c2-1', from: 'customer', text: '¿Tienen turno mañana por la tarde?', time: '09:10' }],
    3: [{ id: 'c3-1', from: 'customer', text: 'Confirmo la cita de mañana a las 11', time: 'Ayer' }],
};

function nowLabel() {
    return new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function extractText(data) {
    if (!data) return '';
    if (typeof data === 'string') return data;
    if (data.choices && data.choices[0]) {
        const choice = data.choices[0];
        return choice.message?.content ?? choice.text ?? '';
    }
    return '';
}

export function InboxPage() {
    const activeConversationId = useUiStore((s) => s.activeConversationId);
    const setActiveConversation = useUiStore((s) => s.setActiveConversation);
    const [chats, setChats] = useState(seedConversations);
    const [threads, setThreads] = useState(starters);
    const [draft, setDraft] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const scroller = useRef(null);

    const active = chats.find((chat) => chat.id === activeConversationId) ?? chats[0];
    const thread = threads[active.id] ?? [];

    useEffect(() => {
        const node = scroller.current;
        if (node) node.scrollTop = node.scrollHeight;
    }, [thread, loading, active.id]);

    async function sendMessage(event) {
        event.preventDefault();
        const text = draft.trim();
        if (!text || loading) return;

        const history = thread.slice(-8).map((message) => ({
            role: message.from === 'customer' ? 'user' : 'assistant',
            content: message.text,
        }));
        const mine = { id: crypto.randomUUID(), from: 'customer', text, time: nowLabel() };

        setThreads((prev) => ({ ...prev, [active.id]: [...(prev[active.id] ?? []), mine] }));
        setChats((prev) => prev.map((chat) => (chat.id === active.id ? { ...chat, last: text, time: mine.time } : chat)));
        setDraft('');
        setError('');
        setLoading(true);

        try {
            const payload = JSON.stringify({ message: text, use_rag: true, history });
            const post = (token) => fetch('/api/v1/chat/groq', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: payload,
            });

            let token = await ensureSession();
            let res = await post(token);
            if (res.status === 401) {
                sessionStorage.removeItem('chatbot_token');
                token = await ensureSession();
                res = await post(token);
            }
            const body = await res.json().catch(() => ({}));
            const reply = body?.ok
                ? extractText(body.data)
                : (body?.error || body?.message || 'No hubo respuesta');
            const answer = reply || 'No hubo respuesta';
            const bot = { id: crypto.randomUUID(), from: 'bot', text: answer, time: nowLabel() };
            setThreads((prev) => ({ ...prev, [active.id]: [...(prev[active.id] ?? []), bot] }));
            setChats((prev) => prev.map((chat) => (chat.id === active.id ? { ...chat, last: answer, time: bot.time } : chat)));
        } catch {
            setError('No se pudo enviar. Revisa que el servidor siga activo.');
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="grid h-[calc(100vh-170px)] gap-4 lg:grid-cols-[320px_minmax(0,1fr)_300px]">
            <section className="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-soft">
                <header className="border-b border-slate-200 px-4 py-3 font-heading text-sm font-semibold">Conversaciones</header>
                <div className="space-y-1 p-2">
                    {chats.map((chat) => (
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
                <header className="border-b border-slate-200 px-4 py-3 font-heading text-sm font-semibold">{active.name}</header>
                <div ref={scroller} className="flex-1 space-y-3 overflow-y-auto p-4">
                    {thread.map((msg) => (
                        <div key={msg.id} className={`flex ${msg.from === 'customer' ? 'justify-start' : 'justify-end'}`}>
                            <div
                                className={`max-w-[75%] rounded-2xl px-3 py-2 text-sm ${
                                    msg.from === 'customer' ? 'bg-slate-100 text-slate-700' : 'bg-ink text-white'
                                }`}
                            >
                                <p className="whitespace-pre-wrap">{msg.text}</p>
                                <p className="mt-1 text-[10px] opacity-70">{msg.time}</p>
                            </div>
                        </div>
                    ))}
                    {loading && <p className="text-xs text-slate-400">Escribiendo respuesta...</p>}
                </div>
                <footer className="border-t border-slate-200 p-3">
                    <form onSubmit={sendMessage} className="flex gap-2">
                        <input
                            className="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"
                            placeholder="Escribe un mensaje..."
                            value={draft}
                            onChange={(event) => setDraft(event.target.value)}
                            disabled={loading}
                        />
                        <button type="submit" className="rounded-xl bg-ink px-4 py-2 text-sm text-white disabled:opacity-50" disabled={loading || !draft.trim()}>
                            {loading ? 'Enviando...' : 'Enviar'}
                        </button>
                    </form>
                    {error && <p className="mt-2 text-xs text-red-500">{error}</p>}
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

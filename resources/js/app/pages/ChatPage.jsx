import React, { useState } from 'react';

export function ChatPage() {
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState('');
    const [loading, setLoading] = useState(false);
    const [useRag, setUseRag] = useState(true);

    function extractText(data) {
        if (!data) return '';
        if (typeof data === 'string') return data;
        if (data.text) return data.text;
        if (data.result) return data.result;
        if (Array.isArray(data)) return data.map(d => extractText(d)).join('\n');
        if (data.choices && data.choices[0]) return data.choices[0].text ?? JSON.stringify(data.choices[0]);
        return JSON.stringify(data);
    }

    async function sendMessage(e) {
        e?.preventDefault();
        if (!input.trim()) return;

        const userMsg = { role: 'user', text: input };
        setMessages(prev => [...prev, userMsg]);
        setLoading(true);

        try {
            const res = await fetch('/api/v1/chat/groq', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ message: input, use_rag: useRag }),
            });

            const body = await res.json();

            let assistantText = '';
            if (body && body.ok) {
                assistantText = extractText(body.data);
            } else if (body && body.error) {
                assistantText = 'Error: ' + body.error;
            } else {
                assistantText = 'No response from server';
            }

            const assistantMsg = { role: 'assistant', text: assistantText };
            setMessages(prev => [...prev, assistantMsg]);
            setInput('');
        } catch (err) {
            setMessages(prev => [...prev, { role: 'assistant', text: 'Request failed' }]);
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="p-4">
            <h1 className="text-2xl mb-4">Chat (Groq + Llama)</h1>

            <div className="mb-4">
                <label className="inline-flex items-center">
                    <input type="checkbox" checked={useRag} onChange={e => setUseRag(e.target.checked)} />
                    <span className="ml-2">Usar RAG</span>
                </label>
            </div>

            <div className="border rounded p-3 mb-4 h-64 overflow-auto bg-white/5">
                {messages.map((m, i) => (
                    <div key={i} className="mb-3">
                        <div className="text-sm text-gray-400">{m.role}</div>
                        <div className="mt-1">{m.text}</div>
                    </div>
                ))}
            </div>

            <form onSubmit={sendMessage} className="flex gap-2">
                <input
                    className="flex-1 border rounded px-3 py-2"
                    value={input}
                    onChange={e => setInput(e.target.value)}
                    placeholder="Escribe un mensaje..."
                    disabled={loading}
                />
                <button type="submit" className="btn btn-primary" disabled={loading}>
                    {loading ? 'Enviando...' : 'Enviar'}
                </button>
            </form>
        </div>
    );
}

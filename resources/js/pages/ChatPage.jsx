import React, { useState } from 'react';

export function ChatPage() {
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState('');
    const [loading, setLoading] = useState(false);
    const [useRag, setUseRag] = useState(false);
    const [token, setToken] = useState(() => sessionStorage.getItem('chatbot_token') || '');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [authError, setAuthError] = useState('');

    function extractText(data) {
        if (!data) return '';
        if (typeof data === 'string') return data;
        if (data.text) return data.text;
        if (data.result) return data.result;
        if (Array.isArray(data)) return data.map(d => extractText(d)).join('\n');
        if (data.choices && data.choices[0]) {
            const choice = data.choices[0];
            return choice.message?.content ?? choice.text ?? JSON.stringify(choice);
        }
        return JSON.stringify(data);
    }

    async function sendMessage(e) {
        e?.preventDefault();
        if (!input.trim() || !token) return;

        const history = messages.slice(-8).map((m) => ({ role: m.role, content: m.text }));
        const userMsg = { role: 'user', text: input };
        setMessages(prev => [...prev, userMsg]);
        setLoading(true);

        try {
            const res = await fetch('/api/v1/chat/groq', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ message: input, use_rag: useRag, history }),
            });

            const body = await res.json();

            let assistantText = '';
            if (body && body.ok) {
                assistantText = extractText(body.data);
            } else if (body && body.error) {
                assistantText = 'Error: ' + body.error;
            } else if (body && body.message) {
                assistantText = body.message;
            } else {
                assistantText = 'No response from server';
            }

            setMessages(prev => [...prev, { role: 'assistant', text: assistantText }]);
            setInput('');
        } catch (err) {
            setMessages(prev => [...prev, { role: 'assistant', text: 'Request failed' }]);
        } finally {
            setLoading(false);
        }
    }

    async function authenticate(path) {
        setAuthError('');
        const res = await fetch(path, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                name: email.split('@')[0] || 'Usuario',
                email,
                password,
                password_confirmation: password,
                device_name: 'chat',
            }),
        });
        const body = await res.json();
        const nextToken = body?.data?.token;
        if (!res.ok || !nextToken) {
            setAuthError(body?.message || 'No se pudo iniciar sesión');
            return;
        }
        sessionStorage.setItem('chatbot_token', nextToken);
        setToken(nextToken);
    }

    function logout() {
        sessionStorage.removeItem('chatbot_token');
        setToken('');
    }

    return (
        <div className="p-4">
            <h1 className="text-2xl mb-4">Chat (Groq + Llama)</h1>

            {!token && (
                <form
                    className="mb-4 flex flex-wrap gap-2"
                    onSubmit={(e) => {
                        e.preventDefault();
                        authenticate('/api/v1/auth/login');
                    }}
                >
                    <input className="border rounded px-3 py-2" type="email" required placeholder="Correo" value={email} onChange={(e) => setEmail(e.target.value)} />
                    <input className="border rounded px-3 py-2" type="password" required minLength={8} placeholder="Contraseña" value={password} onChange={(e) => setPassword(e.target.value)} />
                    <button type="submit" className="btn btn-primary">Entrar</button>
                    <button type="button" className="btn" onClick={() => authenticate('/api/v1/auth/register')}>Crear cuenta</button>
                    {authError && <p className="w-full text-sm text-red-500">{authError}</p>}
                </form>
            )}

            {token && (
                <button type="button" className="mb-4 text-sm underline" onClick={logout}>Cerrar sesión</button>
            )}

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
                    disabled={loading || !token}
                />
                <button type="submit" className="btn btn-primary" disabled={loading || !token}>
                    {loading ? 'Enviando...' : 'Enviar'}
                </button>
            </form>
        </div>
    );
}

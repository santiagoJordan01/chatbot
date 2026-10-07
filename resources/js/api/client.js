export async function ensureSession() {
    let token = sessionStorage.getItem('chatbot_token');

    if (!token) {
        let email = sessionStorage.getItem('inbox_email');
        const password = 'pulse-inbox';
        const payload = {
            name: 'Pulse',
            email,
            password,
            password_confirmation: password,
            device_name: 'inbox',
        };

        if (!email) {
            email = `inbox-${crypto.randomUUID().slice(0, 8)}@pulse.local`;
            payload.email = email;
            const created = await request('/auth/register', payload);
            token = created?.token;
            if (!token) {
                throw new Error(created?.message || 'No se pudo preparar la sesión');
            }
            sessionStorage.setItem('inbox_email', email);
            sessionStorage.setItem('chatbot_token', token);
        } else {
            const logged = await request('/auth/login', payload);
            token = logged?.token;
            if (!token) {
                throw new Error(logged?.message || 'No se pudo preparar la sesión');
            }
            sessionStorage.setItem('chatbot_token', token);
        }
    }

    const me = await api('/auth/me');
    if (!me?.business_id) {
        const slug = `pulse-${crypto.randomUUID().slice(0, 8)}`;
        await api('/businesses', {
            method: 'POST',
            body: {
                name: 'Pulse',
                slug,
                business_type: 'veterinary',
                timezone: 'America/Bogota',
            },
        });
    }

    return sessionStorage.getItem('chatbot_token');
}

export async function api(path, { method = 'GET', body } = {}) {
    const token = sessionStorage.getItem('chatbot_token');
    const res = await fetch(`/api/v1${path}`, {
        method,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
        body: body ? JSON.stringify(body) : undefined,
    });
    const json = await res.json().catch(() => ({}));
    if (!res.ok) {
        const error = new Error(json.message || 'No se pudo completar la acción');
        error.status = res.status;
        error.errors = json.errors;
        throw error;
    }

    return json.data;
}

export function rows(payload) {
    if (Array.isArray(payload)) return payload;
    if (Array.isArray(payload?.data)) return payload.data;
    return [];
}

async function request(path, payload) {
    const res = await fetch(`/api/v1${path}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify(payload),
    });
    const body = await res.json().catch(() => ({}));
    return body?.data ?? body;
}

export const navItems = [
    { label: 'Dashboard', path: '/dashboard' },
    { label: 'Inbox', path: '/inbox' },
    { label: 'Leads', path: '/leads' },
    { label: 'Agenda', path: '/agenda' },
];

export const kpiCards = [
    { title: 'Conversaciones activas', value: '148', trend: '+12%' },
    { title: 'Leads nuevos hoy', value: '37', trend: '+8%' },
    { title: 'Citas confirmadas', value: '24', trend: '+5%' },
    { title: 'Primera respuesta', value: '1m 42s', trend: '-18%' },
];

export const conversations = [
    { id: 1, name: 'Andrea Ruiz', last: 'Hola, quiero cotizar limpieza dental para mi perro', time: '09:45', unread: 2 },
    { id: 2, name: 'Carlos Medina', last: '¿Tienen turno mañana por la tarde?', time: '09:10', unread: 0 },
    { id: 3, name: 'Lucia Vega', last: 'Confirmo la cita de mañana a las 11', time: 'Ayer', unread: 0 },
];

export const messages = [
    { id: 1, from: 'customer', text: 'Hola, quiero cotizar limpieza dental para mi perro', time: '09:42' },
    { id: 2, from: 'bot', text: 'Perfecto, te ayudo con eso. ¿Qué edad tiene tu mascota?', time: '09:43' },
    { id: 3, from: 'customer', text: 'Tiene 4 años', time: '09:45' },
];

export const leadColumns = {
    nuevo: [{ id: 1, name: 'Andrea Ruiz', score: 82 }],
    calificado: [{ id: 2, name: 'Carlos Medina', score: 74 }],
    cita: [{ id: 3, name: 'Lucia Vega', score: 90 }],
    ganado: [{ id: 4, name: 'Marco Soto', score: 95 }],
};

export const agendaItems = [
    { id: 1, hour: '09:00', client: 'Andrea Ruiz', service: 'Limpieza dental', status: 'Confirmada' },
    { id: 2, hour: '11:30', client: 'Carlos Medina', service: 'Consulta estética', status: 'Pendiente' },
    { id: 3, hour: '16:00', client: 'Lucia Vega', service: 'Control postquirúrgico', status: 'Confirmada' },
];

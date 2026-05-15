import { useEffect, useState } from 'react';

export function useRealtimeMock() {
    const [events, setEvents] = useState([]);

    useEffect(() => {
        const timer = setInterval(() => {
            setEvents((prev) => [
                {
                    id: Date.now(),
                    type: 'message.received',
                    text: 'Nuevo mensaje entrante',
                },
                ...prev,
            ].slice(0, 5));
        }, 14000);

        return () => clearInterval(timer);
    }, []);

    return events;
}

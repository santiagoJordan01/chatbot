import { createRoot } from 'react-dom/client';
import { AppProviders } from './app/providers';
import { AppRouter } from './app/router';

const container = document.getElementById('app');

if (container) {
    createRoot(container).render(
        <AppProviders>
            <AppRouter />
        </AppProviders>
    );
}

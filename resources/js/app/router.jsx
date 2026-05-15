import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import { MainLayout } from '../components/MainLayout';
import { AgendaPage } from '../pages/AgendaPage';
import { DashboardPage } from '../pages/DashboardPage';
import { InboxPage } from '../pages/InboxPage';
import { LeadsPage } from '../pages/LeadsPage';
import { ChatPage } from '../pages/ChatPage';

export function AppRouter() {
    return (
        <BrowserRouter>
            <Routes>
                <Route element={<MainLayout />}>
                    <Route index element={<Navigate to="/dashboard" replace />} />
                    <Route path="/dashboard" element={<DashboardPage />} />
                    <Route path="/inbox" element={<InboxPage />} />
                    <Route path="/leads" element={<LeadsPage />} />
                    <Route path="/agenda" element={<AgendaPage />} />
                    <Route path="/chat" element={<ChatPage />} />
                </Route>
            </Routes>
        </BrowserRouter>
    );
}

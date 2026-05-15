import { Outlet } from 'react-router-dom';
import { useRealtimeMock } from '../realtime/useRealtimeMock';
import { useUiStore } from '../state/uiStore';
import { Sidebar } from './Sidebar';
import { Topbar } from './Topbar';

export function MainLayout() {
    const sidebarOpen = useUiStore((s) => s.sidebarOpen);
    const events = useRealtimeMock();

    return (
        <div className="min-h-screen bg-app-gradient p-4 font-body text-slate-800">
            <div className="mx-auto grid max-w-[1400px] gap-4 lg:grid-cols-[260px_minmax(0,1fr)]">
                <div className={sidebarOpen ? 'block' : 'hidden lg:block'}>
                    <Sidebar />
                </div>

                <main>
                    <Topbar />
                    {events.length > 0 && (
                        <div className="mb-4 rounded-xl border border-cyan-200 bg-cyan-50 px-3 py-2 text-xs text-cyan-800">
                            Tiempo real: {events[0].text}
                        </div>
                    )}
                    <Outlet />
                </main>
            </div>
        </div>
    );
}

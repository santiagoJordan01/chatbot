import { useNavigate } from 'react-router-dom';
import { useUiStore } from '../state/uiStore';

export function Topbar() {
    const toggleSidebar = useUiStore((s) => s.toggleSidebar);
    const openLeadComposer = useUiStore((s) => s.openLeadComposer);
    const navigate = useNavigate();

    return (
        <header className="mb-4 flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 shadow-soft backdrop-blur">
            <div>
                <p className="font-heading text-lg font-semibold text-slate-900">CRM Conversacional</p>
                <p className="text-sm text-slate-500">Veterinarias y Clinicas Esteticas</p>
            </div>
            <div className="flex items-center gap-3">
                <button
                    type="button"
                    onClick={toggleSidebar}
                    className="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"
                >
                    Menu
                </button>
                <button
                    type="button"
                    className="rounded-lg bg-ink px-3 py-2 text-sm text-white"
                    onClick={() => {
                        openLeadComposer();
                        navigate('/leads');
                    }}
                >
                    Nuevo Lead
                </button>
            </div>
        </header>
    );
}

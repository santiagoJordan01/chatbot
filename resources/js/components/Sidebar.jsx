import { NavLink } from 'react-router-dom';
import { navItems } from '../data/mockData';

export function Sidebar() {
    return (
        <aside className="h-full rounded-2xl border border-slate-200/70 bg-white/80 p-4 shadow-soft backdrop-blur">
            <div className="mb-8 rounded-xl bg-ink px-4 py-3 text-white">
                <p className="font-heading text-sm uppercase tracking-[0.22em] text-cyan-200">Pulse CRM</p>
                <h1 className="mt-1 font-heading text-xl font-semibold">Control Center</h1>
            </div>

            <nav className="space-y-2">
                {navItems.map((item) => (
                    <NavLink
                        key={item.path}
                        to={item.path}
                        className={({ isActive }) =>
                            `flex items-center justify-between rounded-lg px-3 py-2 text-sm transition ${
                                isActive
                                    ? 'bg-cyan-50 text-cyan-800'
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                            }`
                        }
                    >
                        <span>{item.label}</span>
                        <span className="h-2 w-2 rounded-full bg-slate-300" />
                    </NavLink>
                ))}
            </nav>
        </aside>
    );
}

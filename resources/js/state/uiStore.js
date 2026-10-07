import { create } from 'zustand';

export const useUiStore = create((set) => ({
    sidebarOpen: true,
    activeConversationId: 1,
    leadComposerOpen: false,
    toggleSidebar: () => set((state) => ({ sidebarOpen: !state.sidebarOpen })),
    setActiveConversation: (id) => set({ activeConversationId: id }),
    openLeadComposer: () => set({ leadComposerOpen: true }),
    closeLeadComposer: () => set({ leadComposerOpen: false }),
}));

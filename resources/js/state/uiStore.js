import { create } from 'zustand';

export const useUiStore = create((set) => ({
    sidebarOpen: true,
    activeConversationId: 1,
    toggleSidebar: () => set((state) => ({ sidebarOpen: !state.sidebarOpen })),
    setActiveConversation: (id) => set({ activeConversationId: id }),
}));

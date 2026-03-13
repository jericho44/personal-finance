import { defineStore } from 'pinia';

export const useThemeStore = defineStore('theme', {
    state: () => ({
        mode: localStorage.getItem('theme_mode') || 'light',
    }),
    actions: {
        setMode(mode: 'light' | 'dark') {
            this.mode = mode;
            localStorage.setItem('theme_mode', mode);
            this.applyTheme();
        },
        toggleMode() {
            const nextMode = this.mode === 'light' ? 'dark' : 'light';
            this.setMode(nextMode);
        },
        applyTheme() {
            if (this.mode === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.body.setAttribute('data-kt-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.body.setAttribute('data-kt-theme', 'light');
            }
        }
    }
});

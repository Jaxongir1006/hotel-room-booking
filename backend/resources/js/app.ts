import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
            case name.startsWith('Rooms/'):
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#c9a84c',
        showSpinner: false,
        delay: 100,
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();

// Vue 3.5 + Inertia + reka-ui fragment unmount race produces a benign
// "Cannot read properties of null (reading 'type')" rejection during page
// transitions. The pages render correctly; only the console gets noisy.
// Swallow that specific message in dev so genuine errors stay visible.
if (typeof window !== 'undefined' && import.meta.env.DEV) {
    window.addEventListener('unhandledrejection', (event) => {
        const message = (event.reason as Error | undefined)?.message ?? '';
        if (message.includes("Cannot read properties of null (reading 'type')")) {
            event.preventDefault();
        }
    });
}

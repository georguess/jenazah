import { createInertiaApp } from '@inertiajs/react';
import { TooltipProvider } from '@/components/ui/tooltip';
import { initializeTheme } from '@/hooks/use-appearance';
import AppLayout from '@/layouts/app-layout';
import AuthLayout from '@/layouts/auth-layout';
import SettingsLayout from '@/layouts/settings/layout';

const appName = import.meta.env.VITE_APP_NAME || 'SI Manajemen Jenazah';

// Tata letak dipilih SEKALI di sini berdasarkan nama halaman (Inertia 3).
// Halaman tidak menulis `Halaman.layout = Komponen` (pola Inertia 2);
// bila ada, `Halaman.layout` hanya berisi props untuk tata letak (breadcrumbs).
void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    strictMode: true,
    withApp(app) {
        return <TooltipProvider delayDuration={0}>{app}</TooltipProvider>;
    },
    // Keadaan "memuat" saat berpindah halaman: bilah kemajuan Inertia.
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();

import { usePage } from '@inertiajs/react';

// Alamat bernama dihitung di server (route()) dan dibagikan lewat
// HandleInertiaRequests::share, jadi tidak ada URL harfiah di komponen.
export function useRute() {
    return usePage().props.rute;
}

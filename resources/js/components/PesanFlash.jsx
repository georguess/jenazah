import { usePage } from '@inertiajs/react';
import { CircleAlert, CircleCheck } from 'lucide-react';

/**
 * Keadaan "berhasil" (dan galat umum): pesan flash dari server yang
 * dibagikan lewat HandleInertiaRequests::share. Dipasang sekali di tata letak.
 */
export default function PesanFlash() {
    const { flash } = usePage().props;

    if (!flash.sukses && !flash.galat) {
        return null;
    }

    return (
        <div className="space-y-2 px-4 pt-4">
            {flash.sukses && (
                <div
                    role="status"
                    className="flex items-start gap-2 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200"
                >
                    <CircleCheck className="mt-0.5 size-4 shrink-0" />
                    <span>{flash.sukses}</span>
                </div>
            )}
            {flash.galat && (
                <div
                    role="alert"
                    className="flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200"
                >
                    <CircleAlert className="mt-0.5 size-4 shrink-0" />
                    <span>{flash.galat}</span>
                </div>
            )}
        </div>
    );
}

import { cn } from '@/lib/utils';

const warna = {
    belum_teridentifikasi:
        'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200',
    proses: 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200',
    teridentifikasi:
        'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200',
    aktif: 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200',
    cocok: 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200',
    ditutup:
        'bg-neutral-200 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
    menunggu:
        'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200',
    disetujui:
        'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200',
    ditolak: 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-200',
};

/** Lencana status; label sudah diterjemahkan server, di sini hanya warnanya. */
export default function LencanaStatus({ status, label }) {
    return (
        <span
            className={cn(
                'inline-flex rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap',
                warna[status] ?? 'bg-muted text-foreground',
            )}
        >
            {label}
        </span>
    );
}

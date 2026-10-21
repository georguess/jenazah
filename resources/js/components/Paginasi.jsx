import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

/** Nomor halaman dari meta.links milik resource berpaginasi Laravel. */
export default function Paginasi({ meta }) {
    if (!meta || meta.last_page <= 1) {
        return null;
    }

    return (
        <nav
            aria-label="Paginasi"
            className="mt-4 flex flex-wrap items-center justify-between gap-2 text-sm"
        >
            <p className="text-muted-foreground">
                Menampilkan {meta.from}–{meta.to} dari {meta.total} data
            </p>
            <div className="flex flex-wrap gap-1">
                {meta.links.map((tautan) =>
                    tautan.url ? (
                        <Link
                            key={`${tautan.label}-${tautan.url}`}
                            href={tautan.url}
                            preserveScroll
                            className={cn(
                                'rounded-md border px-3 py-1',
                                tautan.active
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'hover:bg-muted',
                            )}
                        >
                            {tautan.label}
                        </Link>
                    ) : (
                        <span
                            key={tautan.label}
                            className="rounded-md border px-3 py-1 text-muted-foreground"
                        >
                            {tautan.label}
                        </span>
                    ),
                )}
            </div>
        </nav>
    );
}

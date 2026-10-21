/** Bingkai berjudul; isinya ditentukan pemakai lewat children. */
export default function Kartu({ judul, keterangan, aksi, children }) {
    return (
        <section className="rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
            {(judul || aksi) && (
                <header className="mb-4 flex flex-wrap items-start justify-between gap-2">
                    <div className="min-w-0">
                        {judul && (
                            <h2 className="text-lg font-semibold break-words">
                                {judul}
                            </h2>
                        )}
                        {keterangan && (
                            <p className="text-sm text-muted-foreground">
                                {keterangan}
                            </p>
                        )}
                    </div>
                    {aksi && <div className="flex flex-wrap gap-2">{aksi}</div>}
                </header>
            )}
            {children}
        </section>
    );
}

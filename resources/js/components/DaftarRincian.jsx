/** Daftar pasangan label–nilai untuk halaman rincian. */
export default function DaftarRincian({ butir }) {
    return (
        <dl className="grid gap-x-6 gap-y-3 sm:grid-cols-2">
            {butir.map((b) => (
                <div key={b.label} className="min-w-0">
                    <dt className="text-xs text-muted-foreground uppercase">
                        {b.label}
                    </dt>
                    <dd className="text-sm break-words whitespace-pre-line">
                        {b.nilai === null ||
                        b.nilai === undefined ||
                        b.nilai === ''
                            ? '—'
                            : b.nilai}
                    </dd>
                </div>
            ))}
        </dl>
    );
}

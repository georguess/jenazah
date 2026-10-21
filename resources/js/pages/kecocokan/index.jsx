import { Head, Link } from '@inertiajs/react';
import BarisKecocokan from '@/components/BarisKecocokan';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Paginasi from '@/components/Paginasi';
import Tabel from '@/components/Tabel';
import TombolTautan from '@/components/TombolTautan';
import { cn } from '@/lib/utils';

export default function Index({ kecocokan, filter, opsi, can, url }) {
    const tombolTambah = can.tambah && (
        <TombolTautan href={url.tambah}>Ajukan kecocokan</TombolTautan>
    );
    const tab = [{ value: '', label: 'Semua' }, ...opsi.status];

    return (
        <>
            <Head title="Kecocokan Identifikasi" />
            <div className="p-4">
                <Kartu
                    judul="Kecocokan Identifikasi"
                    keterangan="Usulan pasangan jenazah–laporan dan hasil verifikasinya."
                    aksi={tombolTambah}
                >
                    <nav
                        className="mb-4 flex flex-wrap gap-2"
                        aria-label="Saring status"
                    >
                        {tab.map((t) => (
                            <Link
                                key={t.value}
                                href={url.index}
                                data={t.value ? { status: t.value } : {}}
                                preserveScroll
                                className={cn(
                                    'rounded-md border px-3 py-1 text-sm',
                                    filter.status === t.value
                                        ? 'bg-primary text-primary-foreground'
                                        : 'hover:bg-muted',
                                )}
                            >
                                {t.label}
                            </Link>
                        ))}
                    </nav>

                    {kecocokan.data.length === 0 ? (
                        <Kosong
                            pesan={
                                filter.status
                                    ? 'Tidak ada usulan dengan status ini.'
                                    : 'Belum ada usulan kecocokan. Usulan diajukan petugas forensik setelah membandingkan data postmortem dan antemortem.'
                            }
                            aksi={tombolTambah}
                        />
                    ) : (
                        <Tabel
                            kolom={[
                                'Jenazah',
                                'Laporan',
                                'Metode',
                                'Skor',
                                'Status',
                                'Aksi',
                            ]}
                        >
                            {kecocokan.data.map((k) => (
                                <BarisKecocokan key={k.id} kecocokan={k} />
                            ))}
                        </Tabel>
                    )}
                    <Paginasi meta={kecocokan.meta} />
                </Kartu>
            </div>
        </>
    );
}

Index.layout = (props) => ({
    breadcrumbs: [{ title: 'Kecocokan', href: props.url.index }],
});

import { Head, Link, usePage } from '@inertiajs/react';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import TombolTautan from '@/components/TombolTautan';

/**
 * Dasbor semua peran. Kartu statistik hanya ada bila server mengirimnya
 * (Gate "melihat-statistik"); pintasan diambil dari menu yang sudah
 * disaring server, jadi React tidak memeriksa peran sendiri.
 */
export default function Dashboard({ statistik, laporanSaya }) {
    const { auth, menu } = usePage().props;
    const pintasan = menu.slice(1);

    return (
        <>
            <Head title="Dasbor" />
            <div className="flex flex-col gap-4 p-4">
                <Kartu
                    judul={`Selamat datang, ${auth.user.name}`}
                    keterangan={`Masuk sebagai ${auth.user.label_peran}.`}
                >
                    <div className="flex flex-wrap gap-2">
                        {pintasan.map((item) => (
                            <TombolTautan
                                key={item.href}
                                href={item.href}
                                variant="outline"
                            >
                                {item.judul}
                            </TombolTautan>
                        ))}
                    </div>
                </Kartu>

                {statistik && (
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        {statistik.map((s) => (
                            <div
                                key={s.judul}
                                className="rounded-xl border p-4"
                            >
                                <p className="text-sm text-muted-foreground">
                                    {s.judul}
                                </p>
                                <p className="mt-1 text-3xl font-semibold tabular-nums">
                                    {s.nilai}
                                </p>
                                {s.url && (
                                    <Link
                                        href={s.url}
                                        className="mt-2 inline-block text-sm underline underline-offset-4"
                                    >
                                        Lihat
                                    </Link>
                                )}
                            </div>
                        ))}
                    </div>
                )}

                {laporanSaya.length > 0 && (
                    <Kartu judul="Laporan yang Anda catat">
                        <ul className="divide-y">
                            {laporanSaya.map((l) => (
                                <li
                                    key={l.id}
                                    className="flex items-center justify-between gap-2 py-2"
                                >
                                    <Link
                                        href={l.url}
                                        className="min-w-0 break-words hover:underline"
                                    >
                                        {l.nama_korban_hilang}
                                    </Link>
                                    <span className="text-sm text-muted-foreground">
                                        {l.status_label}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </Kartu>
                )}

                {!statistik &&
                    laporanSaya.length === 0 &&
                    pintasan.length === 0 && (
                        <Kosong pesan="Belum ada modul yang tersedia untuk akun Anda. Hubungi admin bila ini keliru." />
                    )}
            </div>
        </>
    );
}

Dashboard.layout = (props) => ({
    breadcrumbs: [{ title: 'Dasbor', href: props.rute.dashboard }],
});

import { Head, Link } from '@inertiajs/react';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Paginasi from '@/components/Paginasi';
import Tabel from '@/components/Tabel';
import TombolTautan from '@/components/TombolTautan';

/** Data Antemortem Saya — server hanya mengirim data milik pengguna ini. */
export default function Index({ antemortem, can, url }) {
    const tombolTambah = can.tambah && (
        <TombolTautan href={url.tambah}>Lengkapi data antemortem</TombolTautan>
    );

    return (
        <>
            <Head title="Data Antemortem Saya" />
            <div className="p-4">
                <Kartu
                    judul="Data Antemortem Saya"
                    keterangan="Ciri-ciri anggota keluarga yang Anda laporkan hilang."
                    aksi={tombolTambah}
                >
                    {antemortem.data.length === 0 ? (
                        <Kosong
                            pesan="Anda belum melengkapi data antemortem. Buat laporan kehilangan terlebih dahulu, lalu lengkapi ciri-ciri korban di sini."
                            aksi={tombolTambah}
                        />
                    ) : (
                        <Tabel
                            kolom={[
                                'Korban',
                                'Golongan darah',
                                'Tinggi',
                                'Aksi',
                            ]}
                        >
                            {antemortem.data.map((a) => (
                                <tr key={a.id} className="align-top">
                                    <td className="max-w-xs px-3 py-2 break-words">
                                        <Link
                                            href={a.url.laporan}
                                            className="font-medium hover:underline"
                                        >
                                            {a.nama_korban_hilang}
                                        </Link>
                                    </td>
                                    <td className="px-3 py-2">
                                        {a.golongan_darah ?? '—'}
                                    </td>
                                    <td className="px-3 py-2">
                                        {a.tinggi_badan_cm
                                            ? `${a.tinggi_badan_cm} cm`
                                            : '—'}
                                    </td>
                                    <td className="px-3 py-2">
                                        <div className="flex flex-wrap gap-2">
                                            <TombolTautan
                                                href={a.url.lihat}
                                                variant="outline"
                                            >
                                                Rincian
                                            </TombolTautan>
                                            {a.can.ubah && (
                                                <TombolTautan
                                                    href={a.url.ubah}
                                                    variant="outline"
                                                >
                                                    Ubah
                                                </TombolTautan>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </Tabel>
                    )}
                    <Paginasi meta={antemortem.meta} />
                </Kartu>
            </div>
        </>
    );
}

Index.layout = (props) => ({
    breadcrumbs: [{ title: 'Data Antemortem Saya', href: props.url.index }],
});

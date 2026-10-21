import { Head, Link } from '@inertiajs/react';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Paginasi from '@/components/Paginasi';
import Tabel from '@/components/Tabel';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

export default function Index({ postmortem, can, url }) {
    const tombolTambah = can.tambah && (
        <TombolTautan href={url.tambah}>Isi hasil pemeriksaan</TombolTautan>
    );

    return (
        <>
            <Head title="Postmortem" />
            <div className="p-4">
                <Kartu
                    judul="Pemeriksaan Postmortem"
                    keterangan="Hasil pemeriksaan forensik per jenazah."
                    aksi={tombolTambah}
                >
                    {postmortem.data.length === 0 ? (
                        <Kosong
                            pesan="Belum ada jenazah yang diperiksa. Pilih jenazah lalu isi hasil pemeriksaannya."
                            aksi={tombolTambah}
                        />
                    ) : (
                        <Tabel
                            kolom={[
                                'Jenazah',
                                'Tanggal',
                                'Pemeriksa',
                                'Gol. darah',
                                'Aksi',
                            ]}
                        >
                            {postmortem.data.map((p) => (
                                <tr key={p.id} className="align-top">
                                    <td className="px-3 py-2 whitespace-nowrap">
                                        <Link
                                            href={p.url.jenazah}
                                            className="font-medium hover:underline"
                                        >
                                            {p.kode_jenazah}
                                        </Link>
                                    </td>
                                    <td className="px-3 py-2 whitespace-nowrap">
                                        {p.tanggal_pemeriksaan}
                                    </td>
                                    <td className="px-3 py-2">{p.pemeriksa}</td>
                                    <td className="px-3 py-2">
                                        {p.golongan_darah ?? '—'}
                                    </td>
                                    <td className="px-3 py-2">
                                        <div className="flex flex-wrap gap-2">
                                            <TombolTautan
                                                href={p.url.lihat}
                                                variant="outline"
                                            >
                                                Rincian
                                            </TombolTautan>
                                            {p.can.ubah && (
                                                <TombolTautan
                                                    href={p.url.ubah}
                                                    variant="outline"
                                                >
                                                    Ubah
                                                </TombolTautan>
                                            )}
                                            {p.can.hapus && (
                                                <TombolHapus
                                                    url={p.url.hapus}
                                                    pertanyaan="Hapus hasil pemeriksaan?"
                                                />
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </Tabel>
                    )}
                    <Paginasi meta={postmortem.meta} />
                </Kartu>
            </div>
        </>
    );
}

Index.layout = (props) => ({
    breadcrumbs: [{ title: 'Postmortem', href: props.url.index }],
});

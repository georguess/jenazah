import { Head } from '@inertiajs/react';
import BarisDokumen from '@/components/BarisDokumen';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Paginasi from '@/components/Paginasi';
import Tabel from '@/components/Tabel';
import TombolTautan from '@/components/TombolTautan';

export default function Index({ dokumen, can, url }) {
    const tombolTambah = can.tambah && (
        <TombolTautan href={url.tambah}>Unggah dokumen</TombolTautan>
    );

    return (
        <>
            <Head title="Dokumen Pendukung" />
            <div className="p-4">
                <Kartu
                    judul="Dokumen Pendukung"
                    keterangan="Foto, sidik jari, berita acara, dan dokumen identitas."
                    aksi={tombolTambah}
                >
                    {dokumen.data.length === 0 ? (
                        <Kosong
                            pesan="Belum ada dokumen yang diunggah. Dokumen selalu ditautkan ke satu jenazah atau satu laporan."
                            aksi={tombolTambah}
                        />
                    ) : (
                        <Tabel
                            kolom={[
                                'Jenis',
                                'Milik',
                                'Pengunggah',
                                'Diunggah',
                                'Aksi',
                            ]}
                        >
                            {dokumen.data.map((d) => (
                                <BarisDokumen key={d.id} dokumen={d} />
                            ))}
                        </Tabel>
                    )}
                    <Paginasi meta={dokumen.meta} />
                </Kartu>
            </div>
        </>
    );
}

Index.layout = (props) => ({
    breadcrumbs: [{ title: 'Dokumen', href: props.url.index }],
});

import { Head } from '@inertiajs/react';
import BarisJenazah from '@/components/BarisJenazah';
import FilterCari from '@/components/FilterCari';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Paginasi from '@/components/Paginasi';
import Tabel from '@/components/Tabel';
import TombolTautan from '@/components/TombolTautan';

export default function Index({ jenazah, filter, can, url }) {
    const tombolTambah = can.tambah && (
        <TombolTautan href={url.tambah}>Catat jenazah</TombolTautan>
    );

    return (
        <>
            <Head title="Jenazah" />
            <div className="p-4">
                <Kartu
                    judul="Data Jenazah"
                    keterangan="Jenazah temuan/korban beserta status identifikasinya."
                    aksi={tombolTambah}
                >
                    <FilterCari
                        url={url.index}
                        nilaiAwal={filter.cari}
                        placeholder="Cari kode atau lokasi…"
                    />

                    {jenazah.data.length === 0 ? (
                        <Kosong
                            pesan={
                                filter.cari
                                    ? `Tidak ada jenazah dengan kode atau lokasi "${filter.cari}". Coba kata kunci lain.`
                                    : 'Belum ada jenazah yang dicatat. Data akan muncul setelah petugas administrasi mencatat temuan pertama.'
                            }
                            aksi={tombolTambah}
                        />
                    ) : (
                        <Tabel
                            kolom={[
                                'Kode',
                                'Ditemukan',
                                'Lokasi',
                                'Kondisi',
                                'Status',
                                'Aksi',
                            ]}
                        >
                            {jenazah.data.map((j) => (
                                <BarisJenazah key={j.id} jenazah={j} />
                            ))}
                        </Tabel>
                    )}

                    <Paginasi meta={jenazah.meta} />
                </Kartu>
            </div>
        </>
    );
}

Index.layout = (props) => ({
    breadcrumbs: [{ title: 'Jenazah', href: props.url.index }],
});

import { Head } from '@inertiajs/react';
import BarisLaporan from '@/components/BarisLaporan';
import FilterCari from '@/components/FilterCari';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Paginasi from '@/components/Paginasi';
import Tabel from '@/components/Tabel';
import TombolTautan from '@/components/TombolTautan';

export default function Index({ laporan, filter, can, url }) {
    const tombolTambah = can.tambah && (
        <TombolTautan href={url.tambah}>Buat laporan</TombolTautan>
    );

    return (
        <>
            <Head title="Laporan Kehilangan" />
            <div className="p-4">
                <Kartu
                    judul="Laporan Kehilangan"
                    keterangan="Laporan orang hilang yang menjadi dasar pencocokan identitas."
                    aksi={tombolTambah}
                >
                    <FilterCari
                        url={url.index}
                        nilaiAwal={filter.cari}
                        placeholder="Cari nama korban atau pelapor…"
                    />

                    {laporan.data.length === 0 ? (
                        <Kosong
                            pesan={
                                filter.cari
                                    ? `Tidak ada laporan dengan nama "${filter.cari}".`
                                    : 'Belum ada laporan kehilangan. Buat laporan bila ada anggota keluarga yang hilang.'
                            }
                            aksi={tombolTambah}
                        />
                    ) : (
                        <Tabel
                            kolom={[
                                'Korban',
                                'Pelapor',
                                'Hilang sejak',
                                'Lokasi terakhir',
                                'Status',
                                'Aksi',
                            ]}
                        >
                            {laporan.data.map((l) => (
                                <BarisLaporan key={l.id} laporan={l} />
                            ))}
                        </Tabel>
                    )}

                    <Paginasi meta={laporan.meta} />
                </Kartu>
            </div>
        </>
    );
}

Index.layout = (props) => ({
    breadcrumbs: [{ title: 'Laporan Kehilangan', href: props.url.index }],
});

import { Head, useForm } from '@inertiajs/react';
import FormLaporan from '@/components/FormLaporan';
import Kartu from '@/components/Kartu';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Edit({ laporan, opsi, url }) {
    const nilaiAwal = {
        nama_pelapor: laporan.nama_pelapor,
        no_hp_pelapor: laporan.no_hp_pelapor,
        hubungan_dengan_korban: laporan.hubungan_dengan_korban,
        nama_korban_hilang: laporan.nama_korban_hilang,
        jenis_kelamin: laporan.jenis_kelamin ?? '',
        usia_perkiraan: laporan.usia_perkiraan ?? '',
        ciri_fisik: laporan.ciri_fisik ?? '',
        tanggal_hilang: laporan.tanggal_hilang,
        lokasi_terakhir: laporan.lokasi_terakhir,
    };
    // Status hanya ikut dikirim bila server mengizinkan (opsi.status ada).
    const { data, setData, put, processing, errors } = useForm(
        opsi.status ? { ...nilaiAwal, status: laporan.status } : nilaiAwal,
    );

    function kirim(e) {
        e.preventDefault();
        put(url.simpan);
    }

    return (
        <>
            <Head title={`Ubah laporan ${laporan.nama_korban_hilang}`} />
            <div className="p-4">
                <Kartu
                    judul="Ubah Laporan Kehilangan"
                    keterangan={laporan.nama_korban_hilang}
                >
                    <form onSubmit={kirim} className="space-y-6">
                        <FormLaporan
                            data={data}
                            setData={setData}
                            errors={errors}
                            opsi={opsi}
                        />
                        <div className="flex gap-2">
                            <TombolKirim processing={processing}>
                                Simpan perubahan
                            </TombolKirim>
                            <TombolTautan href={url.batal} variant="ghost">
                                Batal
                            </TombolTautan>
                        </div>
                    </form>
                </Kartu>
            </div>
        </>
    );
}

Edit.layout = (props) => ({
    breadcrumbs: [
        { title: props.laporan.nama_korban_hilang, href: props.url.batal },
        { title: 'Ubah', href: props.url.simpan },
    ],
});

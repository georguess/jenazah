import { Head, useForm } from '@inertiajs/react';
import FormLaporan from '@/components/FormLaporan';
import Kartu from '@/components/Kartu';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Create({ opsi, url }) {
    const { data, setData, post, processing, errors } = useForm({
        nama_pelapor: '',
        no_hp_pelapor: '',
        hubungan_dengan_korban: '',
        nama_korban_hilang: '',
        jenis_kelamin: '',
        usia_perkiraan: '',
        ciri_fisik: '',
        tanggal_hilang: '',
        lokasi_terakhir: '',
    });

    function kirim(e) {
        e.preventDefault();
        post(url.simpan);
    }

    return (
        <>
            <Head title="Buat Laporan Kehilangan" />
            <div className="p-4">
                <Kartu
                    judul="Buat Laporan Kehilangan"
                    keterangan="Isian bertanda * wajib diisi. Ciri-ciri tubuh (gigi, golongan darah) dapat dilengkapi setelah laporan tersimpan."
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
                                Kirim laporan
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

Create.layout = (props) => ({
    breadcrumbs: [
        { title: 'Laporan Kehilangan', href: props.url.batal },
        { title: 'Buat', href: props.url.simpan },
    ],
});

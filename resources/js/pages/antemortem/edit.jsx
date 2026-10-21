import { Head, useForm } from '@inertiajs/react';
import FormAntemortem from '@/components/FormAntemortem';
import Kartu from '@/components/Kartu';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Edit({ antemortem, opsi, url }) {
    const { data, setData, put, processing, errors } = useForm({
        gigi_geligi: antemortem.gigi_geligi ?? '',
        ciri_khusus: antemortem.ciri_khusus ?? '',
        golongan_darah: antemortem.golongan_darah ?? '',
        tinggi_badan_cm: antemortem.tinggi_badan_cm ?? '',
        berat_badan_kg: antemortem.berat_badan_kg ?? '',
        riwayat_medis: antemortem.riwayat_medis ?? '',
    });

    function kirim(e) {
        e.preventDefault();
        put(url.simpan);
    }

    return (
        <>
            <Head title="Ubah Data Antemortem" />
            <div className="p-4">
                <Kartu
                    judul="Ubah Data Antemortem"
                    keterangan={antemortem.nama_korban_hilang}
                >
                    <form onSubmit={kirim} className="space-y-6">
                        <FormAntemortem
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
        {
            title: props.antemortem.nama_korban_hilang,
            href: props.antemortem.url.laporan,
        },
        { title: 'Ubah antemortem', href: props.url.simpan },
    ],
});

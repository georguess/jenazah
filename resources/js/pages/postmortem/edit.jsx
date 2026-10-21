import { Head, useForm } from '@inertiajs/react';
import FormPostmortem from '@/components/FormPostmortem';
import Kartu from '@/components/Kartu';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Edit({ postmortem, opsi, url }) {
    const { data, setData, put, processing, errors } = useForm({
        tanggal_pemeriksaan: postmortem.tanggal_pemeriksaan,
        gigi_geligi: postmortem.gigi_geligi ?? '',
        ciri_khusus: postmortem.ciri_khusus ?? '',
        golongan_darah: postmortem.golongan_darah ?? '',
        estimasi_tinggi_badan_cm: postmortem.estimasi_tinggi_badan_cm ?? '',
        estimasi_berat_badan_kg: postmortem.estimasi_berat_badan_kg ?? '',
        kode_sampel_dna: postmortem.kode_sampel_dna ?? '',
    });

    function kirim(e) {
        e.preventDefault();
        put(url.simpan);
    }

    return (
        <>
            <Head title="Ubah Hasil Pemeriksaan" />
            <div className="p-4">
                <Kartu
                    judul={`Ubah Hasil Pemeriksaan ${postmortem.kode_jenazah}`}
                >
                    <form onSubmit={kirim} className="space-y-6">
                        <FormPostmortem
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
            title: props.postmortem.kode_jenazah,
            href: props.postmortem.url.jenazah,
        },
        { title: 'Ubah postmortem', href: props.url.simpan },
    ],
});

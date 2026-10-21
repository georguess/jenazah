import { Head, useForm } from '@inertiajs/react';
import FormPostmortem from '@/components/FormPostmortem';
import Isian from '@/components/Isian';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Pilihan from '@/components/Pilihan';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Create({ opsi, bawaan, url }) {
    const { data, setData, post, processing, errors } = useForm({
        jenazah_id: bawaan.jenazah_id,
        tanggal_pemeriksaan: bawaan.tanggal_pemeriksaan,
        gigi_geligi: '',
        ciri_khusus: '',
        golongan_darah: '',
        estimasi_tinggi_badan_cm: '',
        estimasi_berat_badan_kg: '',
        kode_sampel_dna: '',
    });

    function kirim(e) {
        e.preventDefault();
        post(url.simpan);
    }

    return (
        <>
            <Head title="Isi Hasil Pemeriksaan" />
            <div className="p-4">
                <Kartu judul="Isi Hasil Pemeriksaan Postmortem">
                    {opsi.jenazah.length === 0 ? (
                        <Kosong
                            pesan="Semua jenazah yang tercatat sudah memiliki hasil pemeriksaan."
                            aksi={
                                <TombolTautan
                                    href={url.batal}
                                    variant="outline"
                                >
                                    Kembali
                                </TombolTautan>
                            }
                        />
                    ) : (
                        <form onSubmit={kirim} className="space-y-6">
                            <Isian
                                id="jenazah_id"
                                label="Jenazah"
                                error={errors.jenazah_id}
                                wajib
                            >
                                <Pilihan
                                    id="jenazah_id"
                                    value={data.jenazah_id}
                                    opsi={opsi.jenazah}
                                    onChange={(v) => setData('jenazah_id', v)}
                                />
                            </Isian>
                            <FormPostmortem
                                data={data}
                                setData={setData}
                                errors={errors}
                                opsi={opsi}
                            />
                            <div className="flex gap-2">
                                <TombolKirim processing={processing} />
                                <TombolTautan href={url.batal} variant="ghost">
                                    Batal
                                </TombolTautan>
                            </div>
                        </form>
                    )}
                </Kartu>
            </div>
        </>
    );
}

Create.layout = (props) => ({
    breadcrumbs: [{ title: 'Isi hasil pemeriksaan', href: props.url.simpan }],
});

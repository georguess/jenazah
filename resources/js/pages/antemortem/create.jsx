import { Head, useForm } from '@inertiajs/react';
import FormAntemortem from '@/components/FormAntemortem';
import Isian from '@/components/Isian';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Pilihan from '@/components/Pilihan';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Create({ opsi, bawaan, url }) {
    const { data, setData, post, processing, errors } = useForm({
        laporan_kehilangan_id: bawaan.laporan_kehilangan_id,
        gigi_geligi: '',
        ciri_khusus: '',
        golongan_darah: '',
        tinggi_badan_cm: '',
        berat_badan_kg: '',
        riwayat_medis: '',
    });

    function kirim(e) {
        e.preventDefault();
        post(url.simpan);
    }

    return (
        <>
            <Head title="Lengkapi Data Antemortem" />
            <div className="p-4">
                <Kartu judul="Lengkapi Data Antemortem">
                    {opsi.laporan.length === 0 ? (
                        <Kosong
                            pesan="Tidak ada laporan aktif yang belum memiliki data antemortem. Buat laporan kehilangan terlebih dahulu."
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
                                id="laporan_kehilangan_id"
                                label="Laporan kehilangan"
                                error={errors.laporan_kehilangan_id}
                                wajib
                            >
                                <Pilihan
                                    id="laporan_kehilangan_id"
                                    value={data.laporan_kehilangan_id}
                                    opsi={opsi.laporan}
                                    onChange={(v) =>
                                        setData('laporan_kehilangan_id', v)
                                    }
                                />
                            </Isian>
                            <FormAntemortem
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
    breadcrumbs: [
        { title: 'Lengkapi Data Antemortem', href: props.url.simpan },
    ],
});

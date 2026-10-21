import { Head, useForm } from '@inertiajs/react';
import Isian from '@/components/Isian';
import Kartu from '@/components/Kartu';
import Pilihan from '@/components/Pilihan';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';
import { Input } from '@/components/ui/input';

export default function Create({ opsi, bawaan, url }) {
    const { data, setData, post, processing, errors } = useForm({
        jenazah_id: bawaan.jenazah_id,
        laporan_kehilangan_id: '',
        metode_kecocokan: '',
        skor_kecocokan: '',
    });

    function kirim(e) {
        e.preventDefault();
        post(url.simpan);
    }

    return (
        <>
            <Head title="Ajukan Kecocokan" />
            <div className="p-4">
                <Kartu
                    judul="Ajukan Kecocokan"
                    keterangan="Usulan akan menunggu verifikasi akhir oleh admin."
                >
                    <form onSubmit={kirim} className="space-y-6">
                        <div className="grid gap-4 md:grid-cols-2">
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
                            <Isian
                                id="laporan_kehilangan_id"
                                label="Laporan kehilangan (aktif)"
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
                            <Isian
                                id="metode_kecocokan"
                                label="Metode"
                                error={errors.metode_kecocokan}
                                wajib
                            >
                                <Pilihan
                                    id="metode_kecocokan"
                                    value={data.metode_kecocokan}
                                    opsi={opsi.metode}
                                    onChange={(v) =>
                                        setData('metode_kecocokan', v)
                                    }
                                />
                            </Isian>
                            <Isian
                                id="skor_kecocokan"
                                label="Skor kecocokan (0–100)"
                                error={errors.skor_kecocokan}
                                wajib
                            >
                                <Input
                                    id="skor_kecocokan"
                                    type="number"
                                    step="0.01"
                                    min={0}
                                    max={100}
                                    value={data.skor_kecocokan}
                                    onChange={(e) =>
                                        setData(
                                            'skor_kecocokan',
                                            e.target.value,
                                        )
                                    }
                                />
                            </Isian>
                        </div>
                        <div className="flex gap-2">
                            <TombolKirim processing={processing}>
                                Ajukan
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
        { title: 'Kecocokan', href: props.url.batal },
        { title: 'Ajukan', href: props.url.simpan },
    ],
});

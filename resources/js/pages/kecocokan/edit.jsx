import { Head, useForm } from '@inertiajs/react';
import Isian from '@/components/Isian';
import Kartu from '@/components/Kartu';
import Pilihan from '@/components/Pilihan';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';
import { Input } from '@/components/ui/input';

export default function Edit({ kecocokan, opsi, url }) {
    const { data, setData, put, processing, errors } = useForm({
        metode_kecocokan: kecocokan.metode_kecocokan,
        skor_kecocokan: kecocokan.skor_kecocokan,
    });

    function kirim(e) {
        e.preventDefault();
        put(url.simpan);
    }

    return (
        <>
            <Head title="Ubah Usulan Kecocokan" />
            <div className="p-4">
                <Kartu
                    judul="Ubah Usulan Kecocokan"
                    keterangan={`${kecocokan.kode_jenazah} ↔ ${kecocokan.nama_korban_hilang}`}
                >
                    <form onSubmit={kirim} className="space-y-6">
                        <div className="grid gap-4 md:grid-cols-2">
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
                                    kosong={null}
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
        { title: 'Usulan kecocokan', href: props.url.batal },
        { title: 'Ubah', href: props.url.simpan },
    ],
});

import { Head, useForm } from '@inertiajs/react';
import Isian from '@/components/Isian';
import Kartu from '@/components/Kartu';
import Pilihan from '@/components/Pilihan';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';
import { Input } from '@/components/ui/input';

/**
 * Dokumen ditautkan ke TEPAT SATU induk (cermin CHECK num_nonnulls di basis
 * data). Pilihan "jenis induk" adalah state peramban; aturannya tetap
 * divalidasi ulang Form Request di server.
 */
export default function Create({ opsi, bawaan, url }) {
    const { data, setData, post, transform, processing, errors, progress } =
        useForm({
            induk: bawaan.laporan_kehilangan_id ? 'laporan' : 'jenazah',
            jenazah_id: bawaan.jenazah_id,
            laporan_kehilangan_id: bawaan.laporan_kehilangan_id,
            jenis_dokumen: '',
            berkas: null,
        });

    function kirim(e) {
        e.preventDefault();
        // Hanya id induk yang dipilih yang dikirim; `induk` sendiri tidak dikirim.
        transform(({ induk, ...isi }) => ({
            ...isi,
            jenazah_id: induk === 'jenazah' ? isi.jenazah_id : '',
            laporan_kehilangan_id:
                induk === 'laporan' ? isi.laporan_kehilangan_id : '',
        }));
        post(url.simpan, { forceFormData: true });
    }

    return (
        <>
            <Head title="Unggah Dokumen" />
            <div className="p-4">
                <Kartu
                    judul="Unggah Dokumen Pendukung"
                    keterangan="Format JPG, PNG, atau PDF, maksimal 5 MB."
                >
                    <form onSubmit={kirim} className="space-y-6">
                        <div className="grid gap-4 md:grid-cols-2">
                            <Isian id="induk" label="Dokumen milik">
                                <Pilihan
                                    id="induk"
                                    value={data.induk}
                                    kosong={null}
                                    opsi={[
                                        { value: 'jenazah', label: 'Jenazah' },
                                        {
                                            value: 'laporan',
                                            label: 'Laporan kehilangan',
                                        },
                                    ]}
                                    onChange={(v) => setData('induk', v)}
                                />
                            </Isian>
                            {data.induk === 'jenazah' ? (
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
                                        onChange={(v) =>
                                            setData('jenazah_id', v)
                                        }
                                    />
                                </Isian>
                            ) : (
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
                            )}
                            <Isian
                                id="jenis_dokumen"
                                label="Jenis dokumen"
                                error={errors.jenis_dokumen}
                                wajib
                            >
                                <Pilihan
                                    id="jenis_dokumen"
                                    value={data.jenis_dokumen}
                                    opsi={opsi.jenis}
                                    onChange={(v) =>
                                        setData('jenis_dokumen', v)
                                    }
                                />
                            </Isian>
                            <Isian
                                id="berkas"
                                label="Berkas"
                                error={errors.berkas}
                                wajib
                            >
                                <Input
                                    id="berkas"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    onChange={(e) =>
                                        setData(
                                            'berkas',
                                            e.target.files[0] ?? null,
                                        )
                                    }
                                />
                            </Isian>
                        </div>
                        {progress && (
                            <progress
                                value={progress.percentage}
                                max="100"
                                className="w-full"
                            >
                                {progress.percentage}%
                            </progress>
                        )}
                        <div className="flex gap-2">
                            <TombolKirim processing={processing}>
                                Unggah
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
    breadcrumbs: [{ title: 'Unggah dokumen', href: props.url.simpan }],
});

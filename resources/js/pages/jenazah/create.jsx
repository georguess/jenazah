import { Head, useForm } from '@inertiajs/react';
import FormJenazah from '@/components/FormJenazah';
import Kartu from '@/components/Kartu';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Create({ opsi, url }) {
    const { data, setData, post, processing, errors } = useForm({
        kode_jenazah: '',
        tanggal_ditemukan: '',
        lokasi_ditemukan: '',
        jenis_kelamin: '',
        perkiraan_usia: '',
        kondisi_jenazah: 'tidak_dikenali',
        catatan: '',
    });

    function kirim(e) {
        e.preventDefault();
        post(url.simpan);
    }

    return (
        <>
            <Head title="Catat Jenazah" />
            <div className="p-4">
                <Kartu
                    judul="Catat Jenazah"
                    keterangan="Isian bertanda * wajib diisi."
                >
                    <form onSubmit={kirim} className="space-y-6">
                        <FormJenazah
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
                </Kartu>
            </div>
        </>
    );
}

Create.layout = (props) => ({
    breadcrumbs: [
        { title: 'Jenazah', href: props.url.batal },
        { title: 'Catat', href: props.url.simpan },
    ],
});

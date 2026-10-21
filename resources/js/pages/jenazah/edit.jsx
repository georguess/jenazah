import { Head, useForm } from '@inertiajs/react';
import FormJenazah from '@/components/FormJenazah';
import Kartu from '@/components/Kartu';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Edit({ jenazah, opsi, url }) {
    // Nilai awal formulir diambil sekali dari props; selanjutnya isian yang
    // sedang diketik adalah state milik useForm.
    const { data, setData, put, processing, errors } = useForm({
        kode_jenazah: jenazah.kode_jenazah,
        tanggal_ditemukan: jenazah.tanggal_ditemukan,
        lokasi_ditemukan: jenazah.lokasi_ditemukan,
        jenis_kelamin: jenazah.jenis_kelamin ?? '',
        perkiraan_usia: jenazah.perkiraan_usia ?? '',
        kondisi_jenazah: jenazah.kondisi_jenazah,
        catatan: jenazah.catatan ?? '',
    });

    function kirim(e) {
        e.preventDefault();
        put(url.simpan);
    }

    return (
        <>
            <Head title={`Ubah ${jenazah.kode_jenazah}`} />
            <div className="p-4">
                <Kartu
                    judul={`Ubah Jenazah ${jenazah.kode_jenazah}`}
                    keterangan="Status identifikasi tidak diubah di sini; status berubah lewat verifikasi kecocokan."
                >
                    <form onSubmit={kirim} className="space-y-6">
                        <FormJenazah
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
        { title: 'Jenazah', href: props.url.index },
        { title: props.jenazah.kode_jenazah, href: props.url.batal },
        { title: 'Ubah', href: props.url.simpan },
    ],
});

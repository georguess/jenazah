import { Head, useForm } from '@inertiajs/react';
import FormPengguna from '@/components/FormPengguna';
import Kartu from '@/components/Kartu';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Create({ opsi, url }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        role: '',
        password: '',
        password_confirmation: '',
    });

    function kirim(e) {
        e.preventDefault();
        post(url.simpan);
    }

    return (
        <>
            <Head title="Tambah Pengguna" />
            <div className="p-4">
                <Kartu judul="Tambah Pengguna">
                    <form onSubmit={kirim} className="space-y-6">
                        <FormPengguna
                            data={data}
                            setData={setData}
                            errors={errors}
                            opsi={opsi}
                            sandiWajib
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
        { title: 'Pengguna', href: props.url.batal },
        { title: 'Tambah', href: props.url.simpan },
    ],
});

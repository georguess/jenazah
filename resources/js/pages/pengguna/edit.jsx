import { Head, useForm } from '@inertiajs/react';
import FormPengguna from '@/components/FormPengguna';
import Kartu from '@/components/Kartu';
import TombolKirim from '@/components/TombolKirim';
import TombolTautan from '@/components/TombolTautan';

export default function Edit({ pengguna, opsi, url }) {
    const { data, setData, put, processing, errors } = useForm({
        name: pengguna.name,
        email: pengguna.email,
        role: pengguna.role,
        password: '',
        password_confirmation: '',
    });

    function kirim(e) {
        e.preventDefault();
        put(url.simpan);
    }

    return (
        <>
            <Head title={`Ubah ${pengguna.name}`} />
            <div className="p-4">
                <Kartu judul={`Ubah Pengguna ${pengguna.name}`}>
                    <form onSubmit={kirim} className="space-y-6">
                        <FormPengguna
                            data={data}
                            setData={setData}
                            errors={errors}
                            opsi={opsi}
                            sandiWajib={false}
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
        { title: props.pengguna.name, href: props.url.batal },
        { title: 'Ubah', href: props.url.simpan },
    ],
});

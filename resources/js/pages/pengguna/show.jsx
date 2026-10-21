import { Head } from '@inertiajs/react';
import DaftarRincian from '@/components/DaftarRincian';
import Kartu from '@/components/Kartu';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

export default function Show({ pengguna }) {
    return (
        <>
            <Head title={pengguna.name} />
            <div className="p-4">
                <Kartu
                    judul={pengguna.name}
                    aksi={
                        <>
                            {pengguna.can.ubah && (
                                <TombolTautan
                                    href={pengguna.url.ubah}
                                    variant="outline"
                                >
                                    Ubah
                                </TombolTautan>
                            )}
                            {pengguna.can.hapus && (
                                <TombolHapus
                                    url={pengguna.url.hapus}
                                    pertanyaan="Hapus akun ini?"
                                />
                            )}
                        </>
                    }
                >
                    <DaftarRincian
                        butir={[
                            { label: 'Email', nilai: pengguna.email },
                            { label: 'Peran', nilai: pengguna.label_peran },
                            {
                                label: 'Email terverifikasi',
                                nilai: pengguna.terverifikasi ? 'Ya' : 'Belum',
                            },
                        ]}
                    />
                    {!pengguna.can.hapus && (
                        <p className="mt-4 text-sm text-muted-foreground">
                            Akun ini tidak dapat dihapus karena tercatat sebagai
                            pencatat, pemeriksa, pengaju, atau pengunggah data
                            (jejak audit), atau merupakan akun Anda sendiri.
                        </p>
                    )}
                </Kartu>
            </div>
        </>
    );
}

Show.layout = (props) => ({
    breadcrumbs: [
        { title: 'Pengguna', href: props.url.index },
        { title: props.pengguna.name, href: props.pengguna.url.lihat },
    ],
});

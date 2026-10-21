import { Head, Link } from '@inertiajs/react';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import Paginasi from '@/components/Paginasi';
import Tabel from '@/components/Tabel';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

export default function Index({ pengguna, can, url }) {
    const tombolTambah = can.tambah && (
        <TombolTautan href={url.tambah}>Tambah pengguna</TombolTautan>
    );

    return (
        <>
            <Head title="Pengguna" />
            <div className="p-4">
                <Kartu
                    judul="Kelola Pengguna"
                    keterangan="Akun petugas dibuat di sini; registrasi publik selalu menjadi keluarga pelapor."
                    aksi={tombolTambah}
                >
                    {pengguna.data.length === 0 ? (
                        <Kosong
                            pesan="Belum ada pengguna lain."
                            aksi={tombolTambah}
                        />
                    ) : (
                        <Tabel kolom={['Nama', 'Email', 'Peran', 'Aksi']}>
                            {pengguna.data.map((p) => (
                                <tr key={p.id} className="align-top">
                                    <td className="max-w-xs px-3 py-2 break-words">
                                        <Link
                                            href={p.url.lihat}
                                            className="font-medium hover:underline"
                                        >
                                            {p.name}
                                        </Link>
                                    </td>
                                    <td className="px-3 py-2 break-all">
                                        {p.email}
                                    </td>
                                    <td className="px-3 py-2">
                                        {p.label_peran}
                                    </td>
                                    <td className="px-3 py-2">
                                        <div className="flex flex-wrap gap-2">
                                            {p.can.ubah && (
                                                <TombolTautan
                                                    href={p.url.ubah}
                                                    variant="outline"
                                                >
                                                    Ubah
                                                </TombolTautan>
                                            )}
                                            {p.can.hapus && (
                                                <TombolHapus
                                                    url={p.url.hapus}
                                                    pertanyaan={`Hapus akun ${p.name}?`}
                                                />
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </Tabel>
                    )}
                    <Paginasi meta={pengguna.meta} />
                </Kartu>
            </div>
        </>
    );
}

Index.layout = (props) => ({
    breadcrumbs: [{ title: 'Pengguna', href: props.url.index }],
});

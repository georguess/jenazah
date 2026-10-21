import { Head } from '@inertiajs/react';
import Kartu from '@/components/Kartu';
import RincianCiri from '@/components/RincianCiri';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

export default function Show({ antemortem }) {
    return (
        <>
            <Head title="Data Antemortem" />
            <div className="p-4">
                <Kartu
                    judul={`Data Antemortem — ${antemortem.nama_korban_hilang}`}
                    aksi={
                        <>
                            <TombolTautan
                                href={antemortem.url.laporan}
                                variant="outline"
                            >
                                Lihat laporan
                            </TombolTautan>
                            {antemortem.can.ubah && (
                                <TombolTautan
                                    href={antemortem.url.ubah}
                                    variant="outline"
                                >
                                    Ubah
                                </TombolTautan>
                            )}
                            {antemortem.can.hapus && (
                                <TombolHapus
                                    url={antemortem.url.hapus}
                                    pertanyaan="Hapus data antemortem?"
                                />
                            )}
                        </>
                    }
                >
                    <RincianCiri ciri={antemortem} jenis="antemortem" />
                </Kartu>
            </div>
        </>
    );
}

Show.layout = (props) => ({
    breadcrumbs: [
        {
            title: props.antemortem.nama_korban_hilang,
            href: props.antemortem.url.laporan,
        },
        { title: 'Antemortem', href: props.antemortem.url.lihat },
    ],
});

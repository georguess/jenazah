import { Head } from '@inertiajs/react';
import DaftarRincian from '@/components/DaftarRincian';
import Kartu from '@/components/Kartu';
import RincianCiri from '@/components/RincianCiri';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

export default function Show({ postmortem }) {
    return (
        <>
            <Head title={`Postmortem ${postmortem.kode_jenazah}`} />
            <div className="flex flex-col gap-4 p-4">
                <Kartu
                    judul={`Hasil Pemeriksaan — ${postmortem.kode_jenazah}`}
                    aksi={
                        <>
                            <TombolTautan
                                href={postmortem.url.jenazah}
                                variant="outline"
                            >
                                Lihat jenazah
                            </TombolTautan>
                            {postmortem.can.ubah && (
                                <TombolTautan
                                    href={postmortem.url.ubah}
                                    variant="outline"
                                >
                                    Ubah
                                </TombolTautan>
                            )}
                            {postmortem.can.hapus && (
                                <TombolHapus
                                    url={postmortem.url.hapus}
                                    pertanyaan="Hapus hasil pemeriksaan?"
                                />
                            )}
                        </>
                    }
                >
                    <div className="mb-4">
                        <DaftarRincian
                            butir={[
                                {
                                    label: 'Tanggal pemeriksaan',
                                    nilai: postmortem.tanggal_pemeriksaan,
                                },
                                {
                                    label: 'Pemeriksa',
                                    nilai: postmortem.pemeriksa,
                                },
                            ]}
                        />
                    </div>
                    <RincianCiri ciri={postmortem} jenis="postmortem" />
                </Kartu>
            </div>
        </>
    );
}

Show.layout = (props) => ({
    breadcrumbs: [
        {
            title: props.postmortem.kode_jenazah,
            href: props.postmortem.url.jenazah,
        },
        { title: 'Postmortem', href: props.postmortem.url.lihat },
    ],
});

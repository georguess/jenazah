import { Head, Link } from '@inertiajs/react';
import DaftarRincian from '@/components/DaftarRincian';
import FormVerifikasi from '@/components/FormVerifikasi';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import LencanaStatus from '@/components/LencanaStatus';
import RincianCiri from '@/components/RincianCiri';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

export default function Show({ kecocokan, antemortem, postmortem, opsi }) {
    return (
        <>
            <Head title="Rincian Kecocokan" />
            <div className="flex flex-col gap-4 p-4">
                <Kartu
                    judul="Usulan Kecocokan"
                    keterangan={`Diajukan oleh ${kecocokan.pengaju ?? '—'}`}
                    aksi={
                        <>
                            {kecocokan.can.ubah && (
                                <TombolTautan
                                    href={kecocokan.url.ubah}
                                    variant="outline"
                                >
                                    Ubah
                                </TombolTautan>
                            )}
                            {kecocokan.can.hapus && (
                                <TombolHapus
                                    url={kecocokan.url.hapus}
                                    pertanyaan="Hapus usulan ini?"
                                />
                            )}
                        </>
                    }
                >
                    <div className="mb-4">
                        <LencanaStatus
                            status={kecocokan.status_verifikasi}
                            label={kecocokan.status_label}
                        />
                    </div>
                    <DaftarRincian
                        butir={[
                            {
                                label: 'Jenazah',
                                nilai: (
                                    <Link
                                        href={kecocokan.url.jenazah}
                                        className="underline"
                                    >
                                        {kecocokan.kode_jenazah}
                                    </Link>
                                ),
                            },
                            {
                                label: 'Laporan',
                                nilai: (
                                    <Link
                                        href={kecocokan.url.laporan}
                                        className="underline"
                                    >
                                        {kecocokan.nama_korban_hilang}
                                    </Link>
                                ),
                            },
                            { label: 'Metode', nilai: kecocokan.metode_label },
                            { label: 'Skor', nilai: kecocokan.skor_kecocokan },
                            {
                                label: 'Diverifikasi oleh',
                                nilai: kecocokan.verifikator,
                            },
                            {
                                label: 'Tanggal verifikasi',
                                nilai: kecocokan.tanggal_verifikasi,
                            },
                            {
                                label: 'Catatan verifikasi',
                                nilai: kecocokan.catatan_verifikasi,
                            },
                        ]}
                    />
                </Kartu>

                <div className="grid gap-4 lg:grid-cols-2">
                    <Kartu judul="Antemortem (dari keluarga)">
                        {antemortem ? (
                            <RincianCiri ciri={antemortem} jenis="antemortem" />
                        ) : (
                            <Kosong pesan="Laporan ini belum memiliki data antemortem." />
                        )}
                    </Kartu>
                    <Kartu judul="Postmortem (hasil pemeriksaan)">
                        {postmortem ? (
                            <RincianCiri ciri={postmortem} jenis="postmortem" />
                        ) : (
                            <Kosong pesan="Jenazah ini belum diperiksa." />
                        )}
                    </Kartu>
                </div>

                {kecocokan.can.verifikasi && (
                    <Kartu
                        judul="Verifikasi Akhir"
                        keterangan="Bila disetujui, jenazah ditandai teridentifikasi dan laporan ditandai cocok."
                    >
                        <FormVerifikasi
                            url={kecocokan.url.verifikasi}
                            opsi={opsi.status}
                        />
                    </Kartu>
                )}
            </div>
        </>
    );
}

Show.layout = (props) => ({
    breadcrumbs: [
        { title: 'Kecocokan', href: props.url.index },
        {
            title: `${props.kecocokan.kode_jenazah} ↔ ${props.kecocokan.nama_korban_hilang}`,
            href: props.kecocokan.url.lihat,
        },
    ],
});

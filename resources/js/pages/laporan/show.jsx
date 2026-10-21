import { Head } from '@inertiajs/react';
import BarisDokumen from '@/components/BarisDokumen';
import BarisKecocokan from '@/components/BarisKecocokan';
import DaftarRincian from '@/components/DaftarRincian';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import LencanaStatus from '@/components/LencanaStatus';
import RincianCiri from '@/components/RincianCiri';
import Tabel from '@/components/Tabel';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

export default function Show({
    laporan,
    antemortem,
    kecocokan,
    dokumen,
    can,
    url,
}) {
    return (
        <>
            <Head title={`Laporan ${laporan.nama_korban_hilang}`} />
            <div className="flex flex-col gap-4 p-4">
                <Kartu
                    judul={laporan.nama_korban_hilang}
                    keterangan={`Dilaporkan oleh ${laporan.nama_pelapor} (${laporan.hubungan_dengan_korban})`}
                    aksi={
                        <>
                            {laporan.can.ubah && (
                                <TombolTautan
                                    href={laporan.url.ubah}
                                    variant="outline"
                                >
                                    Ubah
                                </TombolTautan>
                            )}
                            {laporan.can.hapus && (
                                <TombolHapus
                                    url={laporan.url.hapus}
                                    pertanyaan="Hapus laporan ini beserta data antemortemnya?"
                                />
                            )}
                        </>
                    }
                >
                    <div className="mb-4">
                        <LencanaStatus
                            status={laporan.status}
                            label={laporan.status_label}
                        />
                    </div>
                    <DaftarRincian
                        butir={[
                            {
                                label: 'No. HP pelapor',
                                nilai: laporan.no_hp_pelapor,
                            },
                            {
                                label: 'Tanggal hilang',
                                nilai: laporan.tanggal_hilang,
                            },
                            {
                                label: 'Lokasi terakhir',
                                nilai: laporan.lokasi_terakhir,
                            },
                            {
                                label: 'Jenis kelamin',
                                nilai: laporan.jenis_kelamin_label,
                            },
                            {
                                label: 'Perkiraan usia',
                                nilai:
                                    laporan.usia_perkiraan &&
                                    `${laporan.usia_perkiraan} tahun`,
                            },
                            { label: 'Ciri fisik', nilai: laporan.ciri_fisik },
                        ]}
                    />
                </Kartu>

                <Kartu
                    judul="Data Antemortem"
                    keterangan="Ciri-ciri sebelum meninggal untuk dicocokkan dengan hasil pemeriksaan forensik."
                    aksi={
                        antemortem && (
                            <TombolTautan
                                href={antemortem.url.lihat}
                                variant="outline"
                            >
                                Rincian
                            </TombolTautan>
                        )
                    }
                >
                    {antemortem ? (
                        <RincianCiri ciri={antemortem} jenis="antemortem" />
                    ) : (
                        <Kosong
                            pesan="Data antemortem belum dilengkapi. Ciri gigi, golongan darah, dan riwayat medis sangat membantu proses identifikasi."
                            aksi={
                                can.tambahAntemortem && (
                                    <TombolTautan href={url.tambahAntemortem}>
                                        Lengkapi data antemortem
                                    </TombolTautan>
                                )
                            }
                        />
                    )}
                </Kartu>

                {kecocokan && (
                    <Kartu judul="Usulan Kecocokan">
                        {kecocokan.length === 0 ? (
                            <Kosong pesan="Belum ada jenazah yang diusulkan cocok dengan laporan ini." />
                        ) : (
                            <Tabel
                                kolom={[
                                    'Jenazah',
                                    'Laporan',
                                    'Metode',
                                    'Skor',
                                    'Status',
                                    'Aksi',
                                ]}
                            >
                                {kecocokan.map((k) => (
                                    <BarisKecocokan key={k.id} kecocokan={k} />
                                ))}
                            </Tabel>
                        )}
                    </Kartu>
                )}

                {dokumen && (
                    <Kartu
                        judul="Dokumen Pendukung"
                        aksi={
                            can.tambahDokumen && (
                                <TombolTautan
                                    href={url.tambahDokumen}
                                    variant="outline"
                                >
                                    Unggah dokumen
                                </TombolTautan>
                            )
                        }
                    >
                        {dokumen.length === 0 ? (
                            <Kosong pesan="Belum ada dokumen (foto korban, KTP/KK) untuk laporan ini." />
                        ) : (
                            <Tabel
                                kolom={[
                                    'Jenis',
                                    'Pengunggah',
                                    'Diunggah',
                                    'Aksi',
                                ]}
                            >
                                {dokumen.map((d) => (
                                    <BarisDokumen
                                        key={d.id}
                                        dokumen={d}
                                        tampilkanInduk={false}
                                    />
                                ))}
                            </Tabel>
                        )}
                    </Kartu>
                )}
            </div>
        </>
    );
}

Show.layout = (props) => ({
    breadcrumbs: [
        ...(props.url.index
            ? [{ title: 'Laporan Kehilangan', href: props.url.index }]
            : []),
        {
            title: props.laporan.nama_korban_hilang,
            href: props.laporan.url.lihat,
        },
    ],
});

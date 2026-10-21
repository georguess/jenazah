import { Head } from '@inertiajs/react';
import BarisDokumen from '@/components/BarisDokumen';
import BarisKecocokan from '@/components/BarisKecocokan';
import DaftarRincian from '@/components/DaftarRincian';
import Kartu from '@/components/Kartu';
import Kosong from '@/components/Kosong';
import LencanaStatus from '@/components/LencanaStatus';
import Tabel from '@/components/Tabel';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

export default function Show({
    jenazah,
    postmortem,
    kecocokan,
    dokumen,
    can,
    url,
}) {
    return (
        <>
            <Head title={`Jenazah ${jenazah.kode_jenazah}`} />
            <div className="flex flex-col gap-4 p-4">
                <Kartu
                    judul={`Jenazah ${jenazah.kode_jenazah}`}
                    keterangan={`Dicatat oleh ${jenazah.pencatat}`}
                    aksi={
                        <>
                            {jenazah.can.ubah && (
                                <TombolTautan
                                    href={jenazah.url.ubah}
                                    variant="outline"
                                >
                                    Ubah
                                </TombolTautan>
                            )}
                            {jenazah.can.hapus && (
                                <TombolHapus
                                    url={jenazah.url.hapus}
                                    pertanyaan="Hapus jenazah ini beserta data turunannya?"
                                />
                            )}
                        </>
                    }
                >
                    <div className="mb-4">
                        <LencanaStatus
                            status={jenazah.status_identifikasi}
                            label={jenazah.status_label}
                        />
                    </div>
                    <DaftarRincian
                        butir={[
                            {
                                label: 'Tanggal ditemukan',
                                nilai: jenazah.tanggal_ditemukan,
                            },
                            {
                                label: 'Lokasi ditemukan',
                                nilai: jenazah.lokasi_ditemukan,
                            },
                            {
                                label: 'Jenis kelamin',
                                nilai: jenazah.jenis_kelamin_label,
                            },
                            {
                                label: 'Perkiraan usia',
                                nilai:
                                    jenazah.perkiraan_usia &&
                                    `${jenazah.perkiraan_usia} tahun`,
                            },
                            { label: 'Kondisi', nilai: jenazah.kondisi_label },
                            { label: 'Catatan', nilai: jenazah.catatan },
                        ]}
                    />
                </Kartu>

                {(postmortem || can.tambahPostmortem) && (
                    <Kartu
                        judul="Pemeriksaan Postmortem"
                        aksi={
                            postmortem && (
                                <TombolTautan
                                    href={postmortem.url.lihat}
                                    variant="outline"
                                >
                                    Rincian
                                </TombolTautan>
                            )
                        }
                    >
                        {postmortem ? (
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
                                    {
                                        label: 'Golongan darah',
                                        nilai: postmortem.golongan_darah,
                                    },
                                    {
                                        label: 'Ciri khusus',
                                        nilai: postmortem.ciri_khusus,
                                    },
                                ]}
                            />
                        ) : (
                            <Kosong
                                pesan="Jenazah ini belum diperiksa. Hasil pemeriksaan forensik diperlukan sebelum mengajukan kecocokan."
                                aksi={
                                    <TombolTautan href={url.tambahPostmortem}>
                                        Isi hasil pemeriksaan
                                    </TombolTautan>
                                }
                            />
                        )}
                    </Kartu>
                )}

                {kecocokan && (
                    <Kartu judul="Usulan Kecocokan">
                        {kecocokan.length === 0 ? (
                            <Kosong pesan="Belum ada usulan kecocokan untuk jenazah ini." />
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
                            <Kosong pesan="Belum ada dokumen (foto, sidik jari, berita acara) untuk jenazah ini." />
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
        { title: 'Jenazah', href: props.url.index },
        { title: props.jenazah.kode_jenazah, href: props.jenazah.url.lihat },
    ],
});

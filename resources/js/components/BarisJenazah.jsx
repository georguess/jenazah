import { Link } from '@inertiajs/react';
import LencanaStatus from '@/components/LencanaStatus';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

/** Satu baris tabel jenazah. Tombol aksi mengikuti `can` dari server. */
export default function BarisJenazah({ jenazah }) {
    return (
        <tr className="align-top">
            <td className="px-3 py-2 font-medium whitespace-nowrap">
                <Link href={jenazah.url.lihat} className="hover:underline">
                    {jenazah.kode_jenazah}
                </Link>
            </td>
            <td className="px-3 py-2 whitespace-nowrap">
                {jenazah.tanggal_ditemukan}
            </td>
            <td className="max-w-xs px-3 py-2 break-words">
                {jenazah.lokasi_ditemukan}
            </td>
            <td className="px-3 py-2">{jenazah.kondisi_label}</td>
            <td className="px-3 py-2">
                <LencanaStatus
                    status={jenazah.status_identifikasi}
                    label={jenazah.status_label}
                />
            </td>
            <td className="px-3 py-2">
                <div className="flex flex-wrap gap-2">
                    <TombolTautan href={jenazah.url.lihat} variant="outline">
                        Rincian
                    </TombolTautan>
                    {jenazah.can.ubah && (
                        <TombolTautan href={jenazah.url.ubah} variant="outline">
                            Ubah
                        </TombolTautan>
                    )}
                    {jenazah.can.hapus && (
                        <TombolHapus
                            url={jenazah.url.hapus}
                            pertanyaan={`Hapus ${jenazah.kode_jenazah}?`}
                        />
                    )}
                </div>
            </td>
        </tr>
    );
}

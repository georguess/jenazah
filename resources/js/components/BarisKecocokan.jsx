import { Link } from '@inertiajs/react';
import LencanaStatus from '@/components/LencanaStatus';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

/** Satu baris usulan kecocokan jenazah ↔ laporan. */
export default function BarisKecocokan({ kecocokan }) {
    return (
        <tr className="align-top">
            <td className="px-3 py-2 whitespace-nowrap">
                <Link
                    href={kecocokan.url.jenazah}
                    className="font-medium hover:underline"
                >
                    {kecocokan.kode_jenazah}
                </Link>
            </td>
            <td className="max-w-xs px-3 py-2 break-words">
                <Link href={kecocokan.url.laporan} className="hover:underline">
                    {kecocokan.nama_korban_hilang}
                </Link>
            </td>
            <td className="px-3 py-2">{kecocokan.metode_label}</td>
            <td className="px-3 py-2 tabular-nums">
                {kecocokan.skor_kecocokan}
            </td>
            <td className="px-3 py-2">
                <LencanaStatus
                    status={kecocokan.status_verifikasi}
                    label={kecocokan.status_label}
                />
            </td>
            <td className="px-3 py-2">
                <div className="flex flex-wrap gap-2">
                    <TombolTautan href={kecocokan.url.lihat} variant="outline">
                        {kecocokan.can.verifikasi ? 'Verifikasi' : 'Rincian'}
                    </TombolTautan>
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
                </div>
            </td>
        </tr>
    );
}

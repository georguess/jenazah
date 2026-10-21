import { Link } from '@inertiajs/react';
import LencanaStatus from '@/components/LencanaStatus';
import TombolHapus from '@/components/TombolHapus';
import TombolTautan from '@/components/TombolTautan';

/** Satu baris laporan kehilangan. */
export default function BarisLaporan({ laporan }) {
    return (
        <tr className="align-top">
            <td className="max-w-xs px-3 py-2 break-words">
                <Link
                    href={laporan.url.lihat}
                    className="font-medium hover:underline"
                >
                    {laporan.nama_korban_hilang}
                </Link>
            </td>
            <td className="max-w-xs px-3 py-2 break-words">
                {laporan.nama_pelapor}
                <span className="block text-xs text-muted-foreground">
                    {laporan.hubungan_dengan_korban}
                </span>
            </td>
            <td className="px-3 py-2 whitespace-nowrap">
                {laporan.tanggal_hilang}
            </td>
            <td className="max-w-xs px-3 py-2 break-words">
                {laporan.lokasi_terakhir}
            </td>
            <td className="px-3 py-2">
                <LencanaStatus
                    status={laporan.status}
                    label={laporan.status_label}
                />
            </td>
            <td className="px-3 py-2">
                <div className="flex flex-wrap gap-2">
                    <TombolTautan href={laporan.url.lihat} variant="outline">
                        Rincian
                    </TombolTautan>
                    {laporan.can.ubah && (
                        <TombolTautan href={laporan.url.ubah} variant="outline">
                            Ubah
                        </TombolTautan>
                    )}
                    {laporan.can.hapus && (
                        <TombolHapus
                            url={laporan.url.hapus}
                            pertanyaan="Hapus laporan ini?"
                        />
                    )}
                </div>
            </td>
        </tr>
    );
}

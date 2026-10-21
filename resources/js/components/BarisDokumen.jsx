import { Link } from '@inertiajs/react';
import TombolHapus from '@/components/TombolHapus';

/** Satu baris dokumen pendukung. Unduhan tetap lewat controller (Policy). */
export default function BarisDokumen({ dokumen, tampilkanInduk = true }) {
    return (
        <tr className="align-top">
            <td className="px-3 py-2">
                <Link
                    href={dokumen.url.lihat}
                    className="font-medium hover:underline"
                >
                    {dokumen.jenis_label}
                </Link>
            </td>
            {tampilkanInduk && (
                <td className="max-w-xs px-3 py-2 break-words">
                    <Link href={dokumen.url.induk} className="hover:underline">
                        {dokumen.induk}
                    </Link>
                </td>
            )}
            <td className="px-3 py-2">{dokumen.pengunggah}</td>
            <td className="px-3 py-2 whitespace-nowrap">
                {dokumen.diunggah_pada}
            </td>
            <td className="px-3 py-2">
                <div className="flex flex-wrap gap-2">
                    {/* Unduhan berkas memang memerlukan muat penuh, jadi <a> dipakai di sini. */}
                    <a
                        href={dokumen.url.unduh}
                        className="text-sm underline underline-offset-4"
                    >
                        Unduh
                    </a>
                    {dokumen.can.hapus && (
                        <TombolHapus
                            url={dokumen.url.hapus}
                            pertanyaan="Hapus dokumen ini?"
                        />
                    )}
                </div>
            </td>
        </tr>
    );
}

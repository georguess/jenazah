import { Head, Link, usePage } from '@inertiajs/react';
import { ShieldAlert } from 'lucide-react';
import { Button } from '@/components/ui/button';

const isi = {
    403: {
        judul: 'Akses ditolak',
        pesan: 'Akun Anda tidak memiliki izin untuk membuka halaman atau melakukan tindakan ini. Bila Anda merasa ini keliru, hubungi admin sistem.',
    },
    404: {
        judul: 'Halaman tidak ditemukan',
        pesan: 'Alamat yang Anda buka tidak ada atau datanya sudah dihapus.',
    },
    500: {
        judul: 'Terjadi kesalahan',
        pesan: 'Server mengalami gangguan. Silakan coba lagi beberapa saat lagi.',
    },
    503: {
        judul: 'Sedang pemeliharaan',
        pesan: 'Aplikasi sedang dalam pemeliharaan. Silakan kembali nanti.',
    },
};

/** Halaman galat ramah (403/404/500/503), dirender server lewat Inertia. */
export default function Galat({ status }) {
    const { rute } = usePage().props;
    const teks = isi[status] ?? isi[500];

    return (
        <>
            <Head title={teks.judul} />
            <div className="flex flex-1 flex-col items-center justify-center gap-4 p-8 text-center">
                <ShieldAlert className="size-12 text-muted-foreground" />
                <p className="text-sm font-semibold text-muted-foreground">
                    Galat {status}
                </p>
                <h1 className="text-2xl font-semibold">{teks.judul}</h1>
                <p className="max-w-md text-muted-foreground">{teks.pesan}</p>
                {rute?.dashboard && (
                    <Button asChild>
                        <Link href={rute.dashboard}>Kembali ke dasbor</Link>
                    </Button>
                )}
            </div>
        </>
    );
}

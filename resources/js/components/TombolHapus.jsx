import { useForm } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

/**
 * Penghapusan dengan konfirmasi. State `konfirmasi` lahir di peramban dan
 * hanya dimiliki baris ini (state serendah mungkin). Keputusan boleh-tidaknya
 * tetap di server: tombol ini hanya ditampilkan bila props `can.hapus` benar,
 * dan controller tetap memanggil Gate::authorize.
 */
export default function TombolHapus({
    url,
    label = 'Hapus',
    pertanyaan = 'Yakin dihapus?',
}) {
    const [konfirmasi, setKonfirmasi] = useState(false);
    const { delete: hapus, processing } = useForm({});

    if (!konfirmasi) {
        return (
            <Button
                type="button"
                size="sm"
                variant="outline"
                className="text-red-600"
                onClick={() => setKonfirmasi(true)}
            >
                {label}
            </Button>
        );
    }

    return (
        <span className="inline-flex flex-wrap items-center gap-2">
            <span className="text-sm">{pertanyaan}</span>
            <Button
                type="button"
                size="sm"
                variant="destructive"
                disabled={processing}
                onClick={() => hapus(url, { preserveScroll: true })}
            >
                {processing && <Spinner />}
                {processing ? 'Menghapus…' : 'Ya, hapus'}
            </Button>
            <Button
                type="button"
                size="sm"
                variant="ghost"
                disabled={processing}
                onClick={() => setKonfirmasi(false)}
            >
                Batal
            </Button>
        </span>
    );
}

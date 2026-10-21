import { useForm } from '@inertiajs/react';
import AreaTeks from '@/components/AreaTeks';
import Isian from '@/components/Isian';
import Pilihan from '@/components/Pilihan';
import TombolKirim from '@/components/TombolKirim';

/** Verifikasi akhir oleh admin. Ditampilkan hanya bila can.verifikasi dari server. */
export default function FormVerifikasi({ url, opsi }) {
    const { data, setData, patch, processing, errors } = useForm({
        status_verifikasi: '',
        catatan_verifikasi: '',
    });

    function kirim(e) {
        e.preventDefault();
        patch(url, { preserveScroll: true });
    }

    return (
        <form onSubmit={kirim} className="space-y-4">
            <Isian
                id="status_verifikasi"
                label="Keputusan"
                error={errors.status_verifikasi}
                wajib
            >
                <Pilihan
                    id="status_verifikasi"
                    value={data.status_verifikasi}
                    opsi={opsi}
                    onChange={(v) => setData('status_verifikasi', v)}
                />
            </Isian>
            <Isian
                id="catatan_verifikasi"
                label="Catatan verifikasi"
                error={errors.catatan_verifikasi}
                bantuan="Wajib diisi bila usulan ditolak."
            >
                <AreaTeks
                    id="catatan_verifikasi"
                    value={data.catatan_verifikasi}
                    onChange={(e) =>
                        setData('catatan_verifikasi', e.target.value)
                    }
                />
            </Isian>
            <TombolKirim processing={processing}>Simpan keputusan</TombolKirim>
        </form>
    );
}

import AreaTeks from '@/components/AreaTeks';
import Isian from '@/components/Isian';
import Pilihan from '@/components/Pilihan';
import { Input } from '@/components/ui/input';

/** Isian laporan kehilangan. Pilihan status hanya muncul bila server mengirim opsi.status. */
export default function FormLaporan({ data, setData, errors, opsi }) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <Isian
                id="nama_pelapor"
                label="Nama pelapor"
                error={errors.nama_pelapor}
                wajib
            >
                <Input
                    id="nama_pelapor"
                    value={data.nama_pelapor}
                    onChange={(e) => setData('nama_pelapor', e.target.value)}
                />
            </Isian>
            <Isian
                id="no_hp_pelapor"
                label="No. HP pelapor"
                error={errors.no_hp_pelapor}
                wajib
                bantuan="Angka saja, 8–15 digit, boleh diawali +."
            >
                <Input
                    id="no_hp_pelapor"
                    inputMode="tel"
                    value={data.no_hp_pelapor}
                    onChange={(e) => setData('no_hp_pelapor', e.target.value)}
                />
            </Isian>
            <Isian
                id="hubungan_dengan_korban"
                label="Hubungan dengan korban"
                error={errors.hubungan_dengan_korban}
                wajib
            >
                <Input
                    id="hubungan_dengan_korban"
                    value={data.hubungan_dengan_korban}
                    maxLength={50}
                    onChange={(e) =>
                        setData('hubungan_dengan_korban', e.target.value)
                    }
                />
            </Isian>
            <Isian
                id="nama_korban_hilang"
                label="Nama korban hilang"
                error={errors.nama_korban_hilang}
                wajib
            >
                <Input
                    id="nama_korban_hilang"
                    value={data.nama_korban_hilang}
                    onChange={(e) =>
                        setData('nama_korban_hilang', e.target.value)
                    }
                />
            </Isian>
            <Isian
                id="jenis_kelamin"
                label="Jenis kelamin"
                error={errors.jenis_kelamin}
            >
                <Pilihan
                    id="jenis_kelamin"
                    value={data.jenis_kelamin}
                    opsi={opsi.jenis_kelamin}
                    onChange={(v) => setData('jenis_kelamin', v)}
                />
            </Isian>
            <Isian
                id="usia_perkiraan"
                label="Perkiraan usia (tahun)"
                error={errors.usia_perkiraan}
            >
                <Input
                    id="usia_perkiraan"
                    type="number"
                    min={0}
                    max={120}
                    value={data.usia_perkiraan}
                    onChange={(e) => setData('usia_perkiraan', e.target.value)}
                />
            </Isian>
            <Isian
                id="tanggal_hilang"
                label="Tanggal hilang"
                error={errors.tanggal_hilang}
                wajib
            >
                <Input
                    id="tanggal_hilang"
                    type="date"
                    value={data.tanggal_hilang}
                    onChange={(e) => setData('tanggal_hilang', e.target.value)}
                />
            </Isian>
            <Isian
                id="lokasi_terakhir"
                label="Lokasi terakhir terlihat"
                error={errors.lokasi_terakhir}
                wajib
            >
                <Input
                    id="lokasi_terakhir"
                    value={data.lokasi_terakhir}
                    onChange={(e) => setData('lokasi_terakhir', e.target.value)}
                />
            </Isian>
            {opsi.status && (
                <Isian id="status" label="Status laporan" error={errors.status}>
                    <Pilihan
                        id="status"
                        value={data.status}
                        opsi={opsi.status}
                        kosong={null}
                        onChange={(v) => setData('status', v)}
                    />
                </Isian>
            )}
            <div className="md:col-span-2">
                <Isian
                    id="ciri_fisik"
                    label="Ciri fisik"
                    error={errors.ciri_fisik}
                >
                    <AreaTeks
                        id="ciri_fisik"
                        value={data.ciri_fisik}
                        onChange={(e) => setData('ciri_fisik', e.target.value)}
                    />
                </Isian>
            </div>
        </div>
    );
}

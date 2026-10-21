import AreaTeks from '@/components/AreaTeks';
import Isian from '@/components/Isian';
import Pilihan from '@/components/Pilihan';
import { Input } from '@/components/ui/input';

/** Isian formulir jenazah, dipakai halaman tambah dan ubah. */
export default function FormJenazah({ data, setData, errors, opsi }) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <Isian
                id="kode_jenazah"
                label="Kode jenazah"
                error={errors.kode_jenazah}
                wajib
                bantuan="Sesuai label kantong jenazah, mis. JZ-2026-004."
            >
                <Input
                    id="kode_jenazah"
                    value={data.kode_jenazah}
                    maxLength={30}
                    onChange={(e) => setData('kode_jenazah', e.target.value)}
                />
            </Isian>
            <Isian
                id="tanggal_ditemukan"
                label="Tanggal ditemukan"
                error={errors.tanggal_ditemukan}
                wajib
            >
                <Input
                    id="tanggal_ditemukan"
                    type="date"
                    value={data.tanggal_ditemukan}
                    onChange={(e) =>
                        setData('tanggal_ditemukan', e.target.value)
                    }
                />
            </Isian>
            <div className="md:col-span-2">
                <Isian
                    id="lokasi_ditemukan"
                    label="Lokasi ditemukan"
                    error={errors.lokasi_ditemukan}
                    wajib
                >
                    <Input
                        id="lokasi_ditemukan"
                        value={data.lokasi_ditemukan}
                        onChange={(e) =>
                            setData('lokasi_ditemukan', e.target.value)
                        }
                    />
                </Isian>
            </div>
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
                id="perkiraan_usia"
                label="Perkiraan usia (tahun)"
                error={errors.perkiraan_usia}
            >
                <Input
                    id="perkiraan_usia"
                    type="number"
                    min={0}
                    max={120}
                    value={data.perkiraan_usia}
                    onChange={(e) => setData('perkiraan_usia', e.target.value)}
                />
            </Isian>
            <Isian
                id="kondisi_jenazah"
                label="Kondisi jenazah"
                error={errors.kondisi_jenazah}
                wajib
            >
                <Pilihan
                    id="kondisi_jenazah"
                    value={data.kondisi_jenazah}
                    opsi={opsi.kondisi_jenazah}
                    onChange={(v) => setData('kondisi_jenazah', v)}
                />
            </Isian>
            <div className="md:col-span-2">
                <Isian id="catatan" label="Catatan" error={errors.catatan}>
                    <AreaTeks
                        id="catatan"
                        value={data.catatan}
                        onChange={(e) => setData('catatan', e.target.value)}
                    />
                </Isian>
            </div>
        </div>
    );
}

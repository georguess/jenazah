import AreaTeks from '@/components/AreaTeks';
import Isian from '@/components/Isian';
import Pilihan from '@/components/Pilihan';
import { Input } from '@/components/ui/input';

/** Ciri tubuh sebelum meninggal (dari keluarga/laporan). */
export default function FormAntemortem({ data, setData, errors, opsi }) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <Isian
                id="golongan_darah"
                label="Golongan darah"
                error={errors.golongan_darah}
            >
                <Pilihan
                    id="golongan_darah"
                    value={data.golongan_darah}
                    opsi={opsi.golongan_darah}
                    kosong="— Tidak diketahui —"
                    onChange={(v) => setData('golongan_darah', v)}
                />
            </Isian>
            <div />
            <Isian
                id="tinggi_badan_cm"
                label="Tinggi badan (cm)"
                error={errors.tinggi_badan_cm}
            >
                <Input
                    id="tinggi_badan_cm"
                    type="number"
                    step="0.1"
                    value={data.tinggi_badan_cm}
                    onChange={(e) => setData('tinggi_badan_cm', e.target.value)}
                />
            </Isian>
            <Isian
                id="berat_badan_kg"
                label="Berat badan (kg)"
                error={errors.berat_badan_kg}
            >
                <Input
                    id="berat_badan_kg"
                    type="number"
                    step="0.1"
                    value={data.berat_badan_kg}
                    onChange={(e) => setData('berat_badan_kg', e.target.value)}
                />
            </Isian>
            <div className="md:col-span-2">
                <Isian
                    id="gigi_geligi"
                    label="Gigi geligi"
                    error={errors.gigi_geligi}
                    bantuan="Gigi patah, tambalan, gigi palsu, kawat gigi, dsb."
                >
                    <AreaTeks
                        id="gigi_geligi"
                        value={data.gigi_geligi}
                        onChange={(e) => setData('gigi_geligi', e.target.value)}
                    />
                </Isian>
            </div>
            <div className="md:col-span-2">
                <Isian
                    id="ciri_khusus"
                    label="Ciri khusus"
                    error={errors.ciri_khusus}
                    bantuan="Tato, tahi lalat, bekas luka/operasi."
                >
                    <AreaTeks
                        id="ciri_khusus"
                        value={data.ciri_khusus}
                        onChange={(e) => setData('ciri_khusus', e.target.value)}
                    />
                </Isian>
            </div>
            <div className="md:col-span-2">
                <Isian
                    id="riwayat_medis"
                    label="Riwayat medis"
                    error={errors.riwayat_medis}
                >
                    <AreaTeks
                        id="riwayat_medis"
                        value={data.riwayat_medis}
                        onChange={(e) =>
                            setData('riwayat_medis', e.target.value)
                        }
                    />
                </Isian>
            </div>
        </div>
    );
}

import AreaTeks from '@/components/AreaTeks';
import Isian from '@/components/Isian';
import Pilihan from '@/components/Pilihan';
import { Input } from '@/components/ui/input';

/** Hasil pemeriksaan forensik terhadap jenazah. */
export default function FormPostmortem({ data, setData, errors, opsi }) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <Isian
                id="tanggal_pemeriksaan"
                label="Tanggal pemeriksaan"
                error={errors.tanggal_pemeriksaan}
                wajib
            >
                <Input
                    id="tanggal_pemeriksaan"
                    type="date"
                    value={data.tanggal_pemeriksaan}
                    onChange={(e) =>
                        setData('tanggal_pemeriksaan', e.target.value)
                    }
                />
            </Isian>
            <Isian
                id="golongan_darah"
                label="Golongan darah"
                error={errors.golongan_darah}
            >
                <Pilihan
                    id="golongan_darah"
                    value={data.golongan_darah}
                    opsi={opsi.golongan_darah}
                    kosong="— Belum diketahui —"
                    onChange={(v) => setData('golongan_darah', v)}
                />
            </Isian>
            <Isian
                id="estimasi_tinggi_badan_cm"
                label="Estimasi tinggi badan (cm)"
                error={errors.estimasi_tinggi_badan_cm}
            >
                <Input
                    id="estimasi_tinggi_badan_cm"
                    type="number"
                    step="0.1"
                    value={data.estimasi_tinggi_badan_cm}
                    onChange={(e) =>
                        setData('estimasi_tinggi_badan_cm', e.target.value)
                    }
                />
            </Isian>
            <Isian
                id="estimasi_berat_badan_kg"
                label="Estimasi berat badan (kg)"
                error={errors.estimasi_berat_badan_kg}
            >
                <Input
                    id="estimasi_berat_badan_kg"
                    type="number"
                    step="0.1"
                    value={data.estimasi_berat_badan_kg}
                    onChange={(e) =>
                        setData('estimasi_berat_badan_kg', e.target.value)
                    }
                />
            </Isian>
            <Isian
                id="kode_sampel_dna"
                label="Kode sampel DNA"
                error={errors.kode_sampel_dna}
            >
                <Input
                    id="kode_sampel_dna"
                    value={data.kode_sampel_dna}
                    maxLength={50}
                    onChange={(e) => setData('kode_sampel_dna', e.target.value)}
                />
            </Isian>
            <div />
            <div className="md:col-span-2">
                <Isian
                    id="gigi_geligi"
                    label="Gigi geligi"
                    error={errors.gigi_geligi}
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
                >
                    <AreaTeks
                        id="ciri_khusus"
                        value={data.ciri_khusus}
                        onChange={(e) => setData('ciri_khusus', e.target.value)}
                    />
                </Isian>
            </div>
        </div>
    );
}

import DaftarRincian from '@/components/DaftarRincian';

/** Ciri tubuh antemortem/postmortem dalam bentuk yang sama agar mudah dibandingkan. */
export default function RincianCiri({ ciri, jenis }) {
    const tinggi =
        jenis === 'postmortem'
            ? ciri.estimasi_tinggi_badan_cm
            : ciri.tinggi_badan_cm;
    const berat =
        jenis === 'postmortem'
            ? ciri.estimasi_berat_badan_kg
            : ciri.berat_badan_kg;

    return (
        <DaftarRincian
            butir={[
                { label: 'Gigi geligi', nilai: ciri.gigi_geligi },
                { label: 'Ciri khusus', nilai: ciri.ciri_khusus },
                { label: 'Golongan darah', nilai: ciri.golongan_darah },
                { label: 'Tinggi badan', nilai: tinggi && `${tinggi} cm` },
                { label: 'Berat badan', nilai: berat && `${berat} kg` },
                jenis === 'postmortem'
                    ? { label: 'Kode sampel DNA', nilai: ciri.kode_sampel_dna }
                    : { label: 'Riwayat medis', nilai: ciri.riwayat_medis },
            ]}
        />
    );
}

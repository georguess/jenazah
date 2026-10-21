import { Head, Link } from '@inertiajs/react';
import DaftarRincian from '@/components/DaftarRincian';
import Kartu from '@/components/Kartu';
import TombolHapus from '@/components/TombolHapus';
import { Button } from '@/components/ui/button';

export default function Show({ dokumen }) {
    return (
        <>
            <Head title={`Dokumen ${dokumen.jenis_label}`} />
            <div className="p-4">
                <Kartu
                    judul={`Dokumen: ${dokumen.jenis_label}`}
                    aksi={
                        <>
                            {/* Unduhan berkas memerlukan muat penuh, jadi memakai <a>. */}
                            <Button asChild size="sm">
                                <a href={dokumen.url.unduh}>Unduh</a>
                            </Button>
                            {dokumen.can.hapus && (
                                <TombolHapus
                                    url={dokumen.url.hapus}
                                    pertanyaan="Hapus dokumen ini?"
                                />
                            )}
                        </>
                    }
                >
                    <DaftarRincian
                        butir={[
                            {
                                label: 'Milik',
                                nilai: (
                                    <Link
                                        href={dokumen.url.induk}
                                        className="underline"
                                    >
                                        {dokumen.induk}
                                    </Link>
                                ),
                            },
                            {
                                label: 'Diunggah oleh',
                                nilai: dokumen.pengunggah,
                            },
                            {
                                label: 'Diunggah pada',
                                nilai: dokumen.diunggah_pada,
                            },
                        ]}
                    />
                </Kartu>
            </div>
        </>
    );
}

Show.layout = (props) => ({
    breadcrumbs: [
        { title: props.dokumen.induk, href: props.dokumen.url.induk },
        { title: props.dokumen.jenis_label, href: props.dokumen.url.lihat },
    ],
});

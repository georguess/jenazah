import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

/** Keadaan "memuat": tombol mati dan berganti tulisan selama pengiriman. */
export default function TombolKirim({
    processing,
    children = 'Simpan',
    ...props
}) {
    return (
        <Button type="submit" disabled={processing} {...props}>
            {processing && <Spinner />}
            {processing ? 'Menyimpan…' : children}
        </Button>
    );
}

import { useForm } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

/** Pencarian di server (ILIKE); peramban hanya mengirim kata kuncinya. */
export default function FilterCari({ url, nilaiAwal, placeholder }) {
    const { data, setData, get, processing } = useForm({
        cari: nilaiAwal ?? '',
    });

    function kirim(e) {
        e.preventDefault();
        get(url, { preserveState: true, preserveScroll: true });
    }

    return (
        <form onSubmit={kirim} className="mb-4 flex gap-2" role="search">
            <Input
                type="search"
                aria-label="Cari"
                value={data.cari}
                onChange={(e) => setData('cari', e.target.value)}
                placeholder={placeholder}
                className="max-w-sm"
            />
            <Button type="submit" variant="outline" disabled={processing}>
                <Search />
                Cari
            </Button>
        </form>
    );
}

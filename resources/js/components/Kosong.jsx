import { Inbox } from 'lucide-react';

/** Keadaan kosong: kalimat yang menjelaskan, ditambah satu tindakan bila ada. */
export default function Kosong({ pesan, aksi }) {
    return (
        <div className="flex flex-col items-center gap-3 rounded-lg border border-dashed p-8 text-center">
            <Inbox className="size-8 text-muted-foreground" />
            <p className="max-w-md text-sm text-muted-foreground">{pesan}</p>
            {aksi}
        </div>
    );
}

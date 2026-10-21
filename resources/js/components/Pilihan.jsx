import { cn } from '@/lib/utils';

/** <select> dengan daftar opsi [{ value, label }] yang dikirim server. */
export default function Pilihan({
    id,
    value,
    onChange,
    opsi,
    kosong = '— Pilih —',
    className,
    ...props
}) {
    return (
        <select
            id={id}
            value={value ?? ''}
            onChange={(e) => onChange(e.target.value)}
            className={cn(
                'h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30',
                className,
            )}
            {...props}
        >
            {kosong !== null && <option value="">{kosong}</option>}
            {opsi.map((o) => (
                <option key={o.value} value={o.value}>
                    {o.label}
                </option>
            ))}
        </select>
    );
}

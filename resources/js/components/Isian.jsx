import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';

/** Satu isian formulir: label, kontrol (children), dan galat dari server. */
export default function Isian({
    id,
    label,
    error,
    wajib = false,
    bantuan,
    children,
}) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>
                {label}
                {wajib && <span className="text-red-600"> *</span>}
            </Label>
            {children}
            {bantuan && (
                <p className="text-xs text-muted-foreground">{bantuan}</p>
            )}
            <InputError message={error} />
        </div>
    );
}

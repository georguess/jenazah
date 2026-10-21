import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

/** Tautan antarhalaman bergaya tombol (tetap <Link>, bukan <a>). */
export default function TombolTautan({
    href,
    children,
    variant = 'default',
    size = 'sm',
}) {
    return (
        <Button asChild variant={variant} size={size}>
            <Link href={href}>{children}</Link>
        </Button>
    );
}

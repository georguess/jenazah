import { Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn } from '@/lib/utils';
import { useRute } from '@/hooks/use-rute';
export default function SettingsLayout({ children }) {
    const { isCurrentOrParentUrl } = useCurrentUrl();
    const rute = useRute();
    const sidebarNavItems = [
        { title: 'Profil', href: rute['profile.edit'] },
        { title: 'Keamanan', href: rute['security.edit'] },
        { title: 'Tampilan', href: rute['appearance.edit'] },
    ];
    return (
        <div className="px-4 py-6">
            <Heading
                title="Pengaturan"
                description="Kelola profil dan akun Anda"
            />

            <div className="flex flex-col lg:flex-row lg:space-x-12">
                <aside className="w-full max-w-xl lg:w-48">
                    <nav
                        className="flex flex-col space-y-1 space-x-0"
                        aria-label="Settings"
                    >
                        {sidebarNavItems.map((item) => (
                            <Button
                                key={item.href}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('w-full justify-start', {
                                    'bg-muted': isCurrentOrParentUrl(item.href),
                                })}
                            >
                                <Link href={item.href}>{item.title}</Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 lg:hidden" />

                <div className="flex-1 md:max-w-2xl">
                    <section className="max-w-xl space-y-12">
                        {children}
                    </section>
                </div>
            </div>
        </div>
    );
}

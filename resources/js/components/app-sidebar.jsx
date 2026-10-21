import { Link, usePage } from '@inertiajs/react';
import {
    ClipboardList,
    FileHeart,
    FileText,
    GitCompareArrows,
    LayoutGrid,
    Microscope,
    UserRoundSearch,
    Users,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useRute } from '@/hooks/use-rute';

// Server hanya mengirim nama ikon; komponen ikonnya dipetakan di sini.
const ikon = {
    dasbor: LayoutGrid,
    jenazah: UserRoundSearch,
    laporan: ClipboardList,
    antemortem: FileHeart,
    postmortem: Microscope,
    kecocokan: GitCompareArrows,
    dokumen: FileText,
    pengguna: Users,
};

export function AppSidebar() {
    // Daftar menu dibangun di server dari hasil Policy/Gate
    // (HandleInertiaRequests::share). React tidak memeriksa peran apa pun.
    const { menu } = usePage().props;
    const rute = useRute();
    const items = menu.map((item) => ({
        title: item.judul,
        href: item.href,
        icon: ikon[item.ikon] ?? null,
    }));

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={rute.dashboard} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={items} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}

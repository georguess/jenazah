import { Head } from '@inertiajs/react';
import AppearanceTabs from '@/components/appearance-tabs';
import Heading from '@/components/heading';
export default function Appearance() {
    return (
        <>
            <Head title="Pengaturan tampilan" />

            <h1 className="sr-only">Pengaturan tampilan</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Pengaturan tampilan"
                    description="Atur tampilan aplikasi untuk akun Anda"
                />
                <AppearanceTabs />
            </div>
        </>
    );
}
Appearance.layout = (props) => ({
    breadcrumbs: [
        { title: 'Pengaturan tampilan', href: props.rute['appearance.edit'] },
    ],
});

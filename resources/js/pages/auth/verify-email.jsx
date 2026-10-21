// Components
import { Form, Head } from '@inertiajs/react';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useRute } from '@/hooks/use-rute';
export default function VerifyEmail({ status }) {
    const rute = useRute();
    return (
        <>
            <Head title="Verifikasi email" />

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    A new verification link has been sent to the email address
                    you provided during registration.
                </div>
            )}

            <Form
                action={rute['verification.send']}
                method="post"
                className="space-y-6 text-center"
            >
                {({ processing }) => (
                    <>
                        <Button disabled={processing} variant="secondary">
                            {processing && <Spinner />}
                            Kirim ulang email verifikasi
                        </Button>

                        <TextLink
                            href={rute.logout}
                            method="post"
                            as="button"
                            className="mx-auto block text-sm"
                        >
                            Keluar
                        </TextLink>
                    </>
                )}
            </Form>
        </>
    );
}
VerifyEmail.layout = {
    title: 'Verifikasi email',
    description:
        'Verifikasi email Anda dengan membuka tautan yang baru saja kami kirim.',
};

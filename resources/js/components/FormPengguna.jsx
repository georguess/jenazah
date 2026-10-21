import Isian from '@/components/Isian';
import Pilihan from '@/components/Pilihan';
import { Input } from '@/components/ui/input';

export default function FormPengguna({
    data,
    setData,
    errors,
    opsi,
    sandiWajib,
}) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <Isian id="name" label="Nama" error={errors.name} wajib>
                <Input
                    id="name"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                />
            </Isian>
            <Isian id="email" label="Email" error={errors.email} wajib>
                <Input
                    id="email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                />
            </Isian>
            <Isian id="role" label="Peran" error={errors.role} wajib>
                <Pilihan
                    id="role"
                    value={data.role}
                    opsi={opsi.role}
                    onChange={(v) => setData('role', v)}
                />
            </Isian>
            <div />
            <Isian
                id="password"
                label="Kata sandi"
                error={errors.password}
                wajib={sandiWajib}
                bantuan={
                    sandiWajib ? null : 'Kosongkan bila tidak ingin mengganti.'
                }
            >
                <Input
                    id="password"
                    type="password"
                    autoComplete="new-password"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                />
            </Isian>
            <Isian
                id="password_confirmation"
                label="Ulangi kata sandi"
                error={errors.password_confirmation}
                wajib={sandiWajib}
            >
                <Input
                    id="password_confirmation"
                    type="password"
                    autoComplete="new-password"
                    value={data.password_confirmation}
                    onChange={(e) =>
                        setData('password_confirmation', e.target.value)
                    }
                />
            </Isian>
        </div>
    );
}

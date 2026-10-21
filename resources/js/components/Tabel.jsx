/** Kerangka tabel; baris diisi pemakai lewat children. */
export default function Tabel({ kolom, children }) {
    return (
        <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
                <thead className="border-b text-xs text-muted-foreground uppercase">
                    <tr>
                        {kolom.map((k) => (
                            <th
                                key={k}
                                scope="col"
                                className="px-3 py-2 font-medium"
                            >
                                {k}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y">{children}</tbody>
            </table>
        </div>
    );
}

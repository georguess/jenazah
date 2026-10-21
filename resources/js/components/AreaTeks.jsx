import { cn } from '@/lib/utils';

export default function AreaTeks({ className, ...props }) {
    return (
        <textarea
            rows={3}
            className={cn(
                'w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30',
                className,
            )}
            {...props}
        />
    );
}

import { usePage } from '@inertiajs/react';

import AppLogoIcon from '@/components/app-logo-icon';

export default function AppLogo() {
    const { name } = usePage().props;

    return (
        <>
            {/* Selo colorido da marca, o mesmo do favicon e do login. */}
            <div className="flex aspect-square size-8 shrink-0 items-center justify-center rounded-full bg-white ring-2 ring-emerald-600 ring-inset">
                <AppLogoIcon className="size-[18px] fill-current text-emerald-600" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold">
                    {name}
                </span>
            </div>
        </>
    );
}

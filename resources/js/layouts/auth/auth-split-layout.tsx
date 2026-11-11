import { Link, usePage } from '@inertiajs/react';
import { Building2, ShieldCheck, Truck } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

const highlights = [
    {
        icon: Building2,
        text: 'Cada conta acessa somente as unidades às quais está vinculada.',
    },
    {
        icon: Truck,
        text: 'Solicitações, ofertas e transferências acompanhadas até o recebimento.',
    },
    {
        icon: ShieldCheck,
        text: 'Ações registradas para conferência e auditoria.',
    },
];

/**
 * Casco do login: painel institucional à esquerda (desktop) e, à direita,
 * o cartão de acesso centralizado sobre um fundo que escurece para baixo.
 */
export default function AuthSplitLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    const { name } = usePage().props;

    return (
        <div className="grid min-h-dvh lg:grid-cols-2">
            <aside className="relative hidden h-dvh flex-col overflow-hidden bg-emerald-950 p-10 text-emerald-50 lg:sticky lg:top-0 lg:flex">
                <div
                    aria-hidden
                    className="pointer-events-none absolute -top-24 -right-24 size-96 rounded-full bg-emerald-500/20 blur-3xl"
                />
                <Link
                    href={home()}
                    className="relative flex items-center gap-2 text-lg font-semibold"
                >
                    <AppLogoIcon className="size-8 fill-current text-emerald-400" />
                    {name}
                </Link>

                <div className="relative mt-10 max-w-md space-y-6">
                    <p className="text-3xl leading-tight font-semibold text-balance">
                        Medicamentos chegando onde faltam, com a rede de saúde
                        conectada.
                    </p>
                    <ul className="space-y-3">
                        {highlights.map(({ icon: Icon, text }) => (
                            <li key={text} className="flex gap-3 text-sm">
                                <Icon className="mt-0.5 size-5 shrink-0 text-emerald-400" />
                                <span className="text-emerald-100/90">
                                    {text}
                                </span>
                            </li>
                        ))}
                    </ul>
                </div>

                {/*
                 * Ilustração da rede (unidade, entrega, van e unidades ligadas).
                 * PNG original em public/img/fundo_login.png; servimos o WebP
                 * (≈10x menor, mantém a transparência).
                 */}
                <div className="relative flex min-h-0 flex-1 items-center justify-center py-6">
                    <div
                        aria-hidden
                        className="pointer-events-none absolute inset-x-[8%] top-[12%] bottom-[4%] rounded-[50%] bg-emerald-300/15 blur-3xl"
                    />
                    <img
                        src="/img/fundo_login.webp"
                        alt=""
                        width={1200}
                        height={900}
                        decoding="async"
                        className="relative max-h-full w-full max-w-[640px] object-contain drop-shadow-[0_24px_40px_rgb(0_0_0/0.35)]"
                    />
                </div>

                <p className="relative text-xs text-emerald-200/60">
                    Acesso restrito a profissionais das unidades de saúde
                    participantes.
                </p>
            </aside>

            <main className="relative flex items-center justify-center overflow-hidden bg-background px-4 py-10 sm:px-8">
                <div
                    aria-hidden
                    className="pointer-events-none absolute inset-x-0 bottom-0 h-[45%] bg-gradient-to-b from-transparent via-emerald-800/50 to-emerald-950"
                />

                <div className="relative w-full max-w-[26rem] rounded-3xl bg-card px-6 py-9 shadow-[0_24px_80px_-24px_rgb(2_44_34/0.35)] ring-1 ring-black/5 sm:px-9 dark:ring-white/10">
                    <div className="flex flex-col items-center text-center">
                        <Link
                            href={home()}
                            aria-label={`Voltar para a página inicial do ${name}`}
                            className="flex size-20 items-center justify-center rounded-full bg-muted shadow-inner ring-8 ring-muted/50"
                        >
                            <span className="flex size-14 items-center justify-center rounded-full bg-background shadow-md ring-2 ring-emerald-600/80">
                                <AppLogoIcon className="size-8 fill-current text-emerald-600 dark:text-emerald-400" />
                            </span>
                        </Link>

                        <h1 className="mt-5 text-2xl font-semibold tracking-tight">
                            {title}
                        </h1>
                        <p className="mt-1.5 text-sm text-balance text-muted-foreground">
                            {description}
                        </p>
                    </div>

                    <div className="mt-7">{children}</div>

                    <p className="mt-7 flex items-center justify-center gap-1.5 text-xs text-muted-foreground">
                        <ShieldCheck className="size-3.5" />
                        Ambiente seguro do {name}
                    </p>
                </div>
            </main>
        </div>
    );
}

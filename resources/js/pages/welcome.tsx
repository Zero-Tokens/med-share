import { Head, Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import RoadScene from '@/components/landing/road-scene';
import {
    RequestFormIllustration,
    ReservationIllustration,
    SearchRadiusIllustration,
    TrackingChecklistIllustration,
} from '@/components/landing/step-illustrations';
import { dashboard, login } from '@/routes';
import '../../css/landing.css';

/*
 * Landing page pública do med-share, portada do modelo de design
 * "med-share · Landing page". Página só no tema claro: as cores são as do
 * modelo, não os tokens do painel. Animações em resources/css/landing.css.
 */

const features = [
    {
        title: 'Consulta de medicamentos',
        description:
            'Pesquise a disponibilidade por medicamento e região, com a origem e a data de atualização de cada informação de estoque.',
        icon: (
            <>
                <circle cx="11" cy="11" r="7" />
                <path d="M20 20l-3.5-3.5" />
            </>
        ),
    },
    {
        title: 'Publicação de ofertas',
        description:
            'A unidade com excedente confirma o que pode transferir. O estoque público serve de referência, mas só entra na oferta o que o responsável liberar.',
        icon: (
            <>
                <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z" />
                <path d="M4 7.5l8 4.5 8-4.5" />
                <path d="M12 12v9" />
            </>
        ),
    },
    {
        title: 'Solicitações',
        description:
            'Registre a falta de um medicamento e a quantidade necessária. A plataforma busca origens compatíveis, começando pelas mais próximas.',
        icon: (
            <>
                <rect x="5" y="4" width="14" height="17" rx="2" />
                <path d="M9 4V3h6v1" />
                <path d="M9 10h6M9 14h6M9 18h3" />
            </>
        ),
    },
    {
        title: 'Acompanhamento das transferências',
        description:
            'Aprovação, reserva, coleta e recebimento ficam registrados por lote, com o registro de divergências na entrega.',
        icon: (
            <>
                <path d="M2 6h11v10H2z" />
                <path d="M13 9h4l4 4v3h-8z" />
                <circle cx="6.5" cy="17.5" r="2" />
                <circle cx="17" cy="17.5" r="2" />
            </>
        ),
    },
];

const roles = [
    {
        title: 'Gestor municipal',
        description:
            'Vincula as unidades e aprova os responsáveis do município.',
    },
    {
        title: 'Responsável da unidade',
        description:
            'Declara faltas, libera ofertas e confirma quantidades e lotes.',
    },
    {
        title: 'Aprovador de transferência',
        description:
            'Autoriza a operação conforme as regras dos participantes.',
    },
    {
        title: 'Operador de logística',
        description:
            'Registra a coleta, o transporte e as evidências de entrega.',
    },
    {
        title: 'Auditor ou consulta',
        description:
            'Consulta os registros autorizados sem alterar a operação.',
    },
    {
        title: 'Administração da plataforma',
        description:
            'Gerencia as adesões e trata as inconsistências nos dados.',
    },
];

const principles = [
    {
        title: 'Acesso por vínculo',
        description:
            'Cada pessoa tem conta individual e atua somente nas unidades às quais está vinculada, com a permissão definida para ela.',
        icon: (
            <>
                <rect x="4" y="3" width="11" height="18" rx="1.5" />
                <path d="M15 9h5v12h-5" />
                <path d="M8 7h3M8 11h3M8 15h3" />
            </>
        ),
    },
    {
        title: 'Dados com origem',
        description:
            'Cada informação mostra de onde veio e quando foi atualizada. Estoque público é referência, não garantia de disponibilidade.',
        icon: (
            <>
                <ellipse cx="12" cy="6" rx="7" ry="3" />
                <path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6" />
                <path d="M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6" />
            </>
        ),
    },
    {
        title: 'Rastreabilidade',
        description:
            'Reservas, saídas e recebimentos ficam registrados para conferência entre as unidades.',
        icon: (
            <>
                <path d="M12 3l7 3v6c0 4.5-3 7.6-7 9-4-1.4-7-4.5-7-9V6z" />
                <path d="M9 12l2.2 2.2L15 10.5" />
            </>
        ),
    },
];

const notDone = [
    'Não substitui os sistemas locais de dispensação das unidades.',
    'Não registra dados de pacientes.',
    'Não troca um medicamento por outro automaticamente: a compatibilidade considera composição, concentração e apresentação.',
];

const muted = 'text-[#55615b]';
const container = 'mx-auto max-w-[1120px] px-6';
const sectionTitle =
    'm-0 text-[clamp(28px,3vw,40px)] leading-[1.1] font-bold tracking-[-0.03em]';

export default function Welcome() {
    const { auth, name } = usePage().props;
    const isAuthenticated = Boolean(auth.user);
    const accessHref = isAuthenticated ? dashboard() : login();

    return (
        <>
            <Head title="Redistribuição de medicamentos entre unidades de saúde" />

            <div className="lp overflow-x-hidden bg-white font-sans text-[#0d1512]">
                <header className="sticky top-0 z-20 border-b border-[#e6ebe8] bg-white/88 backdrop-blur-[10px]">
                    <div
                        className={`${container} flex flex-wrap items-center justify-between gap-4 py-3`}
                    >
                        <a
                            href="#topo"
                            className="flex min-h-11 items-center gap-2.5 text-[17px] font-semibold text-[#0d1512] no-underline"
                        >
                            <BrandMark size={26} />
                            <span>{name}</span>
                        </a>
                        <nav className="flex items-center gap-2">
                            <a
                                href="#como-funciona"
                                className="lp-navlink hidden px-3.5 py-3 text-[15px] text-[#55615b] no-underline sm:inline"
                            >
                                Como funciona
                            </a>
                            <Link
                                href={accessHref}
                                className="lp-btn-dark inline-flex min-h-11 items-center gap-2 rounded-[10px] bg-[#0d1512] px-4 text-[15px] font-semibold text-white no-underline"
                            >
                                <LineIcon size={16}>
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                    <path d="M10 17l5-5-5-5" />
                                    <path d="M15 12H3" />
                                </LineIcon>
                                <span>
                                    {isAuthenticated ? 'Painel' : 'Entrar'}
                                </span>
                            </Link>
                        </nav>
                    </div>
                </header>

                <main>
                    {/* HERÓI */}
                    <section
                        id="topo"
                        className="relative border-b border-[#e6ebe8] bg-white bg-[radial-gradient(#d5ebe1_1.1px,transparent_1.1px)] bg-[size:26px_26px]"
                    >
                        <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,rgb(255_255_255/0)_0%,#ffffff_46%)]" />
                        <div
                            className={`${container} relative flex flex-col gap-14 pt-20 pb-[88px]`}
                        >
                            <div className="flex flex-col items-start gap-6">
                                <div className="lp-in1 inline-flex items-center gap-2.5 rounded-full border border-[#cfe5da] bg-white py-[7px] pr-3.5 pl-3 text-[13px] font-medium text-[#3d4a44]">
                                    <span className="lp-pulse block size-2 rounded-full bg-[#059669]" />
                                    <span>
                                        Rede de redistribuição entre unidades de
                                        saúde
                                    </span>
                                </div>
                                <h1 className="lp-in2 m-0 max-w-[920px] text-[clamp(38px,5.2vw,72px)] leading-[1.03] font-bold tracking-[-0.035em] text-balance">
                                    O medicamento que falta em uma unidade pode
                                    estar{' '}
                                    <span className="text-[#047857]">
                                        sobrando na unidade vizinha.
                                    </span>
                                </h1>
                                <p
                                    className={`lp-in3 m-0 max-w-[660px] text-[19px] leading-[1.55] text-pretty ${muted}`}
                                >
                                    O {name} reúne os dados de estoque, recebe
                                    os anúncios de falta e encontra unidades que
                                    possam transferir as quantidades
                                    necessárias, com acompanhamento até o
                                    recebimento.
                                </p>
                                <div className="lp-in4 flex flex-wrap gap-3">
                                    <PrimaryLink href={accessHref}>
                                        {isAuthenticated
                                            ? 'Ir para o painel'
                                            : 'Acessar o sistema'}
                                    </PrimaryLink>
                                    <a
                                        href="#como-funciona"
                                        className="lp-btn-light inline-flex min-h-[50px] items-center rounded-xl border border-[#d5dcd8] bg-white px-[22px] text-base font-semibold text-[#0d1512] no-underline"
                                    >
                                        Ver como funciona
                                    </a>
                                </div>
                            </div>

                            <RoadScene />
                        </div>
                    </section>

                    {/* O QUE A PLATAFORMA FAZ */}
                    <section className="border-b border-[#e6ebe8] bg-[#f7faf8]">
                        <div
                            className={`${container} flex flex-col gap-11 py-24`}
                        >
                            <SectionHeading
                                title="O que a plataforma faz"
                                description="Da consulta ao recebimento, cada etapa da transferência acontece no mesmo lugar."
                            />
                            <div className="grid grid-cols-[repeat(auto-fit,minmax(min(250px,100%),1fr))] gap-4">
                                {features.map(
                                    ({ title, description, icon }) => (
                                        <article
                                            key={title}
                                            className="lp-card lp-rv flex flex-col gap-3.5 rounded-[18px] border border-[#e1e8e4] bg-white p-[26px]"
                                        >
                                            <div className="lp-ico flex size-11 items-center justify-center rounded-xl bg-[#e3f5ec] text-[#047857]">
                                                <LineIcon size={22}>
                                                    {icon}
                                                </LineIcon>
                                            </div>
                                            <h3 className="m-0 text-lg font-semibold tracking-[-0.01em]">
                                                {title}
                                            </h3>
                                            <p
                                                className={`m-0 text-[15px] leading-[1.55] ${muted}`}
                                            >
                                                {description}
                                            </p>
                                        </article>
                                    ),
                                )}
                            </div>
                        </div>
                    </section>

                    {/* COMO FUNCIONA */}
                    <section
                        id="como-funciona"
                        className="scroll-mt-[70px] border-b border-[#e6ebe8] bg-white"
                    >
                        <div
                            className={`${container} flex flex-col gap-[72px] py-24`}
                        >
                            <SectionHeading
                                title="Como funciona"
                                description="Quatro etapas levam o medicamento de onde sobra para onde falta, sempre com a confirmação de quem responde por cada unidade."
                                narrow
                            />

                            <Step
                                number={1}
                                kicker="Solicitação"
                                title="A unidade informa a falta"
                                illustration={<RequestFormIllustration />}
                            >
                                O responsável pela unidade abre uma solicitação
                                com o medicamento, a apresentação, a quantidade
                                necessária, a urgência e o prazo. Uma
                                solicitação pode ter vários itens e ser atendida
                                em partes, por mais de uma origem.
                            </Step>

                            <Step
                                number={2}
                                kicker="Busca"
                                title="A busca encontra origens"
                                illustration={<SearchRadiusIllustration />}
                                reverse
                                extra={
                                    <ol className="flex flex-col gap-2.5 pt-1">
                                        {[
                                            ['lp-rg1', '1. Mesmo município'],
                                            [
                                                'lp-rg2',
                                                '2. Municípios próximos',
                                            ],
                                            ['lp-rg3', '3. Demais regiões'],
                                        ].map(([cls, label]) => (
                                            <li
                                                key={label}
                                                className="flex items-center gap-3 text-base font-medium"
                                            >
                                                <span
                                                    className={`${cls} block size-3 rounded-full bg-[#047857]`}
                                                />
                                                <span>{label}</span>
                                            </li>
                                        ))}
                                    </ol>
                                }
                            >
                                A plataforma procura unidades com oferta
                                confirmada, vigente e compatível, ampliando a
                                área aos poucos. Cada sugestão mostra a
                                quantidade ofertável, a distância estimada e a
                                data da informação.
                            </Step>

                            <Step
                                number={3}
                                kicker="Reserva"
                                title="A origem confirma e reserva"
                                illustration={<ReservationIllustration />}
                            >
                                A unidade de origem confirma a oferta e os
                                lotes, e os responsáveis aprovam a
                                transferência. A quantidade fica reservada e
                                deixa de ser oferecida a outra unidade. O
                                estoque público é só uma referência: vale o que
                                a unidade liberou.
                            </Step>

                            <Step
                                number={4}
                                kicker="Transferência"
                                title="A transferência é acompanhada"
                                illustration={<TrackingChecklistIllustration />}
                                reverse
                            >
                                Da coleta ao recebimento, cada etapa fica
                                registrada por lote. O destino confere o que
                                chegou e, se houver diferença de lote ou
                                quantidade, a divergência é registrada com
                                evidências e tratada antes de concluir.
                            </Step>
                        </div>
                    </section>

                    {/* QUEM PARTICIPA */}
                    <section className="border-b border-[#e6ebe8] bg-[#f7faf8]">
                        <div
                            className={`${container} flex flex-col gap-11 py-24`}
                        >
                            <SectionHeading
                                title="Quem participa"
                                description="Cada pessoa tem uma conta individual e um papel definido. Criar a conta não libera a operação: o vínculo com o município ou a unidade precisa ser verificado."
                                narrow
                            />
                            <div className="grid grid-cols-[repeat(auto-fit,minmax(min(320px,100%),1fr))] gap-4">
                                {roles.map(({ title, description }) => (
                                    <article
                                        key={title}
                                        className="lp-card lp-rv flex flex-col gap-2 rounded-2xl border border-[#e1e8e4] bg-white p-[22px]"
                                    >
                                        <h3 className="m-0 text-[17px] font-semibold">
                                            {title}
                                        </h3>
                                        <p
                                            className={`m-0 text-[15px] leading-normal ${muted}`}
                                        >
                                            {description}
                                        </p>
                                    </article>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* SEGURANÇA */}
                    <section className="border-b border-[#e6ebe8] bg-white">
                        <div
                            className={`${container} flex flex-col gap-11 py-24`}
                        >
                            <h2 className={`lp-rv ${sectionTitle}`}>
                                Segurança e confiança nos dados
                            </h2>
                            <div className="grid grid-cols-[repeat(auto-fit,minmax(min(300px,100%),1fr))] gap-8">
                                {principles.map(
                                    ({ title, description, icon }) => (
                                        <div
                                            key={title}
                                            className="lp-rv flex gap-3.5"
                                        >
                                            <div className="flex size-10 flex-none items-center justify-center rounded-[10px] bg-[#e3f5ec] text-[#047857]">
                                                <LineIcon size={20}>
                                                    {icon}
                                                </LineIcon>
                                            </div>
                                            <div className="flex flex-col gap-1.5">
                                                <h3 className="m-0 text-lg font-semibold">
                                                    {title}
                                                </h3>
                                                <p
                                                    className={`m-0 text-[15px] leading-[1.55] ${muted}`}
                                                >
                                                    {description}
                                                </p>
                                            </div>
                                        </div>
                                    ),
                                )}
                            </div>

                            <div className="lp-rv flex flex-wrap items-start gap-x-12 gap-y-5 rounded-[20px] border border-[#e1e8e4] bg-[#f7faf8] p-7">
                                <h3 className="m-0 flex-[1_1_220px] text-xl leading-tight font-bold tracking-[-0.015em]">
                                    O que o {name} não faz
                                </h3>
                                <ul className="flex min-w-0 flex-[3_1_420px] flex-col gap-3 text-base leading-normal text-[#3d4a44]">
                                    {notDone.map((item) => (
                                        <li key={item} className="flex gap-3">
                                            <span className="mt-[11px] block h-0.5 w-3.5 flex-none bg-[#0d1512]" />
                                            <span>{item}</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    </section>

                    {/* CHAMADA FINAL */}
                    <section
                        id="acessar"
                        className="relative scroll-mt-[70px] overflow-hidden bg-[#064e3b] text-white"
                    >
                        <div
                            className={`${container} flex flex-wrap items-center justify-between gap-x-12 gap-y-7 pt-[88px] pb-[104px]`}
                        >
                            <div className="lp-rv flex min-w-0 flex-[1_1_420px] flex-col gap-3">
                                <h2 className="m-0 text-[clamp(28px,3.2vw,44px)] leading-[1.08] font-bold tracking-[-0.03em]">
                                    Sua unidade já participa da rede?
                                </h2>
                                <p className="m-0 text-lg leading-normal text-[#d1ede2]">
                                    Entre com a conta vinculada à sua unidade de
                                    saúde.
                                </p>
                            </div>
                            <Link
                                href={accessHref}
                                className="lp-btn-dark lp-rv inline-flex min-h-[54px] items-center gap-2.5 rounded-xl bg-white px-[26px] text-[17px] font-semibold text-[#0d1512] no-underline"
                            >
                                <span>
                                    {isAuthenticated
                                        ? 'Ir para o painel'
                                        : 'Acessar o sistema'}
                                </span>
                                <ArrowIcon />
                            </Link>
                        </div>
                        <div className="lp-dash-move absolute inset-x-0 bottom-9 h-1 bg-[repeating-linear-gradient(90deg,#34c391_0_28px,transparent_28px_56px)] opacity-70" />
                    </section>
                </main>

                <footer className="bg-white">
                    <div
                        className={`${container} flex flex-wrap items-center justify-between gap-3 py-7 text-sm ${muted}`}
                    >
                        <div className="flex items-center gap-2.5 font-semibold text-[#0d1512]">
                            <BrandMark size={20} withCore={false} />
                            <span>{name}</span>
                        </div>
                        <span>
                            Rede de redistribuição de medicamentos entre
                            unidades de saúde
                        </span>
                    </div>
                </footer>
            </div>
        </>
    );
}

function SectionHeading({
    title,
    description,
    narrow = false,
}: {
    title: string;
    description: string;
    narrow?: boolean;
}) {
    return (
        <div
            className={`lp-rv flex flex-col gap-3 ${narrow ? 'max-w-[700px]' : ''}`}
        >
            <h2 className={sectionTitle}>{title}</h2>
            <p className={`m-0 text-lg leading-normal ${muted}`}>
                {description}
            </p>
        </div>
    );
}

function Step({
    number,
    kicker,
    title,
    children,
    illustration,
    extra,
    reverse = false,
}: {
    number: number;
    kicker: string;
    title: string;
    children: ReactNode;
    illustration: ReactNode;
    extra?: ReactNode;
    reverse?: boolean;
}) {
    return (
        <div
            className={`lp-rv flex flex-wrap items-center gap-x-16 gap-y-10 ${reverse ? 'flex-row-reverse' : ''}`}
        >
            <div className="flex min-w-0 flex-[1_1_380px] flex-col gap-4">
                <div className="flex items-center gap-3">
                    <span className="flex size-10 items-center justify-center rounded-full bg-[#047857] text-[17px] font-bold text-white">
                        {number}
                    </span>
                    <span className="text-[13px] font-semibold tracking-[0.08em] text-[#047857] uppercase">
                        {kicker}
                    </span>
                </div>
                <h3 className="m-0 text-[clamp(24px,2.4vw,32px)] leading-[1.15] font-bold tracking-[-0.025em]">
                    {title}
                </h3>
                <p
                    className={`m-0 text-[17px] leading-[1.6] text-pretty ${muted}`}
                >
                    {children}
                </p>
                {extra}
            </div>
            {illustration}
        </div>
    );
}

function PrimaryLink({
    href,
    children,
}: {
    href: ReturnType<typeof login>;
    children: ReactNode;
}) {
    return (
        <Link
            href={href}
            className="lp-btn-dark inline-flex min-h-[50px] items-center gap-2.5 rounded-xl bg-[#0d1512] px-[22px] text-base font-semibold text-white no-underline"
        >
            <span>{children}</span>
            <ArrowIcon />
        </Link>
    );
}

function ArrowIcon() {
    return (
        <svg
            className="lp-arr"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            <path d="M5 12h14" />
            <path d="M13 6l6 6-6 6" />
        </svg>
    );
}

function LineIcon({ size, children }: { size: number; children: ReactNode }) {
    return (
        <svg
            width={size}
            height={size}
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            {children}
        </svg>
    );
}

/** Marca da landing: cruz verde com o centro mais claro, como no modelo. */
function BrandMark({
    size,
    withCore = true,
}: {
    size: number;
    withCore?: boolean;
}) {
    return (
        <svg width={size} height={size} viewBox="0 0 26 26" aria-hidden="true">
            <rect x="9" y="1" width="8" height="24" rx="2" fill="#059669" />
            <rect x="1" y="9" width="24" height="8" rx="2" fill="#059669" />
            {withCore && (
                <rect x="9" y="9" width="8" height="8" fill="#34c391" />
            )}
        </svg>
    );
}

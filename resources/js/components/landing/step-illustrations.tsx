import { CheckIcon } from '@/components/landing/road-scene';

/*
 * Ilustrações animadas das quatro etapas do "Como funciona".
 * São decorativas (aria-hidden): o texto ao lado de cada uma já diz tudo.
 * Quadros-chave em resources/css/landing.css.
 */

function Stage({
    children,
    className = '',
}: {
    children: React.ReactNode;
    className?: string;
}) {
    return (
        <div
            className={`box-border flex min-h-[300px] min-w-0 flex-[1_1_380px] items-center justify-center rounded-[22px] border border-[#e1e8e4] bg-[#f7faf8] p-8 ${className}`}
        >
            {children}
        </div>
    );
}

function FakeField({
    label,
    value,
    fillClass,
}: {
    label: string;
    value: string;
    fillClass: string;
}) {
    return (
        <div className="flex flex-col gap-1.5">
            <span className="text-xs text-[#5b6660]">{label}</span>
            <div className="flex h-10 items-center rounded-[10px] border border-[#d5dcd8] px-3 text-sm">
                <span className={`${fillClass} whitespace-nowrap`}>
                    {value}
                </span>
            </div>
        </div>
    );
}

/** Passo 1: o formulário de solicitação se preenche e vira "Solicitação aberta". */
export function RequestFormIllustration() {
    return (
        <Stage>
            <div
                aria-hidden="true"
                className="flex w-full max-w-[340px] flex-col gap-3.5 rounded-2xl border border-[#e1e8e4] bg-white p-5 shadow-[0_12px_30px_rgb(13_21_18/0.06)]"
            >
                <div className="flex items-center justify-between gap-2">
                    <span className="text-sm font-semibold">
                        Nova solicitação
                    </span>
                    <span className="text-[11px] font-semibold tracking-[0.04em] text-[#5b6660] uppercase">
                        Exemplo
                    </span>
                </div>
                <FakeField
                    label="Medicamento"
                    value="Paracetamol 500 mg, comprimido"
                    fillClass="lp-ff1"
                />
                <div className="grid grid-cols-2 gap-2.5">
                    <FakeField
                        label="Quantidade"
                        value="80"
                        fillClass="lp-ff2"
                    />
                    <FakeField
                        label="Urgência"
                        value="Alta"
                        fillClass="lp-ff3"
                    />
                </div>
                <div className="lp-badge-in flex items-center gap-2 rounded-[10px] bg-[#e3f5ec] px-3 py-2.5 text-[13px] font-semibold text-[#065f46]">
                    <CheckIcon />
                    <span>Solicitação aberta</span>
                </div>
            </div>
        </Stage>
    );
}

function Dot({ className }: { className: string }) {
    return (
        <span
            className={`absolute block size-3.5 rounded-full border-[3px] border-white bg-[#059669] ${className}`}
        />
    );
}

/** Passo 2: os anéis da busca acendem do mais próximo ao mais distante. */
export function SearchRadiusIllustration() {
    return (
        <Stage className="min-h-[340px] p-6">
            <div
                aria-hidden="true"
                className="relative size-[290px] max-w-full"
            >
                <div className="lp-rg3 absolute inset-0 rounded-full border-2 border-dashed border-[#059669] bg-[#edf8f3]">
                    <Dot className="top-[12%] left-[14%]" />
                    <Dot className="right-[22%] bottom-[9%]" />
                </div>
                <div className="lp-rg2 absolute inset-12 rounded-full border-2 border-dashed border-[#059669] bg-[#dff3ea]">
                    <Dot className="top-[14%] right-[10%]" />
                    <Dot className="bottom-[12%] left-[12%]" />
                </div>
                <div className="lp-rg1 absolute inset-24 rounded-full border-2 border-[#059669] bg-[#cdecdf]">
                    <Dot className="top-[4%] left-[8%]" />
                </div>
                <span className="lp-ping absolute top-1/2 left-1/2 box-border block size-[290px] max-w-full rounded-full border-2 border-[#047857]" />
                <span className="absolute top-1/2 left-1/2 -mt-[11px] -ml-[11px] box-border block size-[22px] rounded-full border-4 border-white bg-[#0d1512]" />
            </div>
        </Stage>
    );
}

/** Passo 3: do que a unidade liberou, parte fica reservada. */
export function ReservationIllustration() {
    return (
        <Stage>
            <div
                aria-hidden="true"
                className="flex w-full max-w-[380px] flex-col gap-[18px]"
            >
                <div className="flex items-center justify-between gap-2">
                    <span className="text-sm font-semibold">
                        Oferta da unidade de origem
                    </span>
                    <span className="text-[11px] font-semibold tracking-[0.04em] text-[#5b6660] uppercase">
                        Exemplo hipotético
                    </span>
                </div>
                <div className="flex items-center justify-between gap-3 rounded-xl border-[1.5px] border-dashed border-[#b6c2bc] px-3.5 py-3 text-sm text-[#55615b]">
                    <span>Estoque público (referência)</span>
                    <span className="font-semibold text-[#0d1512]">1.000</span>
                </div>
                <div className="flex flex-col gap-2">
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-[#55615b]">
                            Liberado pela unidade
                        </span>
                        <span className="font-semibold">200</span>
                    </div>
                    <div className="relative h-[46px] overflow-hidden rounded-xl bg-[#cdecdf]">
                        <div className="absolute inset-y-0 right-3.5 flex items-center text-[13px] font-semibold text-[#065f46]">
                            Saldo ofertável 120
                        </div>
                        <div className="lp-reserve relative flex h-full w-[40%] items-center overflow-hidden bg-[#047857]">
                            <span className="pl-3.5 text-[13px] font-semibold whitespace-nowrap text-white">
                                Reserva 80
                            </span>
                        </div>
                    </div>
                </div>
                <span className="text-[13px] leading-normal text-[#5b6660]">
                    A unidade libera 200, o sistema reserva 80 e restam 120 para
                    novas reservas.
                </span>
            </div>
        </Stage>
    );
}

const checklistItems = [
    'Aprovação dos responsáveis',
    'Reserva da quantidade',
    'Coleta com conferência',
    'Transporte',
    'Recebimento conferido',
];

/** Passo 4: o checklist da transferência se completa item a item. */
export function TrackingChecklistIllustration() {
    return (
        <Stage>
            <div className="relative h-60 w-full max-w-[320px]">
                <ul className="absolute inset-0 flex flex-col">
                    {checklistItems.map((item) => (
                        <li
                            key={item}
                            className="flex h-12 items-center gap-3.5 text-base font-medium text-[#55615b]"
                        >
                            <span className="box-border block size-[26px] rounded-full border-2 border-[#b6c2bc]" />
                            <span>{item}</span>
                        </li>
                    ))}
                </ul>
                <ul
                    aria-hidden="true"
                    className="lp-checklist absolute inset-0 flex flex-col bg-[#f7faf8]"
                >
                    {checklistItems.map((item) => (
                        <li
                            key={item}
                            className="flex h-12 items-center gap-3.5 text-base font-semibold text-[#0d1512]"
                        >
                            <span className="flex size-[26px] items-center justify-center rounded-full bg-[#047857] text-white">
                                <CheckIcon size={14} strokeWidth={3.2} />
                            </span>
                            <span>{item}</span>
                        </li>
                    ))}
                </ul>
            </div>
        </Stage>
    );
}

/**
 * Cena do herói: o veículo carrega na unidade com excedente, percorre a
 * estrada e descarrega na unidade em falta, que passa a "Recebido". A trilha
 * abaixo (Reserva → Coleta → Transporte → Recebimento) acompanha a viagem.
 * Os quadros-chave ficam em resources/css/landing.css (ciclo de 16s).
 */
export default function RoadScene() {
    return (
        <div className="lp-in5 flex flex-col gap-5">
            <div
                role="img"
                aria-label="Animação: um veículo coleta medicamentos em uma unidade com excedente e os entrega em uma unidade com falta."
                className="relative h-[340px] overflow-hidden rounded-3xl border border-[#d3eadf] bg-[#eef8f3]"
            >
                <div className="lp-cloud absolute top-11 left-[30%] h-[30px] w-[120px] rounded-full bg-white" />
                <div className="lp-cloud lp-cloud--slow absolute top-24 left-[58%] h-[22px] w-[84px] rounded-full bg-white" />

                {/* colinas */}
                <div className="absolute right-[40%] bottom-16 -left-[10%] h-[110px] rounded-t-[50%] bg-[#dcf1e7]" />
                <div className="absolute -right-[15%] bottom-16 left-[35%] h-[84px] rounded-t-[50%] bg-[#e3f4ec]" />

                {/* rótulos */}
                <div className="absolute top-[22px] left-[4%] flex items-center gap-2 rounded-full border border-[#b9e2cf] bg-white px-3 py-[7px] text-[13px] font-semibold text-[#047857]">
                    <span className="block size-2 rounded-full bg-[#059669]" />
                    <span>Tem excedente</span>
                </div>
                <div className="lp-pill-falta absolute top-[22px] right-[4%] flex items-center gap-2 rounded-full border border-[#f0cf9c] bg-white px-3 py-[7px] text-[13px] font-semibold text-[#92400e]">
                    <span className="block size-2 rounded-[2px] bg-[#b45309]" />
                    <span>Em falta</span>
                </div>
                <div className="lp-pill-ok absolute top-[22px] right-[4%] flex items-center gap-2 rounded-full border border-[#047857] bg-[#047857] px-3 py-[7px] text-[13px] font-semibold text-white">
                    <CheckIcon size={14} strokeWidth={3} />
                    <span>Recebido</span>
                </div>

                <HealthUnit className="left-[4%]" />
                <HealthUnit className="right-[4%]" />

                {/* calçada e estrada */}
                <div className="absolute inset-x-0 bottom-16 h-1.5 bg-[#bfe0d0]" />
                <div className="absolute inset-x-0 bottom-0 h-16 bg-[#1f2925]" />
                <div className="absolute inset-x-0 bottom-[30px] h-1 bg-[repeating-linear-gradient(90deg,#ffffff_0_28px,transparent_28px_56px)] opacity-85" />

                {/* caixas sendo carregadas e descarregadas */}
                <div className="lp-box-a absolute bottom-[74px] left-[11%] size-4 rounded-[3px] border-2 border-[#0d1512] bg-[#a7e3c8]" />
                <div className="lp-box-b absolute bottom-[74px] left-[81%] size-4 rounded-[3px] border-2 border-[#0d1512] bg-[#a7e3c8]" />

                <div className="lp-van absolute bottom-5 left-1/2">
                    <div className="lp-bob">
                        <Van />
                    </div>
                </div>
            </div>

            <TransferTrack />
        </div>
    );
}

function HealthUnit({ className }: { className: string }) {
    return (
        <svg
            viewBox="0 0 160 130"
            aria-hidden="true"
            className={`absolute bottom-[68px] block w-[clamp(92px,13vw,170px)] ${className}`}
        >
            <rect
                x="10"
                y="40"
                width="140"
                height="90"
                rx="4"
                fill="#ffffff"
                stroke="#0d1512"
                strokeWidth="2.5"
            />
            <rect x="4" y="32" width="152" height="12" rx="3" fill="#0d1512" />
            <rect x="62" y="4" width="36" height="30" rx="6" fill="#059669" />
            <rect x="77" y="10" width="6" height="18" rx="1" fill="#ffffff" />
            <rect x="71" y="16" width="18" height="6" rx="1" fill="#ffffff" />
            <rect
                x="24"
                y="60"
                width="28"
                height="22"
                rx="2"
                fill="#d6f1e5"
                stroke="#0d1512"
                strokeWidth="2"
            />
            <rect
                x="108"
                y="60"
                width="28"
                height="22"
                rx="2"
                fill="#d6f1e5"
                stroke="#0d1512"
                strokeWidth="2"
            />
            <rect
                x="68"
                y="86"
                width="24"
                height="44"
                rx="2"
                fill="#eef8f3"
                stroke="#0d1512"
                strokeWidth="2"
            />
        </svg>
    );
}

function Van() {
    return (
        <svg viewBox="-30 0 150 66" aria-hidden="true" className="block w-full">
            <g
                className="lp-speed"
                stroke="#047857"
                strokeWidth="3"
                strokeLinecap="round"
            >
                <path d="M-26 20h18" />
                <path d="M-20 32h14" />
                <path d="M-28 44h20" />
            </g>
            <rect
                x="2"
                y="8"
                width="74"
                height="42"
                rx="6"
                fill="#ffffff"
                stroke="#0d1512"
                strokeWidth="2.5"
            />
            <path
                d="M76 20h18l16 15v15H76z"
                fill="#d6f1e5"
                stroke="#0d1512"
                strokeWidth="2.5"
                strokeLinejoin="round"
            />
            <path
                d="M82 25h10l9 9H82z"
                fill="#ffffff"
                stroke="#0d1512"
                strokeWidth="2"
                strokeLinejoin="round"
            />
            <rect x="35" y="17" width="8" height="24" rx="1.5" fill="#059669" />
            <rect x="27" y="25" width="24" height="8" rx="1.5" fill="#059669" />
            <rect x="104" y="40" width="6" height="5" rx="1" fill="#f5b942" />
            <Wheel cx={24} />
            <Wheel cx={90} />
        </svg>
    );
}

function Wheel({ cx }: { cx: number }) {
    return (
        <g className="lp-wh">
            <circle cx={cx} cy="52" r="10" fill="#0d1512" />
            <circle cx={cx} cy="52" r="4" fill="#ffffff" />
            <path
                d={`M${cx} 43v18M${cx - 9} 52h18`}
                stroke="#ffffff"
                strokeWidth="1.6"
            />
        </g>
    );
}

const trackSteps = [
    { label: 'Reserva', className: 'lp-st1' },
    { label: 'Coleta', className: 'lp-st2' },
    { label: 'Transporte', className: 'lp-st3' },
    { label: 'Recebimento', className: 'lp-st4' },
];

function TransferTrack() {
    return (
        <div className="relative pb-1">
            <div className="absolute top-[13px] right-[12.5%] left-[12.5%] h-[3px] rounded-[3px] bg-[#dfe7e3]">
                <div className="lp-track-fill h-full w-full rounded-[3px] bg-[#047857]" />
            </div>
            <ol className="relative grid grid-cols-4 gap-2">
                {trackSteps.map(({ label, className }) => (
                    <li
                        key={label}
                        className={`${className} flex flex-col items-center gap-2.5 text-center text-[#047857]`}
                    >
                        <span className="block size-7 rounded-full border-4 border-white bg-current" />
                        <span className="text-sm font-semibold">{label}</span>
                    </li>
                ))}
            </ol>
        </div>
    );
}

export function CheckIcon({
    size = 16,
    strokeWidth = 2.6,
}: {
    size?: number;
    strokeWidth?: number;
}) {
    return (
        <svg
            width={size}
            height={size}
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth={strokeWidth}
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            <path d="M5 12.5l4.5 4.5L19 7.5" />
        </svg>
    );
}

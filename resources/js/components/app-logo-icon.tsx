import type { SVGAttributes } from 'react';

/**
 * Marca do med-share: cruz de saúde com um ponto central vazado,
 * representando a unidade que conecta a rede.
 */
export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
            <path
                fillRule="evenodd"
                clipRule="evenodd"
                d="M16 2h8a2 2 0 0 1 2 2v10h10a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H26v10a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2V26H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h10V4a2 2 0 0 1 2-2Zm4 13.5a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9Z"
            />
        </svg>
    );
}

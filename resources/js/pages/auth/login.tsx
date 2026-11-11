import { Form, Head } from '@inertiajs/react';
import { Lock, Mail } from 'lucide-react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

type Props = {
    status?: string;
    canResetPassword: boolean;
};

const fieldLabel =
    'text-xs font-semibold tracking-wider text-muted-foreground uppercase';

const fieldInput =
    'h-12 rounded-xl border-transparent bg-muted/70 pl-11 text-base shadow-none ring-1 ring-border transition-colors placeholder:text-muted-foreground/70 focus-visible:bg-background focus-visible:ring-2 focus-visible:ring-emerald-600/60 aria-invalid:ring-destructive/60 md:text-sm';

const fieldIcon =
    'pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-muted-foreground';

export default function Login({ status, canResetPassword }: Props) {
    const [showPassword, setShowPassword] = useState(false);

    return (
        <>
            <Head title="Entrar" />

            {status && (
                <div
                    role="status"
                    className="mb-5 rounded-xl bg-emerald-50 px-3 py-2 text-center text-sm font-medium text-emerald-800 ring-1 ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:ring-emerald-900"
                >
                    {status}
                </div>
            )}

            {/*
             * noValidate: as mensagens de campo obrigatório e de credenciais
             * vêm do servidor, em português, em vez dos balões do navegador.
             */}
            <Form
                {...store.form()}
                noValidate
                resetOnSuccess={['password']}
                className="grid gap-5"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-2">
                            <Label htmlFor="email" className={fieldLabel}>
                                E-mail
                            </Label>
                            <div className="relative">
                                <Mail className={fieldIcon} />
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    autoComplete="email"
                                    inputMode="email"
                                    placeholder="nome@unidade.gov.br"
                                    className={fieldInput}
                                    aria-invalid={!!errors.email}
                                    aria-describedby={
                                        errors.email ? 'email-error' : undefined
                                    }
                                />
                            </div>
                            <InputError
                                id="email-error"
                                role="alert"
                                message={errors.email}
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password" className={fieldLabel}>
                                Senha
                            </Label>
                            <div className="relative">
                                <Lock className={fieldIcon} />
                                <Input
                                    id="password"
                                    name="password"
                                    type={showPassword ? 'text' : 'password'}
                                    required
                                    autoComplete="current-password"
                                    placeholder="Digite sua senha"
                                    className={cn(fieldInput, 'pr-24')}
                                    aria-invalid={!!errors.password}
                                    aria-describedby={
                                        errors.password
                                            ? 'password-error'
                                            : undefined
                                    }
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword((v) => !v)}
                                    aria-pressed={showPassword}
                                    aria-controls="password"
                                    className="absolute inset-y-0 right-0 rounded-r-xl px-4 text-sm font-semibold text-muted-foreground hover:text-foreground focus-visible:ring-2 focus-visible:ring-emerald-600/60 focus-visible:outline-none"
                                >
                                    {showPassword ? 'Ocultar' : 'Mostrar'}
                                </button>
                            </div>
                            <InputError
                                id="password-error"
                                role="alert"
                                message={errors.password}
                            />
                        </div>

                        <div className="flex items-center justify-between gap-3">
                            <div className="flex items-center gap-2.5">
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    className="size-5 rounded-md"
                                />
                                <Label
                                    htmlFor="remember"
                                    className="text-sm font-medium"
                                >
                                    Lembrar de mim
                                </Label>
                            </div>
                            {canResetPassword && (
                                <TextLink
                                    href={request()}
                                    className="text-sm font-semibold text-foreground no-underline hover:underline"
                                >
                                    Esqueci a senha
                                </TextLink>
                            )}
                        </div>

                        <Button
                            type="submit"
                            disabled={processing}
                            aria-busy={processing}
                            data-test="login-button"
                            className="mt-1 h-12 w-full rounded-xl bg-emerald-600 text-base font-semibold text-white shadow-[0_12px_32px_-10px_rgb(5_150_105/0.7)] hover:bg-emerald-700 dark:bg-emerald-500 dark:text-emerald-950 dark:hover:bg-emerald-400"
                        >
                            {processing && <Spinner />}
                            {processing ? 'Entrando...' : 'Entrar'}
                        </Button>
                    </>
                )}
            </Form>
        </>
    );
}

Login.layout = {
    title: 'Bem-vindo de volta',
    description: 'Entre com o e-mail e a senha da sua unidade de saúde.',
};

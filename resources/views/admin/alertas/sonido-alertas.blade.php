@if (
    auth()->check()
    && \Illuminate\Support\Facades\Route::has('admin.alertas.sonido')
)
    <style>
        .nanos-sonido-control {
            position: fixed;
            right: 20px;
            bottom: 18px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 11px;
            border: 1px solid #414550;
            border-radius: 12px;
            color: #e9ecf3;
            background: #1b1d24;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .25);
            font-family: inherit;
        }

        .nanos-sonido-control button {
            min-height: 33px;
            padding: 7px 11px;
            border: 1px solid #444854;
            border-radius: 8px;
            color: #fff;
            background: #292c35;
            font-family: inherit;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .nanos-sonido-control button.activo {
            border-color: #c91b25;
            background: #c91b25;
        }

        .nanos-sonido-control button:focus-visible {
            outline: 2px solid #ff858e;
            outline-offset: 2px;
        }

        .nanos-sonido-estado {
            max-width: 170px;
            color: #abb2c1;
            font-size: 10px;
            line-height: 1.5;
        }

        @media (max-width: 600px) {
            .nanos-sonido-control {
                right: 10px;
                bottom: 10px;
            }

            .nanos-sonido-estado {
                max-width: 105px;
            }
        }

        @media print {
            .nanos-sonido-control {
                display: none !important;
            }
        }
    </style>

    <div
        class="nanos-sonido-control"
        id="nanosSonidoControl"
        aria-label="Sonido de alertas"
    >
        <button
            type="button"
            id="nanosSonidoActivar"
            aria-pressed="false"
        >
            Activar sonido
        </button>

        <button type="button" id="nanosSonidoProbar">
            Probar
        </button>

        <span
            id="nanosSonidoEstado"
            class="nanos-sonido-estado"
            role="status"
            aria-live="polite"
        >
            Sonido desactivado
        </span>
    </div>

    <script>
    (() => {
        if (window.nanosSonidoAlertasInicializado) {
            return;
        }

        window.nanosSonidoAlertasInicializado = true;

        const url = {{ \Illuminate\Support\Js::from(
            route('admin.alertas.sonido')
        ) }};

        const usuarioId = {{ \Illuminate\Support\Js::from(
            (string) auth()->id()
        ) }};

        const clavePreferencia = 'nanos-sonido-activo-' + usuarioId;
        const claveConocidas = 'nanos-alertas-conocidas-' + usuarioId;

        const botonActivar = document.getElementById('nanosSonidoActivar');
        const botonProbar = document.getElementById('nanosSonidoProbar');
        const estado = document.getElementById('nanosSonidoEstado');

        const AudioContextNanos = window.AudioContext
            || window.webkitAudioContext;

        let contexto = null;
        let consultando = false;
        let detenido = false;
        let inicializado = false;
        let conocidas = new Set();
        let habilitado = false;
        let pendientesDeSonar = false;

        function leerGuardado(clave) {
            try {
                return sessionStorage.getItem(clave);
            } catch {
                return null;
            }
        }

        function guardar(clave, valor) {
            try {
                sessionStorage.setItem(clave, valor);
            } catch {
                // El sonido sigue funcionando durante esta página.
            }
        }

        habilitado = leerGuardado(clavePreferencia) === '1';

        try {
            const guardadas = leerGuardado(claveConocidas);

            if (guardadas !== null) {
                const datos = JSON.parse(guardadas);

                if (Array.isArray(datos)) {
                    conocidas = new Set(datos);
                    inicializado = true;
                }
            }
        } catch {
            conocidas = new Set();
            inicializado = false;
        }

        function actualizarBoton() {
            const listo = habilitado
                && contexto
                && contexto.state === 'running';

            botonActivar.classList.toggle('activo', Boolean(listo));
            botonActivar.setAttribute(
                'aria-pressed',
                listo ? 'true' : 'false'
            );

            if (listo) {
                botonActivar.textContent = 'Silenciar';
                estado.textContent = 'Sonido activado';
            } else if (habilitado) {
                botonActivar.textContent = 'Activar sonido';
                estado.textContent = 'Pulsa para habilitar el audio';
            } else {
                botonActivar.textContent = 'Activar sonido';
                estado.textContent = 'Sonido desactivado';
            }
        }

        async function prepararAudio() {
            if (!AudioContextNanos) {
                estado.textContent = 'Tu navegador no admite este audio';
                return false;
            }

            if (!contexto || contexto.state === 'closed') {
                contexto = new AudioContextNanos();
            }

            if (contexto.state === 'suspended') {
                await contexto.resume();
            }

            return contexto.state === 'running';
        }

        function reproducirTono() {
            if (
                !habilitado
                || !contexto
                || contexto.state !== 'running'
            ) {
                return false;
            }

            const inicio = contexto.currentTime + 0.02;

            [
                { frecuencia: 880, desplazamiento: 0 },
                { frecuencia: 1174.66, desplazamiento: 0.22 }
            ].forEach(nota => {
                const oscilador = contexto.createOscillator();
                const volumen = contexto.createGain();

                const tiempo = inicio + nota.desplazamiento;

                oscilador.type = 'sine';

                oscilador.frequency.setValueAtTime(
                    nota.frecuencia,
                    tiempo
                );

                volumen.gain.setValueAtTime(0.0001, tiempo);

                volumen.gain.exponentialRampToValueAtTime(
                    0.16,
                    tiempo + 0.02
                );

                volumen.gain.exponentialRampToValueAtTime(
                    0.0001,
                    tiempo + 0.18
                );

                oscilador.connect(volumen);
                volumen.connect(contexto.destination);

                oscilador.start(tiempo);
                oscilador.stop(tiempo + 0.20);

                oscilador.onended = () => {
                    oscilador.disconnect();
                    volumen.disconnect();
                };
            });

            return true;
        }

        async function consultarAlertas() {
            if (consultando || detenido) {
                return;
            }

            consultando = true;

            try {
                const respuesta = await fetch(url, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if ([401, 403, 419].includes(respuesta.status)) {
                    detenido = true;
                    estado.textContent = 'Sesión finalizada o acceso no disponible';
                    return;
                }

                if (!respuesta.ok) {
                    return;
                }

                const tipo = respuesta.headers.get('content-type') || '';

                if (!tipo.includes('application/json')) {
                    detenido = true;
                    return;
                }

                const datos = await respuesta.json();

                if (!Array.isArray(datos.identificadores)) {
                    return;
                }

                const actuales = datos.identificadores;

                /*
                 * La primera consulta establece la referencia.
                 * No reproduce las alertas antiguas al entrar.
                 */
                if (!inicializado) {
                    conocidas = new Set(actuales);
                    inicializado = true;

                    guardar(
                        claveConocidas,
                        JSON.stringify([...conocidas])
                    );

                    return;
                }

                const nuevas = actuales.filter(
                    identificador => !conocidas.has(identificador)
                );

                actuales.forEach(identificador => {
                    conocidas.add(identificador);
                });

                guardar(
                    claveConocidas,
                    JSON.stringify([...conocidas])
                );

                if (nuevas.length > 0 && habilitado) {
                    pendientesDeSonar = !reproducirTono();

                    if (pendientesDeSonar) {
                        estado.textContent =
                            'Hay alertas nuevas. Activa el audio';
                    }
                }
            } catch {
                // Reintentar en la siguiente consulta.
            } finally {
                consultando = false;
            }
        }

        botonActivar.addEventListener('click', async () => {
            const listo = habilitado
                && contexto
                && contexto.state === 'running';

            if (listo) {
                habilitado = false;
                pendientesDeSonar = false;
                guardar(clavePreferencia, '0');
                actualizarBoton();
                return;
            }

            try {
                habilitado = true;

                const permitido = await prepararAudio();

                guardar(clavePreferencia, '1');
                actualizarBoton();

                if (permitido) {
                    reproducirTono();
                    pendientesDeSonar = false;
                }
            } catch {
                estado.textContent = 'No se pudo activar el audio';
            }
        });

        botonProbar.addEventListener('click', async () => {
            try {
                habilitado = true;

                const permitido = await prepararAudio();

                guardar(clavePreferencia, '1');
                actualizarBoton();

                if (permitido) {
                    reproducirTono();
                    pendientesDeSonar = false;
                }
            } catch {
                estado.textContent = 'No se pudo reproducir el tono';
            }
        });

        /*
         * Después de navegar a otra página, aprovechar una
         * interacción del usuario para reanudar el audio.
         */
        document.addEventListener('pointerdown', () => {
            if (!habilitado) {
                return;
            }

            prepararAudio().then(permitido => {
                actualizarBoton();

                if (permitido && pendientesDeSonar) {
                    reproducirTono();
                    pendientesDeSonar = false;
                }
            }).catch(() => {
                actualizarBoton();
            });
        }, { passive: true });

        actualizarBoton();
        consultarAlertas();

        setInterval(consultarAlertas, 30000);
    })();
    </script>
@endif
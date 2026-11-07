@php
    $editando = isset($usuario);
@endphp

<style>
    .password-tools {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .password-tool-btn {
        border: 1px solid rgba(255, 255, 255, .10);
        background: rgba(255, 255, 255, .04);
        color: #d1d5db;
        border-radius: 8px;
        padding: 9px 14px;
        cursor: pointer;
        font-size: 12px;
        transition: .2s ease;
    }

    .password-tool-btn:hover {
        border-color: #e31b23;
        color: #fff;
        background: rgba(227, 27, 35, .08);
    }

    .password-strength {
        margin-top: 12px;
    }

    .password-strength-bar {
        width: 100%;
        height: 7px;
        border-radius: 20px;
        background: rgba(255, 255, 255, .08);
        overflow: hidden;
    }

    .password-strength-fill {
        width: 0;
        height: 100%;
        border-radius: 20px;
        transition: width .25s ease, background .25s ease;
    }

    .password-strength-info {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 7px;
        font-size: 11px;
    }

    #passwordStrengthText {
        color: #8b8d93;
        font-weight: 700;
    }

    #passwordRequirements {
        color: #666970;
        text-align: right;
    }

    .password-generated-message {
        display: none;
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        background: rgba(34, 197, 94, .08);
        border: 1px solid rgba(34, 197, 94, .20);
        color: #86efac;
        font-size: 11px;
    }
</style>


<div class="form-panel-admin">

    {{-- ====================================================== --}}
    {{-- EMPLEADO --}}
    {{-- ====================================================== --}}

    <div class="form-section">

        <div class="form-section-title">
            <strong>Empleado relacionado</strong>

            <span>
                La cuenta de acceso debe pertenecer a un empleado de NANOS.
            </span>
        </div>


        <div class="form-grid">

            <div class="form-group full">

                <label for="empleado_id">
                    Empleado
                    <span class="required">*</span>
                </label>


                @if($editando)

                    <input
                        type="text"
                        class="form-control"
                        value="{{
                            $usuario->empleado
                                ? $usuario->empleado->codigo_empleado
                                    . ' - '
                                    . $usuario->empleado->nombres
                                    . ' '
                                    . $usuario->empleado->apellidos
                                : 'Empleado no disponible'
                        }}"
                        disabled
                    >

                    <small
                        style="
                            display:block;
                            margin-top:7px;
                            color:#666970;
                        "
                    >
                        El empleado asociado a una cuenta
                        no puede modificarse.
                    </small>

                @else

                    <select
                        name="empleado_id"
                        id="empleado_id"
                        class="form-control"
                        required
                    >

                        <option value="">
                            Selecciona un empleado
                        </option>


                        @foreach($empleados as $empleado)

                            <option
                                value="{{ $empleado->id }}"
                                @selected(
                                    old('empleado_id')
                                    == $empleado->id
                                )
                            >
                                {{ $empleado->codigo_empleado }}
                                -
                                {{ $empleado->nombres }}
                                {{ $empleado->apellidos }}
                            </option>

                        @endforeach

                    </select>


                    @if($empleados->isEmpty())

                        <small
                            style="
                                display:block;
                                margin-top:7px;
                                color:#f5b942;
                            "
                        >
                            No existen empleados activos
                            disponibles sin una cuenta.
                        </small>

                    @endif

                @endif


                @error('empleado_id')

                    <span class="field-error">
                        {{ $message }}
                    </span>

                @enderror

            </div>

        </div>

    </div>


    {{-- ====================================================== --}}
    {{-- DATOS DE ACCESO --}}
    {{-- ====================================================== --}}

    <div class="form-section">

        <div class="form-section-title">

            <strong>
                Datos de acceso
            </strong>

            <span>
                Configura el usuario, rol y estado de la cuenta.
            </span>

        </div>


        <div class="form-grid">

            {{-- USUARIO --}}
            <div class="form-group">

                <label for="username">
                    Nombre de usuario
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="username"
                    id="username"
                    value="{{
                        old(
                            'username',
                            $editando
                                ? $usuario->username
                                : ''
                        )
                    }}"
                    class="form-control"
                    maxlength="80"
                    autocomplete="off"
                    required
                    placeholder="Ej. juan.perez"
                >

                <small
                    style="
                        display:block;
                        margin-top:7px;
                        color:#666970;
                    "
                >
                    Este será el nombre utilizado para iniciar sesión.
                </small>

                @error('username')

                    <span class="field-error">
                        {{ $message }}
                    </span>

                @enderror

            </div>


            {{-- ROL --}}
            <div class="form-group">

                <label for="rol_id">
                    Rol
                    <span class="required">*</span>
                </label>

                <select
                    name="rol_id"
                    id="rol_id"
                    class="form-control"
                    required
                >

                    <option value="">
                        Selecciona un rol
                    </option>


                    @foreach($roles as $rol)

                        <option
                            value="{{ $rol->id }}"
                            @selected(
                                old(
                                    'rol_id',
                                    $editando
                                        ? $usuario->rol_id
                                        : ''
                                )
                                == $rol->id
                            )
                        >
                            {{
                                ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $rol->nombre
                                    )
                                )
                            }}
                        </option>

                    @endforeach

                </select>


                @error('rol_id')

                    <span class="field-error">
                        {{ $message }}
                    </span>

                @enderror

            </div>


            {{-- ESTADO --}}
            <div class="form-group">

                <label for="estado">
                    Estado
                    <span class="required">*</span>
                </label>

                <select
                    name="estado"
                    id="estado"
                    class="form-control"
                    required
                >

                    <option
                        value="activo"
                        @selected(
                            old(
                                'estado',
                                $editando
                                    ? $usuario->estado
                                    : 'activo'
                            )
                            === 'activo'
                        )
                    >
                        Activo
                    </option>


                    <option
                        value="inactivo"
                        @selected(
                            old(
                                'estado',
                                $editando
                                    ? $usuario->estado
                                    : 'activo'
                            )
                            === 'inactivo'
                        )
                    >
                        Inactivo
                    </option>

                </select>


                @error('estado')

                    <span class="field-error">
                        {{ $message }}
                    </span>

                @enderror

            </div>

        </div>

    </div>


    {{-- ====================================================== --}}
    {{-- CONTRASEÑA --}}
    {{-- ====================================================== --}}

    <div class="form-section">

        <div class="form-section-title">

            <strong>
                {{
                    $editando
                        ? 'Cambiar contraseña'
                        : 'Contraseña de acceso'
                }}
            </strong>

            <span>

                @if($editando)

                    Puedes escribir una nueva contraseña
                    o generar una automáticamente.
                    Si la dejas vacía se conservará la actual.

                @else

                    Puedes crear tu propia contraseña
                    o generar una contraseña segura automáticamente.

                @endif

            </span>

        </div>


        <div class="form-grid">

            {{-- CONTRASEÑA --}}
            <div class="form-group full">

                <label for="password">

                    {{
                        $editando
                            ? 'Nueva contraseña'
                            : 'Contraseña'
                    }}

                    @unless($editando)
                        <span class="required">*</span>
                    @endunless

                </label>


                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    minlength="8"
                    autocomplete="new-password"
                    {{ $editando ? '' : 'required' }}
                    placeholder="{{
                        $editando
                            ? 'Déjala vacía para conservar la actual'
                            : 'Escribe o genera una contraseña'
                    }}"
                >


                <div class="password-tools">

                    <button
                        type="button"
                        class="password-tool-btn"
                        id="generatePassword"
                    >
                        ⚡ Generar contraseña segura
                    </button>


                    <button
                        type="button"
                        class="password-tool-btn"
                        id="togglePassword"
                    >
                        👁 Mostrar
                    </button>


                    <button
                        type="button"
                        class="password-tool-btn"
                        id="copyPassword"
                    >
                        ⧉ Copiar
                    </button>

                </div>


                {{-- INDICADOR DE SEGURIDAD --}}

                <div class="password-strength">

                    <div class="password-strength-bar">

                        <div
                            class="password-strength-fill"
                            id="passwordStrengthFill"
                        ></div>

                    </div>


                    <div class="password-strength-info">

                        <span id="passwordStrengthText">
                            Sin contraseña
                        </span>

                        <span id="passwordRequirements">
                            Mínimo 8 caracteres
                        </span>

                    </div>

                </div>


                <div
                    class="password-generated-message"
                    id="passwordGeneratedMessage"
                >
                    ✓ Contraseña segura generada automáticamente.
                    Recuerda entregársela al empleado.
                </div>


                @error('password')

                    <span class="field-error">
                        {{ $message }}
                    </span>

                @enderror

            </div>


            {{-- CONFIRMAR CONTRASEÑA --}}
            <div class="form-group full">

                <label for="password_confirmation">

                    Confirmar contraseña

                    @unless($editando)
                        <span class="required">*</span>
                    @endunless

                </label>


                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control"
                    minlength="8"
                    autocomplete="new-password"
                    {{ $editando ? '' : 'required' }}
                    placeholder="Repite la contraseña"
                >


                <small
                    id="passwordMatch"
                    style="
                        display:block;
                        margin-top:7px;
                        color:#666970;
                    "
                >
                    Ambas contraseñas deben coincidir.
                </small>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const password =
        document.getElementById('password');

    const confirmation =
        document.getElementById('password_confirmation');

    const generateButton =
        document.getElementById('generatePassword');

    const toggleButton =
        document.getElementById('togglePassword');

    const copyButton =
        document.getElementById('copyPassword');

    const strengthFill =
        document.getElementById('passwordStrengthFill');

    const strengthText =
        document.getElementById('passwordStrengthText');

    const requirements =
        document.getElementById('passwordRequirements');

    const matchText =
        document.getElementById('passwordMatch');

    const generatedMessage =
        document.getElementById('passwordGeneratedMessage');


    /*
    |--------------------------------------------------------------------------
    | GENERAR CONTRASEÑA SEGURA
    |--------------------------------------------------------------------------
    */

    function generarPassword() {

        const mayusculas =
            'ABCDEFGHJKLMNPQRSTUVWXYZ';

        const minusculas =
            'abcdefghijkmnopqrstuvwxyz';

        const numeros =
            '23456789';

        const simbolos =
            '!@#$%*-_?';

        const todos =
            mayusculas
            + minusculas
            + numeros
            + simbolos;


        let resultado = '';

        /*
         * Garantizamos al menos:
         * 1 mayúscula
         * 1 minúscula
         * 1 número
         * 1 símbolo
         */

        resultado +=
            mayusculas[
                Math.floor(
                    Math.random()
                    * mayusculas.length
                )
            ];

        resultado +=
            minusculas[
                Math.floor(
                    Math.random()
                    * minusculas.length
                )
            ];

        resultado +=
            numeros[
                Math.floor(
                    Math.random()
                    * numeros.length
                )
            ];

        resultado +=
            simbolos[
                Math.floor(
                    Math.random()
                    * simbolos.length
                )
            ];


        /*
         * Completamos hasta 14 caracteres.
         */

        while (resultado.length < 14) {

            resultado +=
                todos[
                    Math.floor(
                        Math.random()
                        * todos.length
                    )
                ];
        }


        /*
         * Mezclar caracteres.
         */

        resultado =
            resultado
                .split('')
                .sort(
                    () => Math.random() - 0.5
                )
                .join('');


        return resultado;
    }


    /*
    |--------------------------------------------------------------------------
    | MEDIR SEGURIDAD
    |--------------------------------------------------------------------------
    */

    function evaluarPassword(valor) {

        if (!valor) {

            strengthFill.style.width = '0%';

            strengthFill.style.background =
                'transparent';

            strengthText.textContent =
                'Sin contraseña';

            strengthText.style.color =
                '#8b8d93';

            requirements.textContent =
                'Mínimo 8 caracteres';

            return;
        }


        let puntos = 0;


        if (valor.length >= 8) {
            puntos++;
        }

        if (valor.length >= 12) {
            puntos++;
        }

        if (/[a-z]/.test(valor)) {
            puntos++;
        }

        if (/[A-Z]/.test(valor)) {
            puntos++;
        }

        if (/[0-9]/.test(valor)) {
            puntos++;
        }

        if (/[^A-Za-z0-9]/.test(valor)) {
            puntos++;
        }


        if (puntos <= 2) {

            strengthFill.style.width =
                '20%';

            strengthFill.style.background =
                '#ef4444';

            strengthText.textContent =
                'Muy débil';

            strengthText.style.color =
                '#ef4444';

        }
        else if (puntos === 3) {

            strengthFill.style.width =
                '40%';

            strengthFill.style.background =
                '#f97316';

            strengthText.textContent =
                'Débil';

            strengthText.style.color =
                '#f97316';

        }
        else if (puntos === 4) {

            strengthFill.style.width =
                '60%';

            strengthFill.style.background =
                '#eab308';

            strengthText.textContent =
                'Seguridad media';

            strengthText.style.color =
                '#eab308';

        }
        else if (puntos === 5) {

            strengthFill.style.width =
                '80%';

            strengthFill.style.background =
                '#22c55e';

            strengthText.textContent =
                'Segura';

            strengthText.style.color =
                '#22c55e';

        }
        else {

            strengthFill.style.width =
                '100%';

            strengthFill.style.background =
                '#16a34a';

            strengthText.textContent =
                'Muy segura';

            strengthText.style.color =
                '#22c55e';

        }


        const faltantes = [];


        if (valor.length < 8) {
            faltantes.push('8+ caracteres');
        }

        if (!/[A-Z]/.test(valor)) {
            faltantes.push('mayúscula');
        }

        if (!/[a-z]/.test(valor)) {
            faltantes.push('minúscula');
        }

        if (!/[0-9]/.test(valor)) {
            faltantes.push('número');
        }

        if (!/[^A-Za-z0-9]/.test(valor)) {
            faltantes.push('símbolo');
        }


        requirements.textContent =
            faltantes.length
                ? 'Falta: ' + faltantes.join(', ')
                : 'Cumple los requisitos';
    }


    /*
    |--------------------------------------------------------------------------
    | COMPROBAR COINCIDENCIA
    |--------------------------------------------------------------------------
    */

    function comprobarCoincidencia() {

        if (!confirmation.value) {

            matchText.textContent =
                'Ambas contraseñas deben coincidir.';

            matchText.style.color =
                '#666970';

            return;
        }


        if (
            password.value
            === confirmation.value
        ) {

            matchText.textContent =
                '✓ Las contraseñas coinciden.';

            matchText.style.color =
                '#22c55e';

        }
        else {

            matchText.textContent =
                '✕ Las contraseñas no coinciden.';

            matchText.style.color =
                '#ef4444';

        }
    }


    /*
    |--------------------------------------------------------------------------
    | GENERAR
    |--------------------------------------------------------------------------
    */

    generateButton.addEventListener(
        'click',
        function () {

            const nuevaPassword =
                generarPassword();

            password.value =
                nuevaPassword;

            confirmation.value =
                nuevaPassword;


            /*
             * Mostramos temporalmente para que
             * el administrador pueda verla/copiarla.
             */

            password.type = 'text';

            confirmation.type = 'text';

            toggleButton.textContent =
                '🙈 Ocultar';


            generatedMessage.style.display =
                'block';


            evaluarPassword(
                nuevaPassword
            );

            comprobarCoincidencia();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR / OCULTAR
    |--------------------------------------------------------------------------
    */

    toggleButton.addEventListener(
        'click',
        function () {

            const visible =
                password.type === 'text';


            password.type =
                visible
                    ? 'password'
                    : 'text';

            confirmation.type =
                visible
                    ? 'password'
                    : 'text';


            toggleButton.textContent =
                visible
                    ? '👁 Mostrar'
                    : '🙈 Ocultar';
        }
    );


    /*
    |--------------------------------------------------------------------------
    | COPIAR
    |--------------------------------------------------------------------------
    */

    copyButton.addEventListener(
        'click',
        async function () {

            if (!password.value) {

                alert(
                    'Primero escribe o genera una contraseña.'
                );

                return;
            }


            try {

                await navigator.clipboard.writeText(
                    password.value
                );

                const textoOriginal =
                    copyButton.textContent;

                copyButton.textContent =
                    '✓ Copiada';


                setTimeout(
                    function () {

                        copyButton.textContent =
                            textoOriginal;

                    },
                    1500
                );

            }
            catch (error) {

                password.select();

                document.execCommand(
                    'copy'
                );

                alert(
                    'Contraseña copiada.'
                );
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | EVENTOS
    |--------------------------------------------------------------------------
    */

    password.addEventListener(
        'input',
        function () {

            generatedMessage.style.display =
                'none';

            evaluarPassword(
                password.value
            );

            comprobarCoincidencia();
        }
    );


    confirmation.addEventListener(
        'input',
        comprobarCoincidencia
    );

});
</script>
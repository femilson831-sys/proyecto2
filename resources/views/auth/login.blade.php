<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Iniciar sesión | Minisúper NANOS</title>

    <style>

        /* =====================================================
           VARIABLES
        ===================================================== */

        :root {
            --red: #e31b23;
            --red-dark: #b90f16;
            --red-light: #ff4148;

            --black: #0c0d0f;
            --dark: #121316;
            --dark-2: #18191d;
            --dark-3: #202126;
            --dark-4: #292a30;

            --white: #ffffff;

            --text: #f5f5f5;
            --text-soft: #9da0a8;

            --border: rgba(255,255,255,.10);

            --success: #42d47a;
            --success-bg: rgba(66,212,122,.08);

            --error: #ff656b;
            --error-bg: rgba(255,70,76,.08);
        }


        /* =====================================================
           RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        html,
        body {
            width: 100%;
            min-height: 100%;
        }


        body {
            min-height: 100vh;

            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Arial,
                sans-serif;

            background: var(--black);

            color: var(--text);
        }


        button,
        input {
            font-family: inherit;
        }


        /* =====================================================
           FONDO GENERAL
        ===================================================== */

        .page {
            position: relative;

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 35px;

            overflow: hidden;

            background:
                radial-gradient(
                    circle at 10% 15%,
                    rgba(227, 27, 35, .25),
                    transparent 28%
                ),

                radial-gradient(
                    circle at 90% 85%,
                    rgba(227, 27, 35, .15),
                    transparent 32%
                ),

                radial-gradient(
                    circle at 50% 50%,
                    rgba(255,255,255,.025),
                    transparent 40%
                ),

                linear-gradient(
                    135deg,
                    #090a0c 0%,
                    #121316 40%,
                    #17181c 70%,
                    #200709 100%
                );
        }


        /* =====================================================
           CUADRÍCULA
        ===================================================== */

        .grid {
            position: absolute;
            inset: 0;

            pointer-events: none;

            opacity: .5;

            background-image:
                linear-gradient(
                    rgba(255,255,255,.025) 1px,
                    transparent 1px
                ),

                linear-gradient(
                    90deg,
                    rgba(255,255,255,.025) 1px,
                    transparent 1px
                );

            background-size: 45px 45px;
        }


        /* =====================================================
           LUCES DE FONDO
        ===================================================== */

        .circle {
            position: absolute;

            border-radius: 50%;

            pointer-events: none;

            animation:
                floating 10s ease-in-out infinite;
        }


        .circle-1 {
            width: 450px;
            height: 450px;

            top: -230px;
            left: -180px;

            background:
                rgba(227, 27, 35, .16);

            filter: blur(10px);
        }


        .circle-2 {
            width: 550px;
            height: 550px;

            bottom: -320px;
            right: -240px;

            background:
                rgba(227, 27, 35, .11);

            filter: blur(15px);

            animation-delay: -4s;
        }


        .circle-3 {
            width: 300px;
            height: 300px;

            top: 30%;
            left: 45%;

            background:
                rgba(255,255,255,.02);

            filter: blur(20px);

            animation-delay: -6s;
        }


        @keyframes floating {
            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(14px, -18px);
            }
        }


        /* =====================================================
           CONTENEDOR LOGIN
        ===================================================== */

        .login-container {
            position: relative;

            z-index: 10;

            width: 100%;
            max-width: 1000px;

            min-height: 620px;

            display: grid;

            grid-template-columns:
                1fr 1fr;

            background: var(--dark);

            border:
                1px solid rgba(255,255,255,.08);

            border-radius: 28px;

            overflow: hidden;

            box-shadow:
                0 40px 100px rgba(0,0,0,.55);

            animation:
                showLogin
                .7s
                cubic-bezier(.16,1,.3,1);
        }


        @keyframes showLogin {
            from {
                opacity: 0;

                transform:
                    translateY(25px)
                    scale(.98);
            }

            to {
                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);
            }
        }


        /* =====================================================
           PANEL IZQUIERDO
        ===================================================== */

        .brand-panel {
            position: relative;

            padding: 55px;

            display: flex;

            flex-direction: column;

            justify-content: space-between;

            color: white;

            overflow: hidden;

            background:
                radial-gradient(
                    circle at 85% 15%,
                    rgba(227,27,35,.18),
                    transparent 35%
                ),

                linear-gradient(
                    145deg,
                    #0d0e10 0%,
                    #17181b 60%,
                    #390609 150%
                );

            border-right:
                1px solid rgba(255,255,255,.06);
        }


        .brand-panel::before {
            content: "";

            position: absolute;

            width: 430px;
            height: 430px;

            border-radius: 50%;

            right: -260px;
            top: -210px;

            background:
                rgba(227,27,35,.15);
        }


        .brand-panel::after {
            content: "";

            position: absolute;

            width: 350px;
            height: 350px;

            border-radius: 50%;

            left: -230px;
            bottom: -210px;

            background:
                rgba(227,27,35,.07);
        }


        /* =====================================================
           LOGO
        ===================================================== */

        .brand-header {
            position: relative;

            z-index: 2;

            display: flex;
            align-items: center;

            gap: 16px;
        }


        .logo-box {
            width: 76px;
            height: 76px;

            padding: 7px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: white;

            border-radius: 20px;

            box-shadow:
                0 15px 35px rgba(0,0,0,.35);
        }


        .logo-box img {
            display: block;

            width: 100%;
            height: 100%;

            object-fit: contain;

            border-radius: 13px;
        }


        .brand-title h2 {
            font-size: 28px;

            color: white;

            letter-spacing: -.5px;
        }


        .brand-title span {
            display: block;

            margin-top: 4px;

            color:
                rgba(255,255,255,.52);

            font-size: 11px;

            font-weight: 600;

            letter-spacing: 1.7px;

            text-transform: uppercase;
        }


        /* =====================================================
           INFORMACIÓN IZQUIERDA
        ===================================================== */

        .brand-center {
            position: relative;

            z-index: 2;
        }


        .brand-line {
            width: 48px;
            height: 4px;

            margin-bottom: 25px;

            border-radius: 50px;

            background:
                linear-gradient(
                    90deg,
                    var(--red),
                    var(--red-light)
                );

            box-shadow:
                0 0 18px rgba(227,27,35,.40);
        }


        .brand-center h1 {
            max-width: 410px;

            color: white;

            font-size: 43px;

            line-height: 1.1;

            letter-spacing: -1.5px;
        }


        .brand-center h1 span {
            color: var(--red-light);
        }


        .brand-center p {
            max-width: 390px;

            margin-top: 20px;

            color:
                rgba(255,255,255,.58);

            line-height: 1.75;

            font-size: 14px;
        }


        /* =====================================================
           FOOTER IZQUIERDO
        ===================================================== */

        .brand-footer {
            position: relative;

            z-index: 2;

            display: flex;
            align-items: center;

            gap: 11px;

            color:
                rgba(255,255,255,.42);

            font-size: 12px;
        }


        .status-dot {
            width: 8px;
            height: 8px;

            flex-shrink: 0;

            border-radius: 50%;

            background: #39cf74;

            box-shadow:
                0 0 0 5px rgba(57,207,116,.10);
        }


        /* =====================================================
           PANEL DERECHO - AHORA OSCURO
        ===================================================== */

        .form-panel {
            position: relative;

            padding: 65px;

            display: flex;
            align-items: center;

            background:
                radial-gradient(
                    circle at 100% 0%,
                    rgba(227,27,35,.07),
                    transparent 30%
                ),

                linear-gradient(
                    145deg,
                    #1b1c20 0%,
                    #151619 100%
                );
        }


        .form-panel::before {
            content: "";

            position: absolute;

            top: 0;
            left: 0;
            right: 0;

            height: 1px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255,255,255,.08),
                    transparent
                );
        }


        .form-content {
            position: relative;

            z-index: 2;

            width: 100%;
        }


        /* =====================================================
           LOGO RESPONSIVE
        ===================================================== */

        .mobile-logo {
            display: none;
        }


        /* =====================================================
           ENCABEZADO
        ===================================================== */

        .form-header {
            margin-bottom: 35px;
        }


        .form-header .subtitle {
            display: block;

            margin-bottom: 10px;

            color: var(--red-light);

            font-size: 11px;

            font-weight: 750;

            letter-spacing: 1.5px;

            text-transform: uppercase;
        }


        .form-header h2 {
            margin-bottom: 10px;

            color: white;

            font-size: 32px;

            letter-spacing: -1px;
        }


        .form-header p {
            color: #9a9da5;

            line-height: 1.6;

            font-size: 14px;
        }


        /* =====================================================
           ALERTAS
        ===================================================== */

        .alert {
            display: flex;

            align-items: flex-start;

            gap: 10px;

            padding: 13px 14px;

            margin-bottom: 20px;

            border-radius: 12px;

            font-size: 13px;
        }


        .alert-error {
            color: #ff858a;

            background:
                rgba(227,27,35,.08);

            border:
                1px solid rgba(227,27,35,.20);
        }


        .alert-success {
            color: #55da87;

            background:
                rgba(57,207,116,.08);

            border:
                1px solid rgba(57,207,116,.18);
        }


        /* =====================================================
           CAMPOS
        ===================================================== */

        .field {
            margin-bottom: 21px;
        }


        .field label {
            display: block;

            margin-bottom: 8px;

            color: #e6e6e8;

            font-size: 13px;

            font-weight: 650;
        }


        .input-wrapper {
            position: relative;
        }


        .input-icon {
            position: absolute;

            left: 16px;
            top: 50%;

            width: 20px;
            height: 20px;

            transform:
                translateY(-50%);

            color: #777b84;

            pointer-events: none;

            transition: .2s;
        }


        .input-wrapper input {
            width: 100%;
            height: 54px;

            padding:
                0 48px;

            border:
                1px solid rgba(255,255,255,.09);

            border-radius: 14px;

            outline: none;

            background: #24252a;

            color: #ffffff;

            font-size: 14px;

            transition:
                border-color .2s,
                box-shadow .2s,
                background .2s,
                transform .2s;
        }


        .input-wrapper input::placeholder {
            color: #696c74;
        }


        .input-wrapper input:hover {
            background: #292a30;

            border-color:
                rgba(255,255,255,.15);
        }


        .input-wrapper input:focus {
            background: #292a30;

            border-color: var(--red);

            box-shadow:
                0 0 0 4px
                rgba(227,27,35,.10);
        }


        .input-wrapper:focus-within
        .input-icon {
            color: var(--red-light);
        }


        /* =====================================================
           MOSTRAR CONTRASEÑA
        ===================================================== */

        .password-toggle {
            position: absolute;

            top: 50%;
            right: 10px;

            width: 36px;
            height: 36px;

            transform:
                translateY(-50%);

            display: flex;

            align-items: center;
            justify-content: center;

            border: none;

            border-radius: 9px;

            background: transparent;

            color: #777b84;

            cursor: pointer;

            transition: .2s;
        }


        .password-toggle:hover {
            color: var(--red-light);

            background:
                rgba(227,27,35,.09);
        }


        .password-toggle svg {
            width: 19px;
            height: 19px;
        }


        /* =====================================================
           MANTENER SESIÓN
        ===================================================== */

        .options {
            margin:
                4px
                0
                18px;
        }


        .remember {
            display: inline-flex;

            align-items: center;

            gap: 9px;

            color: #a8abb2;

            font-size: 13px;

            cursor: pointer;

            user-select: none;
        }


        .remember input {
            width: 17px;
            height: 17px;

            accent-color: var(--red);

            cursor: pointer;
        }


        /* =====================================================
           MENSAJE DE AYUDA
        ===================================================== */

        .login-help {
            display: flex;

            align-items: flex-start;

            gap: 11px;

            margin-bottom: 26px;

            padding: 13px 14px;

            border:
                1px solid rgba(255,255,255,.07);

            border-radius: 12px;

            background:
                rgba(255,255,255,.025);

            color: #8f929a;

            font-size: 12px;

            line-height: 1.55;
        }


        .login-help svg {
            width: 18px;
            height: 18px;

            flex-shrink: 0;

            margin-top: 1px;

            color: var(--red-light);
        }


        .login-help strong {
            color: #c9cbd0;

            font-weight: 600;
        }


        /* =====================================================
           BOTÓN LOGIN
        ===================================================== */

        .login-button {
            position: relative;

            width: 100%;
            height: 55px;

            display: flex;

            align-items: center;
            justify-content: center;

            gap: 10px;

            border: none;

            border-radius: 14px;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    #ec2028,
                    #bd0e15
                );

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            overflow: hidden;

            box-shadow:
                0 14px 30px rgba(227,27,35,.22);

            transition:
                transform .2s,
                box-shadow .2s;
        }


        .login-button::before {
            content: "";

            position: absolute;

            top: 0;
            left: -100%;

            width: 100%;
            height: 100%;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255,255,255,.20),
                    transparent
                );

            transition: .5s;
        }


        .login-button:hover::before {
            left: 100%;
        }


        .login-button:hover {
            transform:
                translateY(-2px);

            box-shadow:
                0 18px 35px rgba(227,27,35,.30);
        }


        .login-button:active {
            transform:
                translateY(0);
        }


        .login-button:disabled {
            cursor: not-allowed;

            opacity: .75;
        }


        .login-button svg {
            width: 18px;
            height: 18px;
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .form-footer {
            margin-top: 35px;

            padding-top: 24px;

            border-top:
                1px solid rgba(255,255,255,.07);

            text-align: center;

            color: #666970;

            font-size: 11px;
        }


        /* =====================================================
           RESPONSIVE TABLET
        ===================================================== */

        @media (max-width: 900px) {

            .page {
                padding: 25px;
            }


            .login-container {
                max-width: 520px;

                display: block;

                min-height: auto;
            }


            .brand-panel {
                display: none;
            }


            .form-panel {
                padding: 45px;
            }


            .mobile-logo {
                display: flex;

                align-items: center;

                gap: 13px;

                margin-bottom: 35px;
            }


            .mobile-logo .logo-box {
                width: 60px;
                height: 60px;
            }


            .mobile-name h3 {
                color: white;

                font-size: 22px;
            }


            .mobile-name span {
                display: block;

                margin-top: 3px;

                color: #777a82;

                font-size: 10px;

                letter-spacing: 1px;

                text-transform: uppercase;
            }

        }


        /* =====================================================
           RESPONSIVE CELULAR
        ===================================================== */

        @media (max-width: 520px) {

            .page {
                min-height: 100vh;

                padding:
                    20px
                    14px;

                background:
                    radial-gradient(
                        circle at 10% 10%,
                        rgba(227,27,35,.23),
                        transparent 32%
                    ),

                    radial-gradient(
                        circle at 90% 90%,
                        rgba(227,27,35,.13),
                        transparent 35%
                    ),

                    linear-gradient(
                        145deg,
                        #090a0c,
                        #17181c 70%,
                        #210709
                    );
            }


            .login-container {
                width: 100%;

                min-height: auto;

                border-radius: 22px;

                box-shadow:
                    0 25px 60px
                    rgba(0,0,0,.50);
            }


            .form-panel {
                padding:
                    35px
                    25px;
            }


            .form-header {
                margin-bottom: 30px;
            }


            .form-header h2 {
                font-size: 28px;
            }


            .login-help {
                font-size: 11.5px;
            }

        }


        /* =====================================================
           ACCESIBILIDAD
        ===================================================== */

        @media (
            prefers-reduced-motion:
            reduce
        ) {

            *,
            *::before,
            *::after {
                animation-duration:
                    .01ms !important;

                animation-iteration-count:
                    1 !important;

                transition-duration:
                    .01ms !important;
            }

        }

    </style>

</head>


<body>


<div class="page">


    <!-- FONDO -->

    <div class="grid"></div>

    <div class="circle circle-1"></div>

    <div class="circle circle-2"></div>

    <div class="circle circle-3"></div>



    <main class="login-container">


        <!-- =================================================
             PANEL IZQUIERDO
        ================================================== -->

        <section class="brand-panel">


            <div class="brand-header">


                <div class="logo-box">

                    <img
                        src="{{ asset('img/logo.png') }}"
                        alt="Logo Minisúper NANOS"
                    >

                </div>


                <div class="brand-title">

                    <h2>
                        NANOS
                    </h2>

                    <span>
                        Minisúper
                    </span>

                </div>


            </div>



            <div class="brand-center">


                <div class="brand-line"></div>


                <h1>

                    Gestión eficiente,
                    <span>
                        control preciso.
                    </span>

                </h1>


                <p>
                    Sistema administrativo para el
                    control y seguimiento de las
                    operaciones del Minisúper NANOS.
                </p>


            </div>



            <div class="brand-footer">

                <span class="status-dot"></span>

                Sistema NANOS · Acceso autorizado

            </div>


        </section>



        <!-- =================================================
             PANEL DERECHO
        ================================================== -->

        <section class="form-panel">


            <div class="form-content">


                <!-- LOGO PARA TABLET / CELULAR -->

                <div class="mobile-logo">


                    <div class="logo-box">

                        <img
                            src="{{ asset('img/logo.png') }}"
                            alt="Logo NANOS"
                        >

                    </div>


                    <div class="mobile-name">

                        <h3>
                            NANOS
                        </h3>

                        <span>
                            Minisúper
                        </span>

                    </div>


                </div>



                <!-- ENCABEZADO -->

                <header class="form-header">


                    <span class="subtitle">
                        Acceso al sistema
                    </span>


                    <h2>
                        Iniciar sesión
                    </h2>


                    <p>
                        Ingresa tu usuario y contraseña
                        para continuar.
                    </p>


                </header>



                <!-- MENSAJE EXITOSO -->

                @if(session('success'))

                    <div class="alert alert-success">

                        <strong>✓</strong>

                        <div>
                            {{ session('success') }}
                        </div>

                    </div>

                @endif



                <!-- ERRORES -->

                @if($errors->any())

                    <div class="alert alert-error">

                        <strong>!</strong>

                        <div>

                            @foreach($errors->all() as $error)

                                <div>
                                    {{ $error }}
                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif



                <!-- =================================================
                     FORMULARIO
                ================================================== -->

                <form
                    id="loginForm"
                    method="POST"
                    action="{{ route('login.procesar') }}"
                >

                    @csrf



                    <!-- USUARIO -->

                    <div class="field">


                        <label for="username">
                            Usuario
                        </label>


                        <div class="input-wrapper">


                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >

                                <path
                                    d="M20 21a8 8 0 0 0-16 0"
                                />

                                <circle
                                    cx="12"
                                    cy="7"
                                    r="4"
                                />

                            </svg>


                            <input
                                id="username"
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                placeholder="Ingrese su usuario"
                                maxlength="80"
                                autocomplete="username"
                                required
                                autofocus
                            >


                        </div>


                    </div>



                    <!-- CONTRASEÑA -->

                    <div class="field">


                        <label for="password">
                            Contraseña
                        </label>


                        <div class="input-wrapper">


                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >

                                <rect
                                    x="4"
                                    y="10"
                                    width="16"
                                    height="11"
                                    rx="2"
                                />

                                <path
                                    d="M8 10V7a4 4 0 0 1 8 0v3"
                                />

                            </svg>


                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Ingrese su contraseña"
                                autocomplete="current-password"
                                required
                            >


                            <button
                                type="button"
                                class="password-toggle"
                                id="passwordToggle"
                                aria-label="Mostrar contraseña"
                            >


                                <svg
                                    id="eyeIcon"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >

                                    <path
                                        d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="3"
                                    />

                                </svg>


                            </button>


                        </div>


                    </div>



                    <!-- MANTENER SESIÓN -->

                    <div class="options">


                        <label class="remember">


                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                            >


                            <span>
                                Mantener sesión iniciada
                            </span>


                        </label>


                    </div>



                    <!-- =================================================
                         AYUDA PARA CREDENCIALES
                    ================================================== -->

                    <div class="login-help">


                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >

                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                            />

                            <path
                                d="M9.5 9a2.7 2.7 0 0 1 5.2 1c0 2-2.7 2.2-2.7 4"
                            />

                            <path
                                d="M12 18h.01"
                            />

                        </svg>


                        <div>

                            <strong>
                                ¿Olvidaste tu usuario o contraseña?
                            </strong>

                            <br>

                            Comunícate con el administrador
                            del sistema.

                        </div>


                    </div>



                    <!-- BOTÓN -->

                    <button
                        id="loginButton"
                        class="login-button"
                        type="submit"
                    >


                        <span id="buttonText">
                            Iniciar sesión
                        </span>


                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path
                                d="M5 12h14"
                            />

                            <path
                                d="m13 6 6 6-6 6"
                            />

                        </svg>


                    </button>


                </form>



                <!-- FOOTER -->

                <footer class="form-footer">

                    Minisúper NANOS
                    ·
                    Sistema de Gestión
                    ·
                    {{ date('Y') }}

                </footer>


            </div>


        </section>


    </main>


</div>



<script>

    /* =========================================================
       MOSTRAR / OCULTAR CONTRASEÑA
    ========================================================= */

    const password =
        document.getElementById('password');

    const passwordToggle =
        document.getElementById('passwordToggle');

    const eyeIcon =
        document.getElementById('eyeIcon');


    passwordToggle.addEventListener(
        'click',
        function () {

            const isHidden =
                password.type === 'password';


            password.type =
                isHidden
                    ? 'text'
                    : 'password';


            passwordToggle.setAttribute(

                'aria-label',

                isHidden
                    ? 'Ocultar contraseña'
                    : 'Mostrar contraseña'

            );


            if (isHidden) {

                eyeIcon.innerHTML = `

                    <path d="M3 3l18 18"></path>

                    <path
                        d="M10.6 10.6a2 2 0 0 0 2.8 2.8"
                    ></path>

                    <path
                        d="M9.9 4.2A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a18.2 18.2 0 0 1-2.1 3.2"
                    ></path>

                    <path
                        d="M6.6 6.6C3.8 8.5 2 12 2 12s3.5 8 10 8a9.7 9.7 0 0 0 4-.8"
                    ></path>

                `;

            } else {

                eyeIcon.innerHTML = `

                    <path
                        d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"
                    ></path>

                    <circle
                        cx="12"
                        cy="12"
                        r="3"
                    ></circle>

                `;

            }

        }
    );



    /* =========================================================
       ENVÍO DEL FORMULARIO
    ========================================================= */

    const loginForm =
        document.getElementById('loginForm');

    const loginButton =
        document.getElementById('loginButton');

    const buttonText =
        document.getElementById('buttonText');

    const username =
        document.getElementById('username');


    loginForm.addEventListener(
        'submit',
        function () {

            /*
             * Eliminar espacios antes y después
             * del usuario.
             */

            username.value =
                username.value.trim();


            /*
             * Evitar doble clic.
             */

            loginButton.disabled = true;


            /*
             * Mostrar estado.
             */

            buttonText.textContent =
                'Verificando...';

        }
    );

</script>


</body>

</html>
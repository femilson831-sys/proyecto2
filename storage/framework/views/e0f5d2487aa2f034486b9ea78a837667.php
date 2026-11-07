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
        content="<?php echo e(csrf_token()); ?>"
    >

    <title>
        <?php echo $__env->yieldContent('title', 'Administración'); ?> | NANOS
    </title>

    <style>
        :root {
            --red: #e31b23;
            --red-dark: #b90f16;
            --red-light: #ff4148;

            --black: #0b0c0e;
            --dark: #111216;
            --dark-2: #17181c;
            --dark-3: #1d1e23;
            --dark-4: #25262c;

            --white: #ffffff;
            --text: #f5f5f6;
            --muted: #8f929a;

            --border: rgba(255, 255, 255, .08);

            --green: #38d478;
            --yellow: #f5b942;
            --blue: #4f8cff;

            --sidebar-width: 270px;
            --navbar-height: 76px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
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
        input,
        select,
        textarea {
            font-family: inherit;
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* =====================================================
           LAYOUT
        ====================================================== */

        .admin-wrapper {
            min-height: 100vh;
        }

        .main-wrapper {
            min-height: 100vh;

            margin-left:
                var(--sidebar-width);

            transition:
                margin-left .25s ease;
        }

        .main-content {
            min-height:
                calc(
                    100vh -
                    var(--navbar-height)
                );

            padding: 30px;

            background:
                radial-gradient(
                    circle at 100% 0%,
                    rgba(227, 27, 35, .07),
                    transparent 25%
                ),
                radial-gradient(
                    circle at 0% 100%,
                    rgba(227, 27, 35, .035),
                    transparent 28%
                ),
                #101115;
        }

        /* =====================================================
           SIDEBAR
        ====================================================== */

        .sidebar {
            position: fixed;

            top: 0;
            left: 0;
            bottom: 0;

            z-index: 1000;

            width:
                var(--sidebar-width);

            display: flex;
            flex-direction: column;

            background:
                radial-gradient(
                    circle at 0% 0%,
                    rgba(227, 27, 35, .13),
                    transparent 27%
                ),
                linear-gradient(
                    180deg,
                    #121317,
                    #0d0e11
                );

            border-right:
                1px solid
                var(--border);

            transition:
                transform .25s ease;
        }

        .sidebar-header {
            height:
                var(--navbar-height);

            display: flex;
            align-items: center;

            padding:
                0 22px;

            border-bottom:
                1px solid
                var(--border);
        }

        .brand {
            display: flex;
            align-items: center;

            gap: 13px;
        }

        .brand-logo {
            width: 45px;
            height: 45px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 4px;

            background: white;

            border-radius: 12px;

            box-shadow:
                0 8px 20px
                rgba(0, 0, 0, .25);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;

            object-fit: contain;

            border-radius: 8px;
        }

        .brand-info strong {
            display: block;

            color: white;

            font-size: 19px;
            letter-spacing: .3px;
        }

        .brand-info span {
            display: block;

            margin-top: 2px;

            color: #73767e;

            font-size: 9px;
            font-weight: 700;

            letter-spacing: 1.5px;

            text-transform:
                uppercase;
        }

        .sidebar-scroll {
            flex: 1;

            overflow-y: auto;

            padding:
                20px 14px;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #2c2d32;

            border-radius: 20px;
        }

        .menu-section {
            margin-bottom: 25px;
        }

        .menu-title {
            padding:
                0 13px;

            margin-bottom: 9px;

            color: #565960;

            font-size: 9px;
            font-weight: 800;

            letter-spacing: 1.6px;

            text-transform:
                uppercase;
        }

        .menu-item {
            min-height: 47px;

            position: relative;

            display: flex;
            align-items: center;

            gap: 12px;

            margin-bottom: 5px;

            padding:
                0 14px;

            border-radius: 11px;

            color: #94979f;

            font-size: 13px;
            font-weight: 550;

            transition: .2s;
        }

        .menu-item:hover {
            color: white;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .045
                );
        }

        .menu-item.active {
            color: white;

            background:
                linear-gradient(
                    90deg,
                    rgba(227, 27, 35, .17),
                    rgba(227, 27, 35, .06)
                );
        }

        .menu-item.active::before {
            content: "";

            position: absolute;

            left: 0;

            width: 3px;
            height: 25px;

            border-radius:
                0 5px 5px 0;

            background:
                var(--red);

            box-shadow:
                0 0 12px
                rgba(227, 27, 35, .55);
        }

        .menu-item svg {
            width: 19px;
            height: 19px;

            flex-shrink: 0;
        }

        .menu-badge {
            min-width: 21px;
            height: 21px;

            margin-left: auto;

            display: flex;
            align-items: center;
            justify-content: center;

            padding:
                0 6px;

            border-radius: 50px;

            color: white;

            background:
                var(--red);

            font-size: 10px;
            font-weight: 700;
        }

        .sidebar-footer {
            padding: 15px;

            border-top:
                1px solid
                var(--border);
        }

        .sidebar-user {
            display: flex;
            align-items: center;

            gap: 11px;

            padding: 10px;

            border-radius: 12px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .025
                );
        }

        .sidebar-avatar {
            width: 38px;
            height: 38px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--red-dark)
                );

            font-size: 14px;
            font-weight: 800;
        }

        .sidebar-user-info {
            min-width: 0;

            flex: 1;
        }

        .sidebar-user-info strong {
            display: block;

            overflow: hidden;

            color: #e9e9eb;

            font-size: 12px;

            text-overflow:
                ellipsis;

            white-space: nowrap;
        }

        .sidebar-user-info span {
            display: block;

            margin-top: 3px;

            color: #696c73;

            font-size: 10px;
        }

        /* =====================================================
           NAVBAR
        ====================================================== */

        .navbar {
            position: sticky;

            top: 0;

            z-index: 900;

            height:
                var(--navbar-height);

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;

            padding:
                0 30px;

            background:
                rgba(
                    17,
                    18,
                    22,
                    .93
                );

            backdrop-filter:
                blur(18px);

            border-bottom:
                1px solid
                var(--border);
        }

        .navbar-left {
            display: flex;
            align-items: center;

            gap: 17px;
        }

        .menu-toggle {
            width: 41px;
            height: 41px;

            display: none;
            align-items: center;
            justify-content: center;

            border:
                1px solid
                var(--border);

            border-radius: 11px;

            color: #b8bac0;

            background:
                var(--dark-2);

            cursor: pointer;
        }

        .menu-toggle svg {
            width: 20px;
            height: 20px;
        }

        .navbar-title strong {
            display: block;

            color: #f4f4f5;

            font-size: 15px;
        }

        .navbar-title span {
            display: block;

            margin-top: 3px;

            color: #70737b;

            font-size: 11px;
        }

        .navbar-right {
            display: flex;
            align-items: center;

            gap: 10px;
        }

        .nav-button {
            width: 41px;
            height: 41px;

            position: relative;

            display: flex;
            align-items: center;
            justify-content: center;

            border:
                1px solid
                var(--border);

            border-radius: 11px;

            color: #999ca4;

            background:
                var(--dark-2);

            cursor: pointer;

            transition: .2s;
        }

        .nav-button:hover {
            color: white;

            border-color:
                rgba(
                    227,
                    27,
                    35,
                    .30
                );

            background:
                rgba(
                    227,
                    27,
                    35,
                    .08
                );
        }

        .nav-button svg {
            width: 19px;
            height: 19px;
        }

        .notification-dot {
            position: absolute;

            top: 8px;
            right: 8px;

            width: 7px;
            height: 7px;

            border-radius: 50%;

            background:
                var(--red);

            box-shadow:
                0 0 0 3px
                var(--dark-2);
        }

        .profile {
            display: flex;
            align-items: center;

            gap: 10px;

            margin-left: 5px;
            padding-left: 15px;

            border-left:
                1px solid
                var(--border);
        }

        .profile-avatar {
            width: 39px;
            height: 39px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--red-dark)
                );

            font-size: 13px;
            font-weight: 800;
        }

        .profile-info {
            max-width: 150px;
        }

        .profile-info strong {
            display: block;

            overflow: hidden;

            color: #e8e8ea;

            font-size: 12px;

            text-overflow:
                ellipsis;

            white-space: nowrap;
        }

        .profile-info span {
            display: block;

            margin-top: 2px;

            color: #74777f;

            font-size: 10px;
        }

        .logout-button {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-left: 5px;

            border: none;
            border-radius: 10px;

            color: #92959d;

            background:
                transparent;

            cursor: pointer;

            transition: .2s;
        }

        .logout-button:hover {
            color: #ff5d63;

            background:
                rgba(
                    227,
                    27,
                    35,
                    .09
                );
        }

        .logout-button svg {
            width: 19px;
            height: 19px;
        }

        /* =====================================================
           CABECERA
        ====================================================== */

        .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            gap: 20px;

            margin-bottom: 27px;
        }

        .page-title .eyebrow {
            display: block;

            margin-bottom: 7px;

            color:
                var(--red-light);

            font-size: 10px;
            font-weight: 800;

            letter-spacing: 1.5px;

            text-transform:
                uppercase;
        }

        .page-title h1 {
            color: white;

            font-size: 29px;

            letter-spacing: -.8px;
        }

        .page-title p {
            margin-top: 7px;

            color: #858890;

            font-size: 13px;
            line-height: 1.5;
        }

        .date-box {
            display: flex;
            align-items: center;

            gap: 10px;

            padding:
                11px 14px;

            border:
                1px solid
                var(--border);

            border-radius: 11px;

            color: #989ba2;

            background:
                var(--dark-2);

            font-size: 11px;
        }

        .date-box svg {
            width: 17px;
            height: 17px;

            color:
                var(--red-light);
        }

        /* =====================================================
           DASHBOARD STATS
        ====================================================== */

        .stats-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
                );

            gap: 17px;

            margin-bottom: 22px;
        }

        .stat-card {
            position: relative;

            overflow: hidden;

            padding: 21px;

            border:
                1px solid
                var(--border);

            border-radius: 16px;

            background:
                linear-gradient(
                    145deg,
                    #191a1f,
                    #141519
                );

            transition:
                transform .2s,
                border-color .2s;
        }

        .stat-card:hover {
            transform:
                translateY(-3px);

            border-color:
                rgba(
                    255,
                    255,
                    255,
                    .13
                );
        }

        .stat-card::after {
            content: "";

            position: absolute;

            width: 100px;
            height: 100px;

            top: -50px;
            right: -50px;

            border-radius: 50%;

            background:
                rgba(
                    227,
                    27,
                    35,
                    .05
                );
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 10px;

            margin-bottom: 22px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            color:
                var(--red-light);

            background:
                rgba(
                    227,
                    27,
                    35,
                    .10
                );
        }

        .stat-icon svg {
            width: 20px;
            height: 20px;
        }

        .stat-status {
            padding:
                5px 8px;

            border-radius: 7px;

            color: #6edc99;

            background:
                rgba(
                    56,
                    212,
                    120,
                    .08
                );

            font-size: 9px;
            font-weight: 700;
        }

        .stat-label {
            color: #80838b;

            font-size: 11px;
        }

        .stat-value {
            margin-top: 7px;

            color: white;

            font-size: 25px;
            font-weight: 750;

            letter-spacing: -.7px;
        }

        .stat-description {
            margin-top: 6px;

            color: #5f6269;

            font-size: 10px;
        }

        /* =====================================================
           DASHBOARD PANELES
        ====================================================== */

        .dashboard-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1.5fr)
                minmax(300px, .7fr);

            gap: 20px;
        }

        .panel {
            overflow: hidden;

            border:
                1px solid
                var(--border);

            border-radius: 16px;

            background:
                linear-gradient(
                    145deg,
                    #18191e,
                    #141519
                );
        }

        .panel-header {
            min-height: 64px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;

            padding:
                0 20px;

            border-bottom:
                1px solid
                var(--border);
        }

        .panel-title strong {
            display: block;

            color: #e9e9eb;

            font-size: 13px;
        }

        .panel-title span {
            display: block;

            margin-top: 3px;

            color: #666970;

            font-size: 10px;
        }

        .panel-action {
            border: none;

            color:
                var(--red-light);

            background:
                transparent;

            font-size: 10px;
            font-weight: 700;

            cursor: pointer;
        }

        .activity-list {
            padding:
                7px 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;

            gap: 13px;

            padding:
                14px 0;

            border-bottom:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    .05
                );
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 37px;
            height: 37px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;

            color: #8d9097;

            background: #222329;
        }

        .activity-icon svg {
            width: 17px;
            height: 17px;
        }

        .activity-info {
            min-width: 0;

            flex: 1;
        }

        .activity-info strong {
            display: block;

            overflow: hidden;

            color: #cfd0d4;

            font-size: 11px;

            text-overflow:
                ellipsis;

            white-space: nowrap;
        }

        .activity-info span {
            display: block;

            margin-top: 4px;

            color: #64676e;

            font-size: 9px;
        }

        .activity-time {
            color: #5f6269;

            font-size: 9px;

            white-space: nowrap;
        }

        /* =====================================================
           ALERTAS
        ====================================================== */

        .alert-list {
            padding: 12px;
        }

        .alert-item {
            display: flex;

            gap: 11px;

            padding: 13px;

            margin-bottom: 8px;

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    .05
                );

            border-radius: 11px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .018
                );
        }

        .alert-item:last-child {
            margin-bottom: 0;
        }

        .alert-indicator {
            width: 8px;
            height: 8px;

            flex-shrink: 0;

            margin-top: 4px;

            border-radius: 50%;
        }

        .alert-indicator.red {
            background:
                var(--red-light);

            box-shadow:
                0 0 8px
                rgba(
                    255,
                    65,
                    72,
                    .4
                );
        }

        .alert-indicator.yellow {
            background:
                var(--yellow);
        }

        .alert-indicator.green {
            background:
                var(--green);
        }

        .alert-content strong {
            display: block;

            color: #cacbd0;

            font-size: 10px;
        }

        .alert-content p {
            margin-top: 4px;

            color: #686b72;

            font-size: 9px;
            line-height: 1.5;
        }

        /* =====================================================
           ACCESOS RÁPIDOS
        ====================================================== */

        .quick-section {
            margin-top: 20px;
        }

        .quick-title {
            margin-bottom: 12px;

            color: #74777f;

            font-size: 10px;
            font-weight: 750;

            letter-spacing: 1px;

            text-transform:
                uppercase;
        }

        .quick-grid {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 12px;
        }

        .quick-card {
            display: flex;
            align-items: center;

            gap: 12px;

            padding: 15px;

            border:
                1px solid
                var(--border);

            border-radius: 13px;

            color: #a9abb1;

            background:
                var(--dark-2);

            cursor: pointer;

            transition: .2s;
        }

        .quick-card:hover {
            color: white;

            border-color:
                rgba(
                    227,
                    27,
                    35,
                    .24
                );

            background:
                rgba(
                    227,
                    27,
                    35,
                    .06
                );

            transform:
                translateY(-2px);
        }

        .quick-card-icon {
            width: 35px;
            height: 35px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            color:
                var(--red-light);

            background:
                rgba(
                    227,
                    27,
                    35,
                    .09
                );
        }

        .quick-card-icon svg {
            width: 17px;
            height: 17px;
        }

        .quick-card strong {
            display: block;

            font-size: 10px;
        }

        .quick-card span {
            display: block;

            margin-top: 3px;

            color: #60636a;

            font-size: 8px;
        }

        /* =====================================================
           CRUD - BARRA
        ====================================================== */

        .crud-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 16px;

            margin-bottom: 20px;
        }

        .search-form {
            position: relative;

            flex: 1;

            max-width: 520px;
        }

        .search-form svg {
            position: absolute;

            top: 50%;
            left: 15px;

            width: 18px;
            height: 18px;

            transform:
                translateY(-50%);

            color: #73767e;

            pointer-events: none;
        }

        .search-input {
            width: 100%;
            height: 46px;

            padding:
                0 16px
                0 45px;

            border:
                1px solid
                var(--border);

            border-radius: 12px;

            outline: none;

            color: white;

            background:
                var(--dark-2);

            font-size: 12px;

            transition: .2s;
        }

        .search-input:focus {
            border-color:
                var(--red);

            box-shadow:
                0 0 0 3px
                rgba(
                    227,
                    27,
                    35,
                    .09
                );
        }

        .search-input::placeholder {
            color: #62656c;
        }

        /* =====================================================
           BOTONES CRUD
        ====================================================== */

        .btn-primary {
            min-height: 46px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 9px;

            padding:
                0 18px;

            border: none;

            border-radius: 11px;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--red-dark)
                );

            font-size: 12px;
            font-weight: 700;

            cursor: pointer;

            transition: .2s;
        }

        .btn-primary:hover {
            transform:
                translateY(-2px);

            box-shadow:
                0 10px 25px
                rgba(
                    227,
                    27,
                    35,
                    .22
                );
        }

        .btn-primary svg {
            width: 17px;
            height: 17px;
        }

        .btn-secondary {
            min-height: 44px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 8px;

            padding:
                0 17px;

            border:
                1px solid
                var(--border);

            border-radius: 11px;

            color: #b7b9bf;

            background:
                var(--dark-3);

            font-size: 12px;
            font-weight: 600;

            cursor: pointer;

            transition: .2s;
        }

        .btn-secondary:hover {
            color: white;

            background:
                var(--dark-4);
        }

        /* =====================================================
           MENSAJES
        ====================================================== */

        .success-message {
            margin-bottom: 20px;

            padding:
                14px 16px;

            border:
                1px solid
                rgba(
                    56,
                    212,
                    120,
                    .18
                );

            border-radius: 12px;

            color: #65db92;

            background:
                rgba(
                    56,
                    212,
                    120,
                    .07
                );

            font-size: 12px;
        }

        .error-message {
            margin-bottom: 20px;

            padding:
                14px 16px;

            border:
                1px solid
                rgba(
                    227,
                    27,
                    35,
                    .20
                );

            border-radius: 12px;

            color: #ff777c;

            background:
                rgba(
                    227,
                    27,
                    35,
                    .07
                );

            font-size: 12px;

            line-height: 1.6;
        }

        .error-message ul {
            margin:
                7px 0
                0 18px;
        }

        /* =====================================================
           TABLA ADMIN
        ====================================================== */

        .table-panel {
            overflow: hidden;

            border:
                1px solid
                var(--border);

            border-radius: 16px;

            background:
                linear-gradient(
                    145deg,
                    #191a1f,
                    #141519
                );
        }

        .table-responsive {
            width: 100%;

            overflow-x: auto;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #34363c;

            border-radius: 20px;
        }

        .admin-table {
            width: 100%;

            min-width: 900px;

            border-collapse:
                collapse;
        }

        .admin-table th {
            padding:
                14px 17px;

            text-align: left;

            border-bottom:
                1px solid
                var(--border);

            color: #686b72;

            background: #141519;

            font-size: 9px;
            font-weight: 800;

            letter-spacing: 1px;

            text-transform:
                uppercase;

            white-space: nowrap;
        }

        .admin-table td {
            padding:
                15px 17px;

            border-bottom:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    .045
                );

            color: #aaadb4;

            font-size: 11px;

            vertical-align: middle;
        }

        .admin-table tbody tr {
            transition: .2s;
        }

        .admin-table tbody tr:hover {
            background:
                rgba(
                    255,
                    255,
                    255,
                    .025
                );
        }

        .admin-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* =====================================================
           EMPLEADO
        ====================================================== */

        .employee-cell {
            min-width: 220px;

            display: flex;
            align-items: center;

            gap: 11px;
        }

        .employee-avatar {
            width: 39px;
            height: 39px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;

            border-radius: 10px;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    #8d0b10
                );

            font-size: 12px;
            font-weight: 800;
        }

        .employee-avatar img {
            width: 100%;
            height: 100%;

            object-fit: cover;
        }

        .employee-name {
            min-width: 0;
        }

        .employee-name strong {
            display: block;

            overflow: hidden;

            max-width: 230px;

            color: #e5e5e7;

            font-size: 11px;

            text-overflow:
                ellipsis;

            white-space: nowrap;
        }

        .employee-name span {
            display: block;

            overflow: hidden;

            max-width: 230px;

            margin-top: 3px;

            color: #62656c;

            font-size: 9px;

            text-overflow:
                ellipsis;

            white-space: nowrap;
        }

        /* =====================================================
           ESTADOS
        ====================================================== */

        .status-badge {
            display: inline-flex;
            align-items: center;

            gap: 6px;

            padding:
                6px 9px;

            border-radius: 20px;

            font-size: 9px;
            font-weight: 700;

            white-space: nowrap;
        }

        .status-badge::before {
            content: "";

            width: 6px;
            height: 6px;

            border-radius: 50%;
        }

        .status-active {
            color: #62da90;

            background:
                rgba(
                    56,
                    212,
                    120,
                    .08
                );
        }

        .status-active::before {
            background:
                var(--green);
        }

        .status-inactive {
            color: #ff6e74;

            background:
                rgba(
                    227,
                    27,
                    35,
                    .08
                );
        }

        .status-inactive::before {
            background:
                var(--red);
        }

        /* =====================================================
           ACCIONES CRUD
        ====================================================== */

        .actions {
            display: flex;
            align-items: center;

            gap: 6px;
        }

        .actions form {
            margin: 0;
        }

        .action-btn {
            width: 34px;
            height: 34px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 0;

            border:
                1px solid
                var(--border);

            border-radius: 9px;

            color: #858890;

            background: #202126;

            cursor: pointer;

            transition: .2s;
        }

        .action-btn:hover {
            color: white;

            background: #27282e;
        }

        .action-btn.edit:hover {
            color: #ffbd55;
        }

        .action-btn.delete:hover {
            color: #ff5d63;

            border-color:
                rgba(
                    227,
                    27,
                    35,
                    .20
                );

            background:
                rgba(
                    227,
                    27,
                    35,
                    .07
                );
        }

        .action-btn svg {
            width: 15px;
            height: 15px;
        }

        /* =====================================================
           EMPTY STATE
        ====================================================== */

        .empty-state {
            padding:
                65px 20px;

            text-align: center;
        }

        .empty-state h3 {
            color: #d7d7da;

            font-size: 15px;
        }

        .empty-state p {
            margin-top: 7px;

            color: #666970;

            font-size: 11px;
        }

        /* =====================================================
           FORMULARIOS
        ====================================================== */

        .form-panel-admin {
            padding: 25px;

            border:
                1px solid
                var(--border);

            border-radius: 16px;

            background:
                linear-gradient(
                    145deg,
                    #191a1f,
                    #141519
                );
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-title {
            margin-bottom: 18px;

            padding-bottom: 12px;

            border-bottom:
                1px solid
                var(--border);
        }

        .form-section-title strong {
            display: block;

            color: #e7e7e9;

            font-size: 13px;
        }

        .form-section-title span {
            display: block;

            margin-top: 4px;

            color: #666970;

            font-size: 10px;
        }

        .form-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(0, 1fr)
                );

            gap: 18px;
        }

        .form-group {
            min-width: 0;
        }

        .form-group.full {
            grid-column:
                1 / -1;
        }

        .form-group label {
            display: block;

            margin-bottom: 7px;

            color: #b4b6bc;

            font-size: 11px;
            font-weight: 650;
        }

        .form-group label .required {
            color:
                var(--red-light);
        }

        .form-control {
            width: 100%;
            height: 47px;

            padding:
                0 13px;

            border:
                1px solid
                var(--border);

            border-radius: 10px;

            outline: none;

            color: #eeeef0;

            background: #202126;

            font-size: 12px;

            transition: .2s;
        }

        .form-control:focus {
            border-color:
                var(--red);

            box-shadow:
                0 0 0 3px
                rgba(
                    227,
                    27,
                    35,
                    .08
                );
        }

        .form-control::placeholder {
            color: #5d6067;
        }

        textarea.form-control {
            min-height: 105px;

            padding: 13px;

            resize: vertical;

            line-height: 1.6;
        }

        select.form-control {
            cursor: pointer;
        }

        select.form-control option {
            color: white;

            background: #202126;
        }

        input[type="date"].form-control {
            color-scheme: dark;
        }

        .field-error {
            display: block;

            margin-top: 6px;

            color: #ff686e;

            font-size: 9px;
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;

            gap: 10px;

            margin-top: 25px;

            padding-top: 20px;

            border-top:
                1px solid
                var(--border);
        }

        /* =====================================================
           DETALLE
        ====================================================== */

        .detail-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );

            gap: 15px;
        }

        .detail-item {
            min-width: 0;

            padding: 15px;

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    .05
                );

            border-radius: 11px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .018
                );
        }

        .detail-item span {
            display: block;

            color: #62656c;

            font-size: 9px;
            font-weight: 700;

            letter-spacing: .7px;

            text-transform:
                uppercase;
        }

        .detail-item strong {
            display: block;

            margin-top: 6px;

            color: #d8d9dc;

            font-size: 11px;
            font-weight: 600;

            line-height: 1.5;

            overflow-wrap: anywhere;
        }

        /* =====================================================
           OVERLAY
        ====================================================== */

        .sidebar-overlay {
            position: fixed;

            inset: 0;

            z-index: 950;

            display: none;

            background:
                rgba(
                    0,
                    0,
                    0,
                    .65
                );

            backdrop-filter:
                blur(3px);
        }

        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1200px) {

            .stats-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .quick-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

        @media (max-width: 980px) {

            .sidebar {
                transform:
                    translateX(-100%);
            }

            .sidebar.open {
                transform:
                    translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .menu-toggle {
                display: flex;
            }

            .dashboard-grid {
                grid-template-columns:
                    1fr;
            }

            .detail-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

        @media (max-width: 800px) {

            .crud-toolbar {
                align-items: stretch;

                flex-direction: column;
            }

            .search-form {
                max-width: none;
            }

            .crud-toolbar .btn-primary {
                width: 100%;
            }

            .form-grid {
                grid-template-columns:
                    1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .form-actions {
                flex-direction:
                    column-reverse;
            }

            .form-actions a,
            .form-actions button {
                width: 100%;
            }

        }

        @media (max-width: 650px) {

            .navbar {
                padding:
                    0 15px;
            }

            .navbar-title {
                display: none;
            }

            .profile-info {
                display: none;
            }

            .profile {
                padding-left: 10px;
            }

            .main-content {
                padding:
                    20px 15px;
            }

            .page-header {
                align-items:
                    flex-start;

                flex-direction:
                    column;
            }

            .page-title h1 {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns:
                    1fr;
            }

            .quick-grid {
                grid-template-columns:
                    1fr;
            }

            .date-box {
                width: 100%;
            }

            .form-panel-admin {
                padding: 17px;
            }

            .detail-grid {
                grid-template-columns:
                    1fr;
            }

        }

    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>

</head>


<body>

    <div class="admin-wrapper">

        
        <?php echo $__env->make('admin.components.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        
        <div
            class="sidebar-overlay"
            id="sidebarOverlay"
        ></div>


        <div class="main-wrapper">

            
            <?php echo $__env->make('admin.components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


            
            <main class="main-content">

                <?php echo $__env->yieldContent('content'); ?>

            </main>

        </div>

    </div>


    <script>
        const sidebar =
            document.getElementById(
                'sidebar'
            );

        const sidebarOverlay =
            document.getElementById(
                'sidebarOverlay'
            );

        const menuToggle =
            document.getElementById(
                'menuToggle'
            );


        function openSidebar() {

            if (
                !sidebar ||
                !sidebarOverlay
            ) {
                return;
            }

            sidebar.classList.add(
                'open'
            );

            sidebarOverlay.classList.add(
                'show'
            );

            document.body.style.overflow =
                'hidden';
        }


        function closeSidebar() {

            if (
                !sidebar ||
                !sidebarOverlay
            ) {
                return;
            }

            sidebar.classList.remove(
                'open'
            );

            sidebarOverlay.classList.remove(
                'show'
            );

            document.body.style.overflow =
                '';
        }


        if (menuToggle) {

            menuToggle.addEventListener(
                'click',
                function () {

                    if (
                        sidebar &&
                        sidebar.classList.contains(
                            'open'
                        )
                    ) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }

                }
            );

        }


        if (sidebarOverlay) {

            sidebarOverlay.addEventListener(
                'click',
                closeSidebar
            );

        }


        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key ===
                    'Escape'
                ) {
                    closeSidebar();
                }

            }
        );


        window.addEventListener(
            'resize',
            function () {

                if (
                    window.innerWidth >
                    980
                ) {
                    closeSidebar();
                }

            }
        );
    </script>


    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html><?php /**PATH C:\xampp\htdocs\nanosminisuper20262.1\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>
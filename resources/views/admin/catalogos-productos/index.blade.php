@extends('admin.layouts.app')

@section('title', 'Catálogos de productos')

@push('styles')
<style>
    /* ============================================================
       NANOS - CATÁLOGOS DE PRODUCTOS
    ============================================================ */

    .catalog-page {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    /* ============================================================
       CABECERA
    ============================================================ */

    .catalog-hero {
        position: relative;
        overflow: hidden;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        padding: 22px;

        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: 16px;

        background:
            radial-gradient(
                circle at 90% 10%,
                rgba(227, 27, 35, .14),
                transparent 32%
            ),
            linear-gradient(
                135deg,
                rgba(255, 255, 255, .025),
                rgba(255, 255, 255, .008)
            );
    }

    .catalog-hero::before {
        content: '';

        position: absolute;
        top: 0;
        left: 0;

        width: 4px;
        height: 100%;

        background: var(--red);
    }

    .catalog-hero-info {
        min-width: 0;
    }

    .catalog-eyebrow {
        display: flex;
        align-items: center;
        gap: 7px;

        margin-bottom: 6px;

        color: var(--red-light);

        font-size: 8px;
        font-weight: 800;
        letter-spacing: 1.2px;

        text-transform: uppercase;
    }

    .catalog-eyebrow::before {
        content: '';

        width: 18px;
        height: 2px;

        border-radius: 10px;

        background: var(--red);
    }

    .catalog-hero h1 {
        margin: 0;

        color: #f2f2f3;

        font-size: 21px;
        font-weight: 800;
        letter-spacing: -.4px;
    }

    .catalog-hero p {
        max-width: 700px;

        margin: 7px 0 0;

        color: #777a82;

        font-size: 9px;
        line-height: 1.6;
    }

    .catalog-hero-actions {
        display: flex;
        align-items: center;
        gap: 8px;

        flex-shrink: 0;
    }

    .catalog-products-btn {
        min-height: 35px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        padding: 0 13px;

        border: 1px solid rgba(255,255,255,.09);
        border-radius: 9px;

        background: rgba(255,255,255,.025);

        color: #b7b9be;

        font-size: 8px;
        font-weight: 700;

        text-decoration: none;

        transition: .2s;
    }

    .catalog-products-btn svg {
        width: 14px;
        height: 14px;
    }

    .catalog-products-btn:hover {
        border-color: rgba(227,27,35,.3);

        background: rgba(227,27,35,.07);

        color: #fff;

        transform: translateY(-1px);
    }


    /* ============================================================
       MENSAJES
    ============================================================ */

    .catalog-message {
        display: flex;
        align-items: flex-start;
        gap: 10px;

        padding: 12px 14px;

        border-radius: 10px;

        font-size: 8px;
        line-height: 1.6;
    }

    .catalog-message svg {
        width: 16px;
        height: 16px;

        flex-shrink: 0;

        margin-top: 1px;
    }

    .catalog-message.success {
        border: 1px solid rgba(52, 211, 153, .18);

        background: rgba(52, 211, 153, .06);

        color: #7adba9;
    }

    .catalog-message.error {
        border: 1px solid rgba(239, 68, 68, .20);

        background: rgba(239, 68, 68, .06);

        color: #ef8589;
    }

    .catalog-message ul {
        margin: 5px 0 0 16px;
        padding: 0;
    }


    /* ============================================================
       RESUMEN
    ============================================================ */

    .catalog-summary {
        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 12px;
    }

    .summary-card {
        position: relative;
        overflow: hidden;

        display: flex;
        align-items: center;
        gap: 12px;

        padding: 14px;

        border: 1px solid rgba(255,255,255,.06);
        border-radius: 12px;

        background: rgba(255,255,255,.012);
    }

    .summary-card::after {
        content: '';

        position: absolute;
        right: -20px;
        bottom: -25px;

        width: 70px;
        height: 70px;

        border-radius: 50%;

        background: rgba(227,27,35,.035);
    }

    .summary-icon {
        width: 38px;
        height: 38px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 10px;

        background: rgba(227,27,35,.08);

        color: var(--red-light);
    }

    .summary-icon svg {
        width: 18px;
        height: 18px;
    }

    .summary-info strong {
        display: block;

        color: #e4e4e6;

        font-size: 16px;
        font-weight: 800;
    }

    .summary-info span {
        display: block;

        margin-top: 2px;

        color: #686b72;

        font-size: 7px;
        font-weight: 700;

        letter-spacing: .5px;

        text-transform: uppercase;
    }


    /* ============================================================
       GRID PRINCIPAL
    ============================================================ */

    .catalog-grid {
        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 14px;

        align-items: start;
    }


    /* ============================================================
       TARJETAS
    ============================================================ */

    .catalog-card {
        overflow: hidden;

        border: 1px solid rgba(255,255,255,.065);
        border-radius: 14px;

        background: rgba(255,255,255,.01);
    }

    .catalog-card-header {
        padding: 15px;

        border-bottom: 1px solid rgba(255,255,255,.055);

        background:
            linear-gradient(
                135deg,
                rgba(255,255,255,.025),
                transparent
            );
    }

    .catalog-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .catalog-card-heading {
        display: flex;
        align-items: center;
        gap: 10px;

        min-width: 0;
    }

    .catalog-card-icon {
        width: 35px;
        height: 35px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 9px;

        background: rgba(227,27,35,.08);

        color: var(--red-light);
    }

    .catalog-card-icon svg {
        width: 16px;
        height: 16px;
    }

    .catalog-card-title {
        min-width: 0;
    }

    .catalog-card-title h2 {
        margin: 0;

        color: #dedfe2;

        font-size: 11px;
        font-weight: 800;
    }

    .catalog-card-title p {
        margin: 3px 0 0;

        color: #666970;

        font-size: 7px;
    }

    .btn-new-catalog {
        min-height: 31px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;

        padding: 0 10px;

        flex-shrink: 0;

        border: 0;
        border-radius: 8px;

        background:
            linear-gradient(
                135deg,
                var(--red),
                var(--red-dark)
            );

        color: #fff;

        font-size: 7px;
        font-weight: 800;

        cursor: pointer;

        box-shadow:
            0 5px 15px rgba(227,27,35,.14);

        transition: .2s;
    }

    .btn-new-catalog svg {
        width: 12px;
        height: 12px;
    }

    .btn-new-catalog:hover {
        transform: translateY(-1px);

        box-shadow:
            0 8px 20px rgba(227,27,35,.23);
    }


    /* ============================================================
       FILTROS
    ============================================================ */

    .catalog-filter {
        padding: 10px 12px;

        border-bottom: 1px solid rgba(255,255,255,.05);

        background: rgba(0,0,0,.08);
    }

    .catalog-filter-form {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .catalog-search-wrapper {
        position: relative;

        flex: 1;
        min-width: 0;
    }

    .catalog-search-wrapper svg {
        position: absolute;
        top: 50%;
        left: 10px;

        width: 12px;
        height: 12px;

        color: #55585f;

        pointer-events: none;

        transform: translateY(-50%);
    }

    .catalog-search {
        width: 100%;
        min-height: 32px;

        padding: 0 10px 0 29px;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 8px;

        outline: none;

        background: rgba(255,255,255,.018);

        color: #c8c9cd;

        font-size: 8px;

        transition: .2s;
    }

    .catalog-search::placeholder {
        color: #53565d;
    }

    .catalog-search:focus {
        border-color: rgba(227,27,35,.35);

        background: rgba(227,27,35,.025);
    }

    .catalog-filter-select {
        max-width: 115px;
        min-height: 32px;

        padding: 0 7px;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 8px;

        outline: none;

        background: #191a1f;

        color: #8c8f96;

        font-size: 7px;
    }

    .btn-filter {
        width: 32px;
        height: 32px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 8px;

        background: rgba(255,255,255,.02);

        color: #777a81;

        cursor: pointer;

        transition: .2s;
    }

    .btn-filter svg {
        width: 13px;
        height: 13px;
    }

    .btn-filter:hover {
        border-color: rgba(227,27,35,.3);

        background: rgba(227,27,35,.07);

        color: #fff;
    }

    .btn-clear-filter {
        width: 32px;
        height: 32px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 8px;

        background: transparent;

        color: #5e6168;

        text-decoration: none;

        transition: .2s;
    }

    .btn-clear-filter svg {
        width: 12px;
        height: 12px;
    }

    .btn-clear-filter:hover {
        color: #fff;

        background: rgba(255,255,255,.04);
    }


    /* ============================================================
       LISTA
    ============================================================ */

    .catalog-list {
        max-height: 560px;

        overflow-y: auto;
        overflow-x: hidden;
    }

    .catalog-list::-webkit-scrollbar {
        width: 5px;
    }

    .catalog-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .catalog-list::-webkit-scrollbar-thumb {
        border-radius: 10px;

        background: rgba(255,255,255,.08);
    }

    .catalog-item {
        padding: 12px 13px;

        border-bottom:
            1px solid rgba(255,255,255,.04);

        transition: .18s;
    }

    .catalog-item:last-child {
        border-bottom: 0;
    }

    .catalog-item:hover {
        background: rgba(255,255,255,.014);
    }

    .catalog-item-main {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 9px;
    }

    .catalog-item-info {
        flex: 1;
        min-width: 0;
    }

    .catalog-item-name {
        display: block;

        overflow: hidden;

        color: #d0d1d4;

        font-size: 9px;
        font-weight: 800;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .catalog-item-description {
        display: -webkit-box;

        margin-top: 4px;

        overflow: hidden;

        color: #65686f;

        font-size: 7px;
        line-height: 1.45;

        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .catalog-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;

        margin-top: 7px;
    }

    .catalog-category-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;

        padding: 3px 6px;

        border-radius: 20px;

        background: rgba(92, 143, 255, .07);

        color: #7d9fdc;

        font-size: 6.5px;
        font-weight: 700;
    }

    .catalog-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;

        padding: 3px 6px;

        border-radius: 20px;

        font-size: 6.5px;
        font-weight: 800;
    }

    .catalog-status::before {
        content: '';

        width: 5px;
        height: 5px;

        border-radius: 50%;
    }

    .catalog-status.active {
        background: rgba(52,211,153,.06);

        color: #6fdba5;
    }

    .catalog-status.active::before {
        background: #34d399;

        box-shadow:
            0 0 6px rgba(52,211,153,.4);
    }

    .catalog-status.inactive {
        background: rgba(239,68,68,.06);

        color: #eb7a7e;
    }

    .catalog-status.inactive::before {
        background: #ef4444;
    }


    /* ============================================================
       ACCIONES
    ============================================================ */

    .catalog-actions {
        display: flex;
        align-items: center;
        gap: 4px;

        flex-shrink: 0;
    }

    .catalog-actions form {
        margin: 0;
    }

    .catalog-action-btn {
        width: 29px;
        height: 29px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 0;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 7px;

        background: rgba(255,255,255,.015);

        color: #6f7279;

        cursor: pointer;

        transition: .18s;
    }

    .catalog-action-btn svg {
        width: 12px;
        height: 12px;
    }

    .catalog-action-btn.edit:hover {
        border-color: rgba(59,130,246,.32);

        background: rgba(59,130,246,.08);

        color: #6da7f1;

        transform: translateY(-1px);
    }

    .catalog-action-btn.disable:hover {
        border-color: rgba(245,158,11,.30);

        background: rgba(245,158,11,.07);

        color: #e9ad4a;

        transform: translateY(-1px);
    }

    .catalog-action-btn.enable:hover {
        border-color: rgba(52,211,153,.30);

        background: rgba(52,211,153,.07);

        color: #69dca5;

        transform: translateY(-1px);
    }

    .catalog-action-btn.delete:hover {
        border-color: rgba(239,68,68,.40);

        background: rgba(239,68,68,.10);

        color: #ff666b;

        transform: translateY(-1px);

        box-shadow:
            0 5px 15px rgba(239,68,68,.10);
    }


    /* ============================================================
       VACÍO
    ============================================================ */

    .catalog-empty {
        padding: 42px 15px;

        text-align: center;
    }

    .catalog-empty-icon {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 10px;

        border-radius: 12px;

        background: rgba(255,255,255,.025);

        color: #55585f;
    }

    .catalog-empty-icon svg {
        width: 20px;
        height: 20px;
    }

    .catalog-empty strong {
        display: block;

        color: #888b92;

        font-size: 8px;
    }

    .catalog-empty span {
        display: block;

        margin-top: 4px;

        color: #55585f;

        font-size: 7px;
    }


    /* ============================================================
       MODALES GENERALES
    ============================================================ */

    .catalog-modal {
        position: fixed;

        inset: 0;

        z-index: 9999;

        display: none;

        align-items: center;
        justify-content: center;

        padding: 20px;

        background: rgba(0,0,0,.76);

        backdrop-filter: blur(7px);
    }

    .catalog-modal.show {
        display: flex;

        animation:
            catalogFade .18s ease-out;
    }

    @keyframes catalogFade {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .catalog-modal-box {
        width: 100%;
        max-width: 475px;

        overflow: hidden;

        border: 1px solid rgba(255,255,255,.08);
        border-radius: 15px;

        background: #18191e;

        box-shadow:
            0 30px 100px rgba(0,0,0,.60);

        animation:
            catalogModalIn .20s ease-out;
    }

    @keyframes catalogModalIn {
        from {
            opacity: 0;

            transform:
                translateY(10px)
                scale(.985);
        }

        to {
            opacity: 1;

            transform:
                translateY(0)
                scale(1);
        }
    }

    .catalog-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        padding: 16px 18px;

        border-bottom:
            1px solid rgba(255,255,255,.06);
    }

    .catalog-modal-heading {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .catalog-modal-icon {
        width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 9px;

        background: rgba(227,27,35,.08);

        color: var(--red-light);
    }

    .catalog-modal-icon svg {
        width: 15px;
        height: 15px;
    }

    .catalog-modal-title span {
        display: block;

        margin-bottom: 2px;

        color: var(--red-light);

        font-size: 6.5px;
        font-weight: 800;

        letter-spacing: .9px;

        text-transform: uppercase;
    }

    .catalog-modal-title h3 {
        margin: 0;

        color: #e0e1e3;

        font-size: 11px;
        font-weight: 800;
    }

    .catalog-modal-close {
        width: 29px;
        height: 29px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border: 1px solid transparent;
        border-radius: 7px;

        background: transparent;

        color: #686b72;

        font-size: 17px;

        cursor: pointer;

        transition: .2s;
    }

    .catalog-modal-close:hover {
        border-color: rgba(255,255,255,.07);

        background: rgba(255,255,255,.03);

        color: #fff;
    }

    .catalog-modal-body {
        padding: 18px;
    }

    .catalog-form-group {
        margin-bottom: 14px;
    }

    .catalog-form-group:last-child {
        margin-bottom: 0;
    }

    .catalog-form-group label {
        display: block;

        margin-bottom: 6px;

        color: #8e9198;

        font-size: 7px;
        font-weight: 800;

        letter-spacing: .25px;
    }

    .catalog-required {
        color: var(--red-light);
    }

    .catalog-control {
        width: 100%;

        padding: 9px 10px;

        border: 1px solid rgba(255,255,255,.075);
        border-radius: 8px;

        outline: none;

        background: #1c1d22;

        color: #c9cace;

        font-size: 8px;

        transition: .2s;
    }

    .catalog-control:focus {
        border-color: rgba(227,27,35,.38);

        background: #1e1f24;

        box-shadow:
            0 0 0 3px rgba(227,27,35,.035);
    }

    textarea.catalog-control {
        min-height: 82px;

        resize: vertical;
    }

    select.catalog-control {
        cursor: pointer;
    }

    .catalog-field-help {
        display: block;

        margin-top: 5px;

        color: #55585f;

        font-size: 6.5px;
        line-height: 1.45;
    }

    .catalog-modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;

        padding: 13px 18px;

        border-top:
            1px solid rgba(255,255,255,.06);

        background: rgba(0,0,0,.08);
    }

    .catalog-btn-cancel {
        min-height: 32px;

        padding: 0 12px;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 8px;

        background: transparent;

        color: #777a81;

        font-size: 7px;
        font-weight: 800;

        cursor: pointer;

        transition: .2s;
    }

    .catalog-btn-cancel:hover {
        background: rgba(255,255,255,.03);

        color: #fff;
    }

    .catalog-btn-save {
        min-height: 32px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;

        padding: 0 13px;

        border: 0;
        border-radius: 8px;

        background:
            linear-gradient(
                135deg,
                var(--red),
                var(--red-dark)
            );

        color: #fff;

        font-size: 7px;
        font-weight: 800;

        cursor: pointer;

        transition: .2s;
    }

    .catalog-btn-save svg {
        width: 12px;
        height: 12px;
    }

    .catalog-btn-save:hover {
        transform: translateY(-1px);

        box-shadow:
            0 6px 18px rgba(227,27,35,.20);
    }


    /* ============================================================
       MODAL ELIMINAR
    ============================================================ */

    .delete-modal-box {
        max-width: 420px;
    }

    .delete-warning {
        width: 56px;
        height: 56px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 2px auto 14px;

        border: 1px solid rgba(239,68,68,.18);
        border-radius: 16px;

        background: rgba(239,68,68,.07);

        color: #f16c71;
    }

    .delete-warning svg {
        width: 25px;
        height: 25px;
    }

    .delete-content {
        text-align: center;
    }

    .delete-content h3 {
        margin: 0;

        color: #e5e5e7;

        font-size: 13px;
        font-weight: 800;
    }

    .delete-content p {
        margin: 8px auto 0;

        max-width: 330px;

        color: #74777e;

        font-size: 8px;
        line-height: 1.6;
    }

    .delete-record-name {
        display: inline-block;

        max-width: 100%;

        margin-top: 12px;

        padding: 6px 10px;

        overflow: hidden;

        border: 1px solid rgba(239,68,68,.14);
        border-radius: 7px;

        background: rgba(239,68,68,.04);

        color: #e57b7f;

        font-size: 8px;
        font-weight: 800;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .delete-notice {
        margin-top: 13px;

        padding: 9px 10px;

        border-radius: 8px;

        background: rgba(255,255,255,.02);

        color: #5f6269;

        font-size: 7px;
        line-height: 1.5;
    }

    .catalog-btn-delete {
        min-height: 32px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;

        padding: 0 13px;

        border: 0;
        border-radius: 8px;

        background: #d92b32;

        color: #fff;

        font-size: 7px;
        font-weight: 800;

        cursor: pointer;

        transition: .2s;
    }

    .catalog-btn-delete svg {
        width: 12px;
        height: 12px;
    }

    .catalog-btn-delete:hover {
        background: #ef353d;

        transform: translateY(-1px);

        box-shadow:
            0 7px 20px rgba(239,68,68,.20);
    }


    /* ============================================================
       RESPONSIVE
    ============================================================ */

    @media (max-width: 1150px) {

        .catalog-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .catalog-grid .catalog-card:last-child {
            grid-column:
                span 2;
        }

    }

    @media (max-width: 760px) {

        .catalog-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .catalog-hero-actions {
            width: 100%;
        }

        .catalog-products-btn {
            width: 100%;
        }

        .catalog-summary {
            grid-template-columns: 1fr;
        }

        .catalog-grid {
            grid-template-columns: 1fr;
        }

        .catalog-grid .catalog-card:last-child {
            grid-column: auto;
        }

    }

    @media (max-width: 520px) {

        .catalog-hero {
            padding: 17px;
        }

        .catalog-card-top {
            align-items: flex-start;
        }

        .catalog-filter-form {
            flex-wrap: wrap;
        }

        .catalog-search-wrapper {
            flex-basis: 100%;
        }

        .catalog-filter-select {
            max-width: none;

            flex: 1;
        }

        .catalog-item-main {
            flex-direction: column;
        }

        .catalog-actions {
            width: 100%;

            justify-content: flex-end;

            padding-top: 4px;
        }

        .catalog-action-btn {
            width: 32px;
            height: 32px;
        }

        .catalog-modal {
            padding: 12px;
        }

    }
</style>
@endpush


@section('content')

<div class="catalog-page">

    {{-- ============================================================
         CABECERA
    ============================================================ --}}

    <section class="catalog-hero">

        <div class="catalog-hero-info">

            <div class="catalog-eyebrow">
                Inventario · Configuración
            </div>

            <h1>
                Catálogos de productos
            </h1>

            <p>
                Administra las categorías, subcategorías y marcas
                que se utilizarán para clasificar los productos
                del Minisúper NANOS.
            </p>

        </div>


        <div class="catalog-hero-actions">

            <a
                href="{{ route('admin.productos.index') }}"
                class="catalog-products-btn"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"
                    />

                    <path d="m3.3 7 8.7 5 8.7-5"/>
                    <path d="M12 22V12"/>
                </svg>

                Ver productos
            </a>

        </div>

    </section>


    {{-- ============================================================
         MENSAJE DE ÉXITO
    ============================================================ --}}

    @if(session('success'))

        <div class="catalog-message success">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <circle cx="12" cy="12" r="9"/>
                <path d="m8 12 3 3 5-6"/>
            </svg>

            <div>
                {{ session('success') }}
            </div>

        </div>

    @endif


    {{-- ============================================================
         MENSAJE DE ERROR
    ============================================================ --}}

    @if(session('error'))

        <div class="catalog-message error">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 8v5"/>
                <path d="M12 16h.01"/>
            </svg>

            <div>
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- ============================================================
         ERRORES DE VALIDACIÓN
    ============================================================ --}}

    @if($errors->any())

        <div class="catalog-message error">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 8v5"/>
                <path d="M12 16h.01"/>
            </svg>

            <div>

                <strong>
                    Revisa los siguientes datos:
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- ============================================================
         RESUMEN
    ============================================================ --}}

    <div class="catalog-summary">

        {{-- CATEGORÍAS --}}

        <div class="summary-card">

            <div class="summary-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M4 6h16"/>
                    <path d="M4 12h16"/>
                    <path d="M4 18h16"/>
                </svg>

            </div>

            <div class="summary-info">

                <strong>
                    {{ $categorias->count() }}
                </strong>

                <span>
                    Categorías mostradas
                </span>

            </div>

        </div>


        {{-- SUBCATEGORÍAS --}}

        <div class="summary-card">

            <div class="summary-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <rect
                        x="3"
                        y="4"
                        width="18"
                        height="5"
                        rx="1"
                    />

                    <rect
                        x="3"
                        y="15"
                        width="18"
                        height="5"
                        rx="1"
                    />

                    <path d="M7 9v6"/>
                    <path d="M17 9v6"/>
                </svg>

            </div>

            <div class="summary-info">

                <strong>
                    {{ $subcategorias->count() }}
                </strong>

                <span>
                    Subcategorías mostradas
                </span>

            </div>

        </div>


        {{-- MARCAS --}}

        <div class="summary-card">

            <div class="summary-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        d="M20 12 12 20 4 12V4h8Z"
                    />

                    <circle
                        cx="9"
                        cy="9"
                        r="1"
                    />
                </svg>

            </div>

            <div class="summary-info">

                <strong>
                    {{ $marcas->count() }}
                </strong>

                <span>
                    Marcas mostradas
                </span>

            </div>

        </div>

    </div>


    {{-- ============================================================
         GRID DE CATÁLOGOS
    ============================================================ --}}

    <div class="catalog-grid">


        {{-- ========================================================
             CATEGORÍAS
        ======================================================== --}}

        <section class="catalog-card">

            <div class="catalog-card-header">

                <div class="catalog-card-top">

                    <div class="catalog-card-heading">

                        <div class="catalog-card-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 6h16"/>
                                <path d="M4 12h16"/>
                                <path d="M4 18h16"/>
                            </svg>

                        </div>


                        <div class="catalog-card-title">

                            <h2>
                                Categorías
                            </h2>

                            <p>
                                Clasificación principal
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn-new-catalog"
                        onclick="nuevaCategoria()"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 5v14"/>
                            <path d="M5 12h14"/>
                        </svg>

                        Nueva
                    </button>

                </div>

            </div>


            {{-- BUSCADOR CATEGORÍA --}}

            <div class="catalog-filter">

                <form
                    method="GET"
                    action="{{ route(
                        'admin.catalogos-productos.index'
                    ) }}"
                    class="catalog-filter-form"
                >

                    {{-- Mantener filtros de otras tarjetas --}}

                    @if(request('buscar_subcategoria'))

                        <input
                            type="hidden"
                            name="buscar_subcategoria"
                            value="{{ request(
                                'buscar_subcategoria'
                            ) }}"
                        >

                    @endif

                    @if(request('categoria_id'))

                        <input
                            type="hidden"
                            name="categoria_id"
                            value="{{ request(
                                'categoria_id'
                            ) }}"
                        >

                    @endif

                    @if(request('buscar_marca'))

                        <input
                            type="hidden"
                            name="buscar_marca"
                            value="{{ request(
                                'buscar_marca'
                            ) }}"
                        >

                    @endif


                    <div class="catalog-search-wrapper">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path d="m20 20-4-4"/>
                        </svg>

                        <input
                            type="text"
                            name="buscar_categoria"
                            class="catalog-search"
                            placeholder="Buscar categoría..."
                            value="{{ request(
                                'buscar_categoria'
                            ) }}"
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn-filter"
                        title="Buscar"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path d="m20 20-4-4"/>
                        </svg>
                    </button>


                    @if(request('buscar_categoria'))

                        <a
                            href="{{ route(
                                'admin.catalogos-productos.index',
                                array_filter([
                                    'buscar_subcategoria'
                                        => request(
                                            'buscar_subcategoria'
                                        ),

                                    'categoria_id'
                                        => request(
                                            'categoria_id'
                                        ),

                                    'buscar_marca'
                                        => request(
                                            'buscar_marca'
                                        ),
                                ])
                            ) }}"
                            class="btn-clear-filter"
                            title="Limpiar búsqueda"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="m6 6 12 12"/>
                                <path d="m18 6-12 12"/>
                            </svg>
                        </a>

                    @endif

                </form>

            </div>


            {{-- LISTADO CATEGORÍAS --}}

            <div class="catalog-list">

                @forelse($categorias as $categoria)

                    <article class="catalog-item">

                        <div class="catalog-item-main">

                            <div class="catalog-item-info">

                                <strong class="catalog-item-name">
                                    {{ $categoria->nombre }}
                                </strong>

                                <span class="catalog-item-description">
                                    {{
                                        $categoria->descripcion
                                        ?: 'Sin descripción registrada.'
                                    }}
                                </span>


                                <div class="catalog-meta">

                                    <span
                                        class="catalog-status {{
                                            $categoria->estado
                                                ? 'active'
                                                : 'inactive'
                                        }}"
                                    >
                                        {{
                                            $categoria->estado
                                                ? 'Activa'
                                                : 'Inactiva'
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="catalog-actions">

                                {{-- EDITAR --}}

                                <button
                                    type="button"
                                    class="catalog-action-btn edit"
                                    title="Editar categoría"
                                    data-id="{{ $categoria->id }}"
                                    data-nombre="{{
                                        $categoria->nombre
                                    }}"
                                    data-descripcion="{{
                                        $categoria->descripcion
                                    }}"
                                    onclick="
                                        editarCategoria(this)
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 20h9"/>

                                        <path
                                            d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"
                                        />
                                    </svg>
                                </button>


                                {{-- ACTIVAR / DESACTIVAR --}}

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.catalogos-productos.categorias.estado',
                                        $categoria
                                    ) }}"
                                    class="state-form"
                                    data-nombre="{{
                                        $categoria->nombre
                                    }}"
                                    data-accion="{{
                                        $categoria->estado
                                            ? 'desactivar'
                                            : 'activar'
                                    }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="catalog-action-btn {{
                                            $categoria->estado
                                                ? 'disable'
                                                : 'enable'
                                        }}"
                                        title="{{
                                            $categoria->estado
                                                ? 'Desactivar categoría'
                                                : 'Activar categoría'
                                        }}"
                                    >

                                        @if($categoria->estado)

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
                                                    d="M12 8v4"
                                                />

                                                <path
                                                    d="M12 16h.01"
                                                />
                                            </svg>

                                        @else

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
                                                    d="m8 12 3 3 5-6"
                                                />
                                            </svg>

                                        @endif

                                    </button>

                                </form>


                                {{-- ELIMINAR --}}

                                <button
                                    type="button"
                                    class="catalog-action-btn delete"
                                    title="Eliminar categoría"
                                    data-url="{{ route(
                                        'admin.catalogos-productos.categorias.destroy',
                                        $categoria
                                    ) }}"
                                    data-nombre="{{
                                        $categoria->nombre
                                    }}"
                                    data-tipo="categoría"
                                    onclick="
                                        prepararEliminacion(this)
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v5"/>
                                        <path d="M14 11v5"/>
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </article>

                @empty

                    <div class="catalog-empty">

                        <div class="catalog-empty-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 6h16"/>
                                <path d="M4 12h16"/>
                                <path d="M4 18h16"/>
                            </svg>

                        </div>

                        <strong>
                            No se encontraron categorías
                        </strong>

                        <span>
                            Registra una nueva categoría.
                        </span>

                    </div>

                @endforelse

            </div>

        </section>



        {{-- ========================================================
             SUBCATEGORÍAS
        ======================================================== --}}

        <section class="catalog-card">

            <div class="catalog-card-header">

                <div class="catalog-card-top">

                    <div class="catalog-card-heading">

                        <div class="catalog-card-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="5"
                                    rx="1"
                                />

                                <rect
                                    x="3"
                                    y="15"
                                    width="18"
                                    height="5"
                                    rx="1"
                                />

                                <path d="M7 9v6"/>
                                <path d="M17 9v6"/>
                            </svg>

                        </div>


                        <div class="catalog-card-title">

                            <h2>
                                Subcategorías
                            </h2>

                            <p>
                                Clasificación secundaria
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn-new-catalog"
                        onclick="nuevaSubcategoria()"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 5v14"/>
                            <path d="M5 12h14"/>
                        </svg>

                        Nueva
                    </button>

                </div>

            </div>


            {{-- FILTRO SUBCATEGORÍAS --}}

            <div class="catalog-filter">

                <form
                    method="GET"
                    action="{{ route(
                        'admin.catalogos-productos.index'
                    ) }}"
                    class="catalog-filter-form"
                >

                    @if(request('buscar_categoria'))

                        <input
                            type="hidden"
                            name="buscar_categoria"
                            value="{{ request(
                                'buscar_categoria'
                            ) }}"
                        >

                    @endif

                    @if(request('buscar_marca'))

                        <input
                            type="hidden"
                            name="buscar_marca"
                            value="{{ request(
                                'buscar_marca'
                            ) }}"
                        >

                    @endif


                    <div class="catalog-search-wrapper">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path d="m20 20-4-4"/>
                        </svg>

                        <input
                            type="text"
                            name="buscar_subcategoria"
                            class="catalog-search"
                            placeholder="Buscar..."
                            value="{{ request(
                                'buscar_subcategoria'
                            ) }}"
                        >

                    </div>


                    <select
                        name="categoria_id"
                        class="catalog-filter-select"
                    >
                        <option value="">
                            Todas
                        </option>

                        @foreach(
                            $categoriasSelector
                            as $categoriaSelector
                        )

                            <option
                                value="{{
                                    $categoriaSelector->id
                                }}"
                                @selected(
                                    (string)
                                    request('categoria_id')
                                    ===
                                    (string)
                                    $categoriaSelector->id
                                )
                            >
                                {{
                                    $categoriaSelector->nombre
                                }}
                            </option>

                        @endforeach
                    </select>


                    <button
                        type="submit"
                        class="btn-filter"
                        title="Filtrar"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path d="m20 20-4-4"/>
                        </svg>
                    </button>


                    @if(
                        request('buscar_subcategoria')
                        || request('categoria_id')
                    )

                        <a
                            href="{{ route(
                                'admin.catalogos-productos.index',
                                array_filter([
                                    'buscar_categoria'
                                        => request(
                                            'buscar_categoria'
                                        ),

                                    'buscar_marca'
                                        => request(
                                            'buscar_marca'
                                        ),
                                ])
                            ) }}"
                            class="btn-clear-filter"
                            title="Limpiar filtros"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="m6 6 12 12"/>
                                <path d="m18 6-12 12"/>
                            </svg>
                        </a>

                    @endif

                </form>

            </div>


            {{-- LISTADO SUBCATEGORÍAS --}}

            <div class="catalog-list">

                @forelse($subcategorias as $subcategoria)

                    <article class="catalog-item">

                        <div class="catalog-item-main">

                            <div class="catalog-item-info">

                                <strong class="catalog-item-name">
                                    {{ $subcategoria->nombre }}
                                </strong>

                                <span class="catalog-item-description">
                                    {{
                                        $subcategoria->descripcion
                                        ?: 'Sin descripción registrada.'
                                    }}
                                </span>


                                <div class="catalog-meta">

                                    <span class="catalog-category-tag">

                                        {{
                                            optional(
                                                $subcategoria->categoria
                                            )->nombre
                                            ?? 'Sin categoría'
                                        }}

                                    </span>


                                    <span
                                        class="catalog-status {{
                                            $subcategoria->estado
                                                ? 'active'
                                                : 'inactive'
                                        }}"
                                    >
                                        {{
                                            $subcategoria->estado
                                                ? 'Activa'
                                                : 'Inactiva'
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="catalog-actions">

                                {{-- EDITAR --}}

                                <button
                                    type="button"
                                    class="catalog-action-btn edit"
                                    title="Editar subcategoría"
                                    data-id="{{
                                        $subcategoria->id
                                    }}"
                                    data-categoria="{{
                                        $subcategoria->categoria_id
                                    }}"
                                    data-nombre="{{
                                        $subcategoria->nombre
                                    }}"
                                    data-descripcion="{{
                                        $subcategoria->descripcion
                                    }}"
                                    onclick="
                                        editarSubcategoria(this)
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 20h9"/>

                                        <path
                                            d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"
                                        />
                                    </svg>
                                </button>


                                {{-- ACTIVAR / DESACTIVAR --}}

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.catalogos-productos.subcategorias.estado',
                                        $subcategoria
                                    ) }}"
                                    class="state-form"
                                    data-nombre="{{
                                        $subcategoria->nombre
                                    }}"
                                    data-accion="{{
                                        $subcategoria->estado
                                            ? 'desactivar'
                                            : 'activar'
                                    }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="catalog-action-btn {{
                                            $subcategoria->estado
                                                ? 'disable'
                                                : 'enable'
                                        }}"
                                        title="{{
                                            $subcategoria->estado
                                                ? 'Desactivar subcategoría'
                                                : 'Activar subcategoría'
                                        }}"
                                    >

                                        @if($subcategoria->estado)

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
                                                    d="M12 8v4"
                                                />

                                                <path
                                                    d="M12 16h.01"
                                                />
                                            </svg>

                                        @else

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
                                                    d="m8 12 3 3 5-6"
                                                />
                                            </svg>

                                        @endif

                                    </button>

                                </form>


                                {{-- ELIMINAR --}}

                                <button
                                    type="button"
                                    class="catalog-action-btn delete"
                                    title="Eliminar subcategoría"
                                    data-url="{{ route(
                                        'admin.catalogos-productos.subcategorias.destroy',
                                        $subcategoria
                                    ) }}"
                                    data-nombre="{{
                                        $subcategoria->nombre
                                    }}"
                                    data-tipo="subcategoría"
                                    onclick="
                                        prepararEliminacion(this)
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v5"/>
                                        <path d="M14 11v5"/>
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </article>

                @empty

                    <div class="catalog-empty">

                        <div class="catalog-empty-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="5"
                                    rx="1"
                                />

                                <rect
                                    x="3"
                                    y="15"
                                    width="18"
                                    height="5"
                                    rx="1"
                                />
                            </svg>

                        </div>

                        <strong>
                            No se encontraron subcategorías
                        </strong>

                        <span>
                            Registra una nueva subcategoría.
                        </span>

                    </div>

                @endforelse

            </div>

        </section>



        {{-- ========================================================
             MARCAS
        ======================================================== --}}

        <section class="catalog-card">

            <div class="catalog-card-header">

                <div class="catalog-card-top">

                    <div class="catalog-card-heading">

                        <div class="catalog-card-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M20 12 12 20 4 12V4h8Z"
                                />

                                <circle
                                    cx="9"
                                    cy="9"
                                    r="1"
                                />
                            </svg>

                        </div>


                        <div class="catalog-card-title">

                            <h2>
                                Marcas
                            </h2>

                            <p>
                                Marcas comerciales
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn-new-catalog"
                        onclick="nuevaMarca()"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 5v14"/>
                            <path d="M5 12h14"/>
                        </svg>

                        Nueva
                    </button>

                </div>

            </div>


            {{-- BUSCADOR MARCAS --}}

            <div class="catalog-filter">

                <form
                    method="GET"
                    action="{{ route(
                        'admin.catalogos-productos.index'
                    ) }}"
                    class="catalog-filter-form"
                >

                    @if(request('buscar_categoria'))

                        <input
                            type="hidden"
                            name="buscar_categoria"
                            value="{{ request(
                                'buscar_categoria'
                            ) }}"
                        >

                    @endif

                    @if(request('buscar_subcategoria'))

                        <input
                            type="hidden"
                            name="buscar_subcategoria"
                            value="{{ request(
                                'buscar_subcategoria'
                            ) }}"
                        >

                    @endif

                    @if(request('categoria_id'))

                        <input
                            type="hidden"
                            name="categoria_id"
                            value="{{ request(
                                'categoria_id'
                            ) }}"
                        >

                    @endif


                    <div class="catalog-search-wrapper">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path d="m20 20-4-4"/>
                        </svg>

                        <input
                            type="text"
                            name="buscar_marca"
                            class="catalog-search"
                            placeholder="Buscar marca..."
                            value="{{ request(
                                'buscar_marca'
                            ) }}"
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn-filter"
                        title="Buscar"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path d="m20 20-4-4"/>
                        </svg>
                    </button>


                    @if(request('buscar_marca'))

                        <a
                            href="{{ route(
                                'admin.catalogos-productos.index',
                                array_filter([
                                    'buscar_categoria'
                                        => request(
                                            'buscar_categoria'
                                        ),

                                    'buscar_subcategoria'
                                        => request(
                                            'buscar_subcategoria'
                                        ),

                                    'categoria_id'
                                        => request(
                                            'categoria_id'
                                        ),
                                ])
                            ) }}"
                            class="btn-clear-filter"
                            title="Limpiar búsqueda"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="m6 6 12 12"/>
                                <path d="m18 6-12 12"/>
                            </svg>
                        </a>

                    @endif

                </form>

            </div>


            {{-- LISTADO MARCAS --}}

            <div class="catalog-list">

                @forelse($marcas as $marca)

                    <article class="catalog-item">

                        <div class="catalog-item-main">

                            <div class="catalog-item-info">

                                <strong class="catalog-item-name">
                                    {{ $marca->nombre }}
                                </strong>


                                <span class="catalog-item-description">
                                    Marca comercial registrada
                                    en el catálogo de NANOS.
                                </span>


                                <div class="catalog-meta">

                                    <span
                                        class="catalog-status {{
                                            $marca->estado
                                                ? 'active'
                                                : 'inactive'
                                        }}"
                                    >
                                        {{
                                            $marca->estado
                                                ? 'Activa'
                                                : 'Inactiva'
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="catalog-actions">

                                {{-- EDITAR --}}

                                <button
                                    type="button"
                                    class="catalog-action-btn edit"
                                    title="Editar marca"
                                    data-id="{{ $marca->id }}"
                                    data-nombre="{{
                                        $marca->nombre
                                    }}"
                                    onclick="
                                        editarMarca(this)
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 20h9"/>

                                        <path
                                            d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"
                                        />
                                    </svg>
                                </button>


                                {{-- ACTIVAR / DESACTIVAR --}}

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.catalogos-productos.marcas.estado',
                                        $marca
                                    ) }}"
                                    class="state-form"
                                    data-nombre="{{
                                        $marca->nombre
                                    }}"
                                    data-accion="{{
                                        $marca->estado
                                            ? 'desactivar'
                                            : 'activar'
                                    }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="catalog-action-btn {{
                                            $marca->estado
                                                ? 'disable'
                                                : 'enable'
                                        }}"
                                        title="{{
                                            $marca->estado
                                                ? 'Desactivar marca'
                                                : 'Activar marca'
                                        }}"
                                    >

                                        @if($marca->estado)

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
                                                    d="M12 8v4"
                                                />

                                                <path
                                                    d="M12 16h.01"
                                                />
                                            </svg>

                                        @else

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
                                                    d="m8 12 3 3 5-6"
                                                />
                                            </svg>

                                        @endif

                                    </button>

                                </form>


                                {{-- ELIMINAR --}}

                                <button
                                    type="button"
                                    class="catalog-action-btn delete"
                                    title="Eliminar marca"
                                    data-url="{{ route(
                                        'admin.catalogos-productos.marcas.destroy',
                                        $marca
                                    ) }}"
                                    data-nombre="{{
                                        $marca->nombre
                                    }}"
                                    data-tipo="marca"
                                    onclick="
                                        prepararEliminacion(this)
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v5"/>
                                        <path d="M14 11v5"/>
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </article>

                @empty

                    <div class="catalog-empty">

                        <div class="catalog-empty-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M20 12 12 20 4 12V4h8Z"
                                />
                            </svg>

                        </div>

                        <strong>
                            No se encontraron marcas
                        </strong>

                        <span>
                            Registra una nueva marca.
                        </span>

                    </div>

                @endforelse

            </div>

        </section>

    </div>

</div>



{{-- ================================================================
     MODAL CATEGORÍA
================================================================ --}}

<div
    class="catalog-modal"
    id="modalCategoria"
>

    <div class="catalog-modal-box">

        <form
            method="POST"
            id="formCategoria"
            action="{{ route(
                'admin.catalogos-productos.categorias.store'
            ) }}"
        >
            @csrf

            <div id="metodoCategoria"></div>


            <div class="catalog-modal-header">

                <div class="catalog-modal-heading">

                    <div class="catalog-modal-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 6h16"/>
                            <path d="M4 12h16"/>
                            <path d="M4 18h16"/>
                        </svg>

                    </div>


                    <div class="catalog-modal-title">

                        <span>
                            Categorías
                        </span>

                        <h3 id="tituloCategoria">
                            Nueva categoría
                        </h3>

                    </div>

                </div>


                <button
                    type="button"
                    class="catalog-modal-close"
                    onclick="
                        cerrarCatalogModal(
                            'modalCategoria'
                        )
                    "
                >
                    ×
                </button>

            </div>


            <div class="catalog-modal-body">

                <div class="catalog-form-group">

                    <label for="categoriaNombre">
                        Nombre
                        <span class="catalog-required">
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="categoriaNombre"
                        class="catalog-control"
                        maxlength="100"
                        autocomplete="off"
                        required
                    >

                    <span class="catalog-field-help">
                        Ejemplo: Bebidas, Abarrotes,
                        Limpieza, Lácteos.
                    </span>

                </div>


                <div class="catalog-form-group">

                    <label for="categoriaDescripcion">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        id="categoriaDescripcion"
                        class="catalog-control"
                        maxlength="255"
                        rows="3"
                    ></textarea>

                    <span class="catalog-field-help">
                        Describe brevemente qué productos
                        pertenecen a esta categoría.
                    </span>

                </div>

            </div>


            <div class="catalog-modal-footer">

                <button
                    type="button"
                    class="catalog-btn-cancel"
                    onclick="
                        cerrarCatalogModal(
                            'modalCategoria'
                        )
                    "
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="catalog-btn-save"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>

                    Guardar
                </button>

            </div>

        </form>

    </div>

</div>



{{-- ================================================================
     MODAL SUBCATEGORÍA
================================================================ --}}

<div
    class="catalog-modal"
    id="modalSubcategoria"
>

    <div class="catalog-modal-box">

        <form
            method="POST"
            id="formSubcategoria"
            action="{{ route(
                'admin.catalogos-productos.subcategorias.store'
            ) }}"
        >
            @csrf

            <div id="metodoSubcategoria"></div>


            <div class="catalog-modal-header">

                <div class="catalog-modal-heading">

                    <div class="catalog-modal-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="5"
                                rx="1"
                            />

                            <rect
                                x="3"
                                y="15"
                                width="18"
                                height="5"
                                rx="1"
                            />
                        </svg>

                    </div>


                    <div class="catalog-modal-title">

                        <span>
                            Subcategorías
                        </span>

                        <h3 id="tituloSubcategoria">
                            Nueva subcategoría
                        </h3>

                    </div>

                </div>


                <button
                    type="button"
                    class="catalog-modal-close"
                    onclick="
                        cerrarCatalogModal(
                            'modalSubcategoria'
                        )
                    "
                >
                    ×
                </button>

            </div>


            <div class="catalog-modal-body">

                <div class="catalog-form-group">

                    <label for="subcategoriaCategoria">
                        Categoría
                        <span class="catalog-required">
                            *
                        </span>
                    </label>

                    <select
                        name="categoria_id"
                        id="subcategoriaCategoria"
                        class="catalog-control"
                        required
                    >
                        <option value="">
                            Seleccionar categoría
                        </option>

                        @foreach(
                            $categoriasSelector
                            as $categoriaSelector
                        )

                            <option
                                value="{{
                                    $categoriaSelector->id
                                }}"
                            >
                                {{
                                    $categoriaSelector->nombre
                                }}
                            </option>

                        @endforeach

                    </select>

                    <span class="catalog-field-help">
                        Selecciona la categoría principal
                        a la que pertenece.
                    </span>

                </div>


                <div class="catalog-form-group">

                    <label for="subcategoriaNombre">
                        Nombre
                        <span class="catalog-required">
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="subcategoriaNombre"
                        class="catalog-control"
                        maxlength="100"
                        autocomplete="off"
                        required
                    >

                    <span class="catalog-field-help">
                        Ejemplo: Gaseosas, Jugos,
                        Arroz, Fideos.
                    </span>

                </div>


                <div class="catalog-form-group">

                    <label for="subcategoriaDescripcion">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        id="subcategoriaDescripcion"
                        class="catalog-control"
                        maxlength="255"
                        rows="3"
                    ></textarea>

                </div>

            </div>


            <div class="catalog-modal-footer">

                <button
                    type="button"
                    class="catalog-btn-cancel"
                    onclick="
                        cerrarCatalogModal(
                            'modalSubcategoria'
                        )
                    "
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="catalog-btn-save"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>

                    Guardar
                </button>

            </div>

        </form>

    </div>

</div>



{{-- ================================================================
     MODAL MARCA
================================================================ --}}

<div
    class="catalog-modal"
    id="modalMarca"
>

    <div class="catalog-modal-box">

        <form
            method="POST"
            id="formMarca"
            action="{{ route(
                'admin.catalogos-productos.marcas.store'
            ) }}"
        >
            @csrf

            <div id="metodoMarca"></div>


            <div class="catalog-modal-header">

                <div class="catalog-modal-heading">

                    <div class="catalog-modal-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                d="M20 12 12 20 4 12V4h8Z"
                            />

                            <circle
                                cx="9"
                                cy="9"
                                r="1"
                            />
                        </svg>

                    </div>


                    <div class="catalog-modal-title">

                        <span>
                            Marcas
                        </span>

                        <h3 id="tituloMarca">
                            Nueva marca
                        </h3>

                    </div>

                </div>


                <button
                    type="button"
                    class="catalog-modal-close"
                    onclick="
                        cerrarCatalogModal(
                            'modalMarca'
                        )
                    "
                >
                    ×
                </button>

            </div>


            <div class="catalog-modal-body">

                <div class="catalog-form-group">

                    <label for="marcaNombre">
                        Nombre
                        <span class="catalog-required">
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="marcaNombre"
                        class="catalog-control"
                        maxlength="100"
                        autocomplete="off"
                        required
                    >

                    <span class="catalog-field-help">
                        Ejemplo: Coca-Cola, PIL,
                        Fino, Delizia.
                    </span>

                </div>

            </div>


            <div class="catalog-modal-footer">

                <button
                    type="button"
                    class="catalog-btn-cancel"
                    onclick="
                        cerrarCatalogModal(
                            'modalMarca'
                        )
                    "
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="catalog-btn-save"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>

                    Guardar
                </button>

            </div>

        </form>

    </div>

</div>



{{-- ================================================================
     MODAL CONFIRMACIÓN ELIMINAR
================================================================ --}}

<div
    class="catalog-modal"
    id="modalEliminar"
>

    <div class="catalog-modal-box delete-modal-box">

        <form
            method="POST"
            id="formEliminar"
            action=""
        >
            @csrf
            @method('DELETE')


            <div class="catalog-modal-body">

                <div class="delete-warning">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 6h18"/>
                        <path d="M8 6V4h8v2"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                        <path d="M10 11v5"/>
                        <path d="M14 11v5"/>
                    </svg>

                </div>


                <div class="delete-content">

                    <h3>
                        ¿Eliminar registro?
                    </h3>

                    <p id="textoEliminar">
                        Esta acción eliminará el registro
                        seleccionado.
                    </p>


                    <div
                        class="delete-record-name"
                        id="nombreEliminar"
                    >
                        Registro
                    </div>


                    <div class="delete-notice">
                        Si este registro está siendo utilizado
                        por algún producto o por otro catálogo,
                        el sistema impedirá su eliminación para
                        proteger la información.
                    </div>

                </div>

            </div>


            <div class="catalog-modal-footer">

                <button
                    type="button"
                    class="catalog-btn-cancel"
                    onclick="
                        cerrarCatalogModal(
                            'modalEliminar'
                        )
                    "
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="catalog-btn-delete"
                    id="btnConfirmarEliminar"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 6h18"/>
                        <path d="M8 6V4h8v2"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                    </svg>

                    Sí, eliminar
                </button>

            </div>

        </form>

    </div>

</div>

@endsection



@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        ============================================================
        URLs
        ============================================================
        */

        window.catalogUrls = {

            categoriaStore:
                @json(
                    route(
                        'admin.catalogos-productos.categorias.store'
                    )
                ),

            categoriaBase:
                @json(
                    url(
                        '/admin/catalogos-productos/categorias'
                    )
                ),

            subcategoriaStore:
                @json(
                    route(
                        'admin.catalogos-productos.subcategorias.store'
                    )
                ),

            subcategoriaBase:
                @json(
                    url(
                        '/admin/catalogos-productos/subcategorias'
                    )
                ),

            marcaStore:
                @json(
                    route(
                        'admin.catalogos-productos.marcas.store'
                    )
                ),

            marcaBase:
                @json(
                    url(
                        '/admin/catalogos-productos/marcas'
                    )
                )
        };


        /*
        ============================================================
        CONFIRMACIÓN ACTIVAR / DESACTIVAR
        ============================================================
        */

        document
            .querySelectorAll('.state-form')
            .forEach(function (form) {

                form.addEventListener(
                    'submit',
                    function (event) {

                        const nombre =
                            this.dataset.nombre || '';

                        const accion =
                            this.dataset.accion || '';

                        const mensaje =
                            '¿Deseas ' +
                            accion +
                            ' "' +
                            nombre +
                            '"?';

                        if (!confirm(mensaje)) {
                            event.preventDefault();
                        }

                    }
                );

            });


        /*
        ============================================================
        CERRAR MODAL AL HACER CLIC FUERA
        ============================================================
        */

        document
            .querySelectorAll('.catalog-modal')
            .forEach(function (modal) {

                modal.addEventListener(
                    'click',
                    function (event) {

                        if (event.target === modal) {

                            cerrarCatalogModal(
                                modal.id
                            );

                        }

                    }
                );

            });

    }
);


/*
============================================================
ABRIR MODAL
============================================================
*/

function abrirCatalogModal(id)
{
    const modal =
        document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.add('show');

    document.body.style.overflow =
        'hidden';
}


/*
============================================================
CERRAR MODAL
============================================================
*/

function cerrarCatalogModal(id)
{
    const modal =
        document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.remove('show');

    const abiertos =
        document.querySelectorAll(
            '.catalog-modal.show'
        );

    if (abiertos.length === 0) {
        document.body.style.overflow = '';
    }
}


/*
============================================================
AGREGAR MÉTODO PUT
============================================================
*/

function ponerMetodo(
    contenedorId,
    metodo
) {
    const contenedor =
        document.getElementById(
            contenedorId
        );

    if (!contenedor) {
        return;
    }

    contenedor.innerHTML =
        '<input type="hidden" ' +
        'name="_method" ' +
        'value="' +
        metodo +
        '">';
}


/*
============================================================
NUEVA CATEGORÍA
============================================================
*/

function nuevaCategoria()
{
    const form =
        document.getElementById(
            'formCategoria'
        );

    form.action =
        window.catalogUrls
            .categoriaStore;

    document.getElementById(
        'metodoCategoria'
    ).innerHTML = '';

    document.getElementById(
        'tituloCategoria'
    ).textContent =
        'Nueva categoría';

    document.getElementById(
        'categoriaNombre'
    ).value = '';

    document.getElementById(
        'categoriaDescripcion'
    ).value = '';

    abrirCatalogModal(
        'modalCategoria'
    );

    setTimeout(function () {

        document.getElementById(
            'categoriaNombre'
        ).focus();

    }, 100);
}


/*
============================================================
EDITAR CATEGORÍA
============================================================
*/

function editarCategoria(boton)
{
    const id =
        boton.dataset.id;

    const nombre =
        boton.dataset.nombre || '';

    const descripcion =
        boton.dataset.descripcion || '';

    const form =
        document.getElementById(
            'formCategoria'
        );

    form.action =
        window.catalogUrls
            .categoriaBase
        + '/'
        + id;

    ponerMetodo(
        'metodoCategoria',
        'PUT'
    );

    document.getElementById(
        'tituloCategoria'
    ).textContent =
        'Editar categoría';

    document.getElementById(
        'categoriaNombre'
    ).value =
        nombre;

    document.getElementById(
        'categoriaDescripcion'
    ).value =
        descripcion;

    abrirCatalogModal(
        'modalCategoria'
    );

    setTimeout(function () {

        document.getElementById(
            'categoriaNombre'
        ).focus();

    }, 100);
}


/*
============================================================
NUEVA SUBCATEGORÍA
============================================================
*/

function nuevaSubcategoria()
{
    const form =
        document.getElementById(
            'formSubcategoria'
        );

    form.action =
        window.catalogUrls
            .subcategoriaStore;

    document.getElementById(
        'metodoSubcategoria'
    ).innerHTML = '';

    document.getElementById(
        'tituloSubcategoria'
    ).textContent =
        'Nueva subcategoría';

    document.getElementById(
        'subcategoriaCategoria'
    ).value = '';

    document.getElementById(
        'subcategoriaNombre'
    ).value = '';

    document.getElementById(
        'subcategoriaDescripcion'
    ).value = '';

    abrirCatalogModal(
        'modalSubcategoria'
    );
}


/*
============================================================
EDITAR SUBCATEGORÍA
============================================================
*/

function editarSubcategoria(boton)
{
    const id =
        boton.dataset.id;

    const categoria =
        boton.dataset.categoria || '';

    const nombre =
        boton.dataset.nombre || '';

    const descripcion =
        boton.dataset.descripcion || '';

    const form =
        document.getElementById(
            'formSubcategoria'
        );

    form.action =
        window.catalogUrls
            .subcategoriaBase
        + '/'
        + id;

    ponerMetodo(
        'metodoSubcategoria',
        'PUT'
    );

    document.getElementById(
        'tituloSubcategoria'
    ).textContent =
        'Editar subcategoría';

    document.getElementById(
        'subcategoriaCategoria'
    ).value =
        categoria;

    document.getElementById(
        'subcategoriaNombre'
    ).value =
        nombre;

    document.getElementById(
        'subcategoriaDescripcion'
    ).value =
        descripcion;

    abrirCatalogModal(
        'modalSubcategoria'
    );
}


/*
============================================================
NUEVA MARCA
============================================================
*/

function nuevaMarca()
{
    const form =
        document.getElementById(
            'formMarca'
        );

    form.action =
        window.catalogUrls
            .marcaStore;

    document.getElementById(
        'metodoMarca'
    ).innerHTML = '';

    document.getElementById(
        'tituloMarca'
    ).textContent =
        'Nueva marca';

    document.getElementById(
        'marcaNombre'
    ).value = '';

    abrirCatalogModal(
        'modalMarca'
    );

    setTimeout(function () {

        document.getElementById(
            'marcaNombre'
        ).focus();

    }, 100);
}


/*
============================================================
EDITAR MARCA
============================================================
*/

function editarMarca(boton)
{
    const id =
        boton.dataset.id;

    const nombre =
        boton.dataset.nombre || '';

    const form =
        document.getElementById(
            'formMarca'
        );

    form.action =
        window.catalogUrls
            .marcaBase
        + '/'
        + id;

    ponerMetodo(
        'metodoMarca',
        'PUT'
    );

    document.getElementById(
        'tituloMarca'
    ).textContent =
        'Editar marca';

    document.getElementById(
        'marcaNombre'
    ).value =
        nombre;

    abrirCatalogModal(
        'modalMarca'
    );

    setTimeout(function () {

        document.getElementById(
            'marcaNombre'
        ).focus();

    }, 100);
}


/*
============================================================
PREPARAR ELIMINACIÓN
============================================================
*/

function prepararEliminacion(boton)
{
    const url =
        boton.dataset.url;

    const nombre =
        boton.dataset.nombre || 'Registro';

    const tipo =
        boton.dataset.tipo || 'registro';

    const form =
        document.getElementById(
            'formEliminar'
        );

    form.action =
        url;

    document.getElementById(
        'nombreEliminar'
    ).textContent =
        nombre;

    document.getElementById(
        'textoEliminar'
    ).textContent =
        'Estás a punto de eliminar la ' +
        tipo +
        ' seleccionada. Esta acción no ' +
        'se puede deshacer.';

    abrirCatalogModal(
        'modalEliminar'
    );
}


/*
============================================================
EVITAR DOBLE ENVÍO AL ELIMINAR
============================================================
*/

document
    .getElementById('formEliminar')
    ?.addEventListener(
        'submit',
        function () {

            const boton =
                document.getElementById(
                    'btnConfirmarEliminar'
                );

            if (!boton) {
                return;
            }

            boton.disabled = true;

            boton.innerHTML =
                'Eliminando...';
        }
    );


/*
============================================================
CERRAR CON ESC
============================================================
*/

document.addEventListener(
    'keydown',
    function (event) {

        if (event.key !== 'Escape') {
            return;
        }

        document
            .querySelectorAll(
                '.catalog-modal.show'
            )
            .forEach(function (modal) {

                cerrarCatalogModal(
                    modal.id
                );

            });

    }
);
</script>
@endpush
<style>
    .ta-page {
        --ta-bg: #17181c;
        --ta-soft: #1e2027;
        --ta-border: #30333d;
        --ta-muted: #9b9eaa;
        display: grid;
        gap: 20px;
        color: #eceef4;
    }

    .ta-page *,
    .ta-page *::before,
    .ta-page *::after {
        box-sizing: border-box;
    }

    .ta-header,
    .ta-card {
        border: 1px solid var(--ta-border);
        border-radius: 16px;
        background: var(--ta-bg);
    }

    .ta-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding: 24px;
        border-top: 3px solid #e31b23;
        background:
            radial-gradient(circle at right top, #e31b2312, transparent 60%),
            var(--ta-bg);
    }

    .ta-eyebrow {
        color: #ff858b;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.4px;
        text-transform: uppercase;
    }

    .ta-header h1 {
        margin: 8px 0;
        font-size: clamp(22px, 3vw, 30px);
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .ta-header p,
    .ta-help {
        margin: 0;
        color: var(--ta-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .ta-card {
        padding: 24px;
    }

    .ta-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 20px;
        font-size: 16px;
    }

    .ta-section-title b {
        display: grid;
        place-items: center;
        width: 29px;
        height: 29px;
        border: 1px solid #e31b2340;
        border-radius: 9px;
        color: #ff858b;
        background: #e31b2312;
        font-size: 12px;
    }

    .ta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .ta-full {
        grid-column: 1 / -1;
    }

    .ta-field {
        display: grid;
        align-content: start;
        gap: 7px;
        min-width: 0;
        color: #dce0ea;
        font-size: 12px;
        font-weight: 600;
    }

    .ta-field input,
    .ta-field select,
    .ta-field textarea {
        width: 100%;
        min-height: 44px;
        padding: 11px 12px;
        border: 1px solid #393d49;
        border-radius: 9px;
        color: #edf0f8;
        background: #20222a;
        font: inherit;
        font-weight: 400;
        outline: none;
        color-scheme: dark;
    }

    .ta-field textarea {
        min-height: 110px;
        resize: vertical;
    }

    .ta-field input:focus,
    .ta-field select:focus,
    .ta-field textarea:focus {
        border-color: #f05a62;
        box-shadow: 0 0 0 3px #e31b2318;
    }

    .ta-help {
        font-weight: 400;
    }

    .ta-checks {
        display: flex;
        gap: 12px 20px;
        flex-wrap: wrap;
    }

    .ta-check {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #dce0ea;
    }

    .ta-check input {
        accent-color: #e31b23;
    }

    .ta-note,
    .ta-success,
    .ta-error {
        padding: 14px 16px;
        border: 1px solid #3a4156;
        border-radius: 11px;
        background: #202535;
        color: #c4d0f4;
        font-size: 12px;
        line-height: 1.7;
    }

    .ta-error {
        border-color: #e31b2355;
        background: #e31b2310;
        color: #ffa4aa;
    }

    .ta-success {
        border-color: #39c67b55;
        background: #39c67b10;
        color: #a0e9bf;
    }

    .ta-error ul {
        margin: 6px 0 0;
        padding-left: 20px;
    }

    .ta-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ta-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 42px;
        padding: 10px 16px;
        border: 1px solid #3b3f4c;
        border-radius: 9px;
        color: #e9ebf3;
        background: #252832;
        text-decoration: none;
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .ta-button:hover {
        filter: brightness(1.12);
    }

    .ta-button.primary {
        border-color: #e31b23;
        background: #cd1b24;
        color: #fff;
    }

    .ta-button.danger {
        border-color: #e31b2340;
        background: #e31b2310;
        color: #ff9299;
    }

    .ta-items {
        display: grid;
        gap: 14px;
        margin-bottom: 16px;
    }

    .ta-item {
        display: grid;
        gap: 14px;
        padding: 18px;
        border: 1px solid var(--ta-border);
        border-radius: 12px;
        background: var(--ta-soft);
    }

    .ta-item-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .ta-item-head strong {
        font-size: 13px;
    }

    .ta-stock {
        padding: 10px 12px;
        border-radius: 8px;
        background: #15171d;
        color: #afc2e8;
        font-size: 12px;
    }

    .ta-table-wrap {
        overflow-x: auto;
    }

    .ta-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 750px;
    }

    .ta-table th,
    .ta-table td {
        padding: 13px 12px;
        border-bottom: 1px solid var(--ta-border);
        text-align: left;
        vertical-align: top;
        font-size: 12px;
    }

    .ta-table th {
        color: var(--ta-muted);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .ta-table small {
        display: block;
        margin-top: 5px;
        color: var(--ta-muted);
    }

    .ta-badge {
        display: inline-block;
        padding: 5px 9px;
        border: 1px solid #424859;
        border-radius: 99px;
        font-size: 11px;
        white-space: nowrap;
    }

    .ta-description {
        white-space: pre-wrap;
        line-height: 1.8;
        color: #c9cedb;
        font-size: 13px;
    }

    .ta-page [hidden] {
        display: none !important;
    }

    @media (max-width: 680px) {
        .ta-card,
        .ta-header {
            padding: 18px;
        }

        .ta-grid {
            grid-template-columns: 1fr;
        }

        .ta-full {
            grid-column: auto;
        }
    }
</style>
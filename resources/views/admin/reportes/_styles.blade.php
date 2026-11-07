<style>
    .rep-page {
        --rep-bg: #17181c;
        --rep-soft: #202127;
        --rep-border: #343741;
        --rep-muted: #a2a7b3;
        display: grid;
        gap: 20px;
        color: #eef0f5;
    }

    .rep-page * {
        box-sizing: border-box;
    }

    .rep-header,
    .rep-card,
    .rep-stat {
        border: 1px solid var(--rep-border);
        border-radius: 15px;
        background: var(--rep-bg);
    }

    .rep-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 18px;
        padding: 24px;
        background:
            radial-gradient(circle at right top, #e31b2318, transparent 65%),
            var(--rep-bg);
    }

    .rep-eyebrow {
        color: #ff858d;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .rep-header h1 {
        margin: 8px 0;
        font-size: 27px;
    }

    .rep-help {
        margin: 0;
        color: var(--rep-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .rep-card {
        padding: 22px;
        min-width: 0;
    }

    .rep-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .rep-module {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
    }

    .rep-module h2 {
        margin: 0;
        font-size: 18px;
    }

    .rep-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border: 1px solid #e31b2344;
        border-radius: 12px;
        color: #ff8a92;
        background: #e31b2310;
    }

    .rep-icon svg {
        width: 24px;
        height: 24px;
    }

    .rep-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 15px;
        border: 1px solid #444854;
        border-radius: 9px;
        color: #fff;
        background: #272930;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .rep-button.primary {
        border-color: #ce1b25;
        background: #ce1b25;
    }

    .rep-button:hover {
        filter: brightness(1.12);
    }

    .rep-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .rep-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .rep-stat {
        padding: 20px;
    }

    .rep-stat span {
        display: block;
        color: var(--rep-muted);
        font-size: 12px;
    }

    .rep-stat strong {
        display: block;
        margin-top: 10px;
        font-size: 29px;
    }

    .rep-filters {
        display: grid;
        grid-template-columns: 1fr 1.4fr 1fr auto;
        gap: 14px;
        align-items: end;
    }

    .rep-field label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .rep-field input,
    .rep-field select {
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #444854;
        border-radius: 9px;
        color: #eef0f5;
        background: var(--rep-soft);
        font-family: inherit;
        font-size: 13px;
    }

    .rep-field input:focus,
    .rep-field select:focus {
        outline: 2px solid #ff858d;
        outline-offset: 2px;
    }

    .rep-table-card {
        padding: 0;
        overflow: hidden;
    }

    .rep-table-heading {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        padding: 20px;
    }

    .rep-table-heading h2 {
        margin: 0 0 6px;
        font-size: 17px;
    }

    .rep-table-wrap {
        overflow-x: auto;
    }

    .rep-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
    }

    .rep-table th,
    .rep-table td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--rep-border);
        text-align: left;
        font-size: 12px;
    }

    .rep-table th {
        color: var(--rep-muted);
        background: var(--rep-soft);
        font-size: 10px;
        text-transform: uppercase;
    }

    .rep-table .number {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .rep-badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .rep-badge.normal {
        color: #8be5ae;
        background: #38d47814;
    }

    .rep-badge.bajo {
        color: #f4cf7d;
        background: #f5b94214;
    }

    .rep-badge.agotado {
        color: #ff9ca4;
        background: #e31b2317;
    }

    .rep-empty {
        padding: 35px !important;
        color: var(--rep-muted);
        text-align: center !important;
    }

    .rep-pagination {
        padding: 18px;
    }

    .rep-errors {
        padding: 15px;
        border: 1px solid #e31b2355;
        border-radius: 10px;
        color: #ffadb4;
        background: #e31b2310;
        font-size: 13px;
    }

    @media (max-width: 1100px) {
        .rep-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .rep-grid,
        .rep-filters {
            grid-template-columns: 1fr;
        }

        .rep-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rep-header {
            padding: 19px;
        }

        .rep-header h1 {
            font-size: 23px;
        }
    }
</style>
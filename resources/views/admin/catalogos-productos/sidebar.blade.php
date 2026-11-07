{{-- ============================================================
     INVENTARIO
============================================================ --}}

<div class="menu-section">

    <div class="menu-title">
        Inventario
    </div>


    {{-- ========================================================
         PRODUCTOS
    ========================================================= --}}

    <a
        href="{{ route('admin.productos.index') }}"
        class="menu-item {{
            request()->routeIs('admin.productos.*')
                ? 'active'
                : ''
        }}"
        title="Gestión de productos"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path
                d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"
            />

            <path d="m3.3 7 8.7 5 8.7-5"/>

            <path d="M12 22V12"/>
        </svg>

        <span>
            Productos
        </span>
    </a>


    {{-- ========================================================
         CATÁLOGOS
         Categorías - Subcategorías - Marcas
    ========================================================= --}}

    <a
        href="{{ route('admin.catalogos-productos.index') }}"
        class="menu-item {{
            request()->routeIs('admin.catalogos-productos.*')
                ? 'active'
                : ''
        }}"
        title="Categorías, subcategorías y marcas"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path
                d="M20 12 12 20 4 12V4h8Z"
            />

            <circle
                cx="9"
                cy="9"
                r="1.2"
            />

            <path
                d="m14 6 6 6"
            />
        </svg>

        <span>
            Catálogos
        </span>
    </a>


    {{-- ========================================================
         SEPARADOR
    ========================================================= --}}

    <div
        style="
            height: 1px;
            margin: 8px 13px;
            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255,255,255,.07),
                    transparent
                );
        "
    ></div>


    {{-- ========================================================
         INVENTARIO / EXISTENCIAS
    ========================================================= --}}

    <a
        href="#"
        class="menu-item"
        title="Existencias y stock de productos"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <rect
                x="3"
                y="4"
                width="18"
                height="16"
                rx="2"
            />

            <path d="M3 9h18"/>

            <path d="M8 13h3"/>

            <path d="M8 16h6"/>

            <path d="M17 13v4"/>

            <path d="M15 15h4"/>
        </svg>

        <span>
            Inventario
        </span>
    </a>


    {{-- ========================================================
         ALMACENES
         Almacenes - zonas - pasillos - estanterías - ubicaciones
    ========================================================= --}}

    <a
        href="#"
        class="menu-item"
        title="Almacenes y ubicaciones"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path
                d="M3 21V9l9-6 9 6v12"
            />

            <path
                d="M7 21v-8h10v8"
            />

            <path
                d="M7 10h10"
            />

            <path
                d="M9 13v8"
            />

            <path
                d="M15 13v8"
            />
        </svg>

        <span>
            Almacenes
        </span>
    </a>


    {{-- ========================================================
         MOVIMIENTOS
         Entradas - salidas - transferencias - ajustes
    ========================================================= --}}

    <a
        href="#"
        class="menu-item"
        title="Entradas, salidas y transferencias"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M7 7h13"/>

            <path d="m16 3 4 4-4 4"/>

            <path d="M17 17H4"/>

            <path d="m8 21-4-4 4-4"/>
        </svg>

        <span>
            Movimientos
        </span>
    </a>


    {{-- ========================================================
         REPOSICIÓN
         Stock mínimo - sugerencias - tareas de reposición
    ========================================================= --}}

    <a
        href="#"
        class="menu-item"
        title="Reposición de productos"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path
                d="M20 7h-9"
            />

            <path
                d="m16 3 4 4-4 4"
            />

            <path
                d="M4 17h9"
            />

            <path
                d="m8 21-4-4 4-4"
            />

            <circle
                cx="12"
                cy="12"
                r="1"
            />
        </svg>

        <span>
            Reposición
        </span>
    </a>

</div>
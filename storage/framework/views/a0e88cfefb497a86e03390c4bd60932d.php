

<?php $__env->startSection('title', 'Alertas'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .al-page {
        --al-bg: #17181c;
        --al-soft: #202127;
        --al-border: #343741;
        --al-muted: #a3a9b6;
        display: grid;
        gap: 20px;
        color: #eef0f5;
    }

    .al-page * { box-sizing: border-box; }

    .al-header, .al-card, .al-stat, .al-item {
        border: 1px solid var(--al-border);
        border-radius: 15px;
        background: var(--al-bg);
    }

    .al-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
        padding: 24px;
        background:
            radial-gradient(circle at right top, #e31b231b, transparent 65%),
            var(--al-bg);
    }

    .al-eyebrow {
        color: #ff858d;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .al-header h1 { margin: 8px 0; font-size: 28px; }

    .al-help {
        margin: 0;
        color: var(--al-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .al-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .al-actions form { margin: 0; }

    .al-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 41px;
        padding: 10px 14px;
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

    .al-button.primary {
        border-color: #cc1b25;
        background: #cc1b25;
    }

    .al-button:hover { filter: brightness(1.12); }

    .al-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .al-stat { padding: 20px; }

    .al-stat span {
        color: var(--al-muted);
        font-size: 12px;
    }

    .al-stat strong {
        display: block;
        margin-top: 10px;
        font-size: 29px;
    }

    .al-card { padding: 20px; }

    .al-filters {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 15px;
    }

    .al-field label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .al-field input, .al-field select {
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #444854;
        border-radius: 9px;
        color: #eef0f5;
        background: var(--al-soft);
        font-family: inherit;
        font-size: 13px;
    }

    .al-field input:focus, .al-field select:focus {
        outline: 2px solid #ff858d;
        outline-offset: 2px;
    }

    .al-list { display: grid; gap: 14px; }

    .al-item {
        padding: 20px;
        border-left: 4px solid #727b8e;
    }

    .al-item.critica { border-left-color: #ff6471; }
    .al-item.alta { border-left-color: #f5b942; }
    .al-item.media { border-left-color: #6ea8ff; }
    .al-item.baja { border-left-color: #8ba0b5; }

    .al-item.unread {
        box-shadow: inset 0 0 0 1px rgba(227, 27, 35, .15);
    }

    .al-item-header {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        align-items: flex-start;
    }

    .al-item h2 {
        margin: 12px 0 9px;
        font-size: 16px;
        overflow-wrap: anywhere;
    }

    .al-message {
        margin: 0;
        color: #c4c9d4;
        font-size: 13px;
        line-height: 1.8;
        white-space: pre-line;
        overflow-wrap: anywhere;
    }

    .al-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .al-badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        color: #d1d6e1;
        background: #2a2d36;
        font-size: 10px;
        font-weight: 700;
    }

    .al-badge.critica { color: #ffa2ac; background: #e31b231c; }
    .al-badge.alta { color: #f3d18a; background: #f5b94218; }
    .al-badge.media { color: #a8caff; background: #6ea8ff18; }
    .al-badge.resuelta { color: #8be5ae; background: #38d47816; }
    .al-badge.unread { color: #fff; background: #ba1c27; }

    .al-date {
        color: var(--al-muted);
        font-size: 11px;
        white-space: nowrap;
    }

    .al-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 18px;
        margin-top: 15px;
        color: var(--al-muted);
        font-size: 11px;
        line-height: 1.7;
    }

    .al-item .al-actions { margin-top: 16px; }

    .al-feedback {
        padding: 14px 16px;
        border: 1px solid #38d47844;
        border-radius: 10px;
        color: #9de6b7;
        background: #38d47810;
        font-size: 13px;
        line-height: 1.7;
    }

    .al-feedback.error {
        border-color: #e31b2355;
        color: #ffadb5;
        background: #e31b2310;
    }

    .al-empty {
        padding: 35px;
        text-align: center;
    }

    @media (max-width: 900px) {
        .al-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 600px) {
        .al-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .al-filters { grid-template-columns: 1fr; }
        .al-item-header { flex-direction: column; }
        .al-header { padding: 19px; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="al-page">
    <header class="al-header">
        <div>
            <span class="al-eyebrow">
                Minisúper NANOS · Centro de alertas
            </span>

            <h1>Alertas</h1>

            <p class="al-help">
                Revisa las situaciones pendientes y consulta las que ya se resolvieron.
            </p>
        </div>

        <div class="al-actions">
            <form
                action="<?php echo e(route('admin.alertas.actualizar')); ?>"
                method="POST"
            >
                <?php echo csrf_field(); ?>

                <button type="submit" class="al-button primary">
                    Actualizar alertas
                </button>
            </form>

            <form
                action="<?php echo e(route('admin.alertas.leidas')); ?>"
                method="POST"
            >
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <button type="submit" class="al-button">
                    Marcar todas como leídas
                </button>
            </form>
        </div>
    </header>

    <?php if(session('success')): ?>
        <div class="al-feedback" role="status">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="al-feedback error" role="alert">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div><?php echo e($error); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <section class="al-stats">
        <article class="al-stat">
            <span>Pendientes</span>
            <strong><?php echo e($resumen['pendientes']); ?></strong>
        </article>

        <article class="al-stat">
            <span>Pendientes sin leer</span>
            <strong style="color:#ff929d;">
                <?php echo e($resumen['sin_leer']); ?>

            </strong>
        </article>

        <article class="al-stat">
            <span>Críticas pendientes</span>
            <strong style="color:#f3ce80;">
                <?php echo e($resumen['criticas']); ?>

            </strong>
        </article>

        <article class="al-stat">
            <span>Resueltas</span>
            <strong style="color:#8be5ae;">
                <?php echo e($resumen['resueltas']); ?>

            </strong>
        </article>
    </section>

    <p class="al-help">
        Los indicadores muestran el resumen general.
        Las marcas de lectura corresponden solamente a tu usuario.
    </p>

    <section class="al-card">
        <form action="<?php echo e(route('admin.alertas.index')); ?>" method="GET">
            <div class="al-filters">
                <div class="al-field">
                    <label for="buscar">Buscar</label>

                    <input
                        id="buscar"
                        name="buscar"
                        type="search"
                        maxlength="100"
                        value="<?php echo e($filtros['buscar']); ?>"
                        placeholder="Producto, lote, tarea o mensaje"
                    >
                </div>

                <div class="al-field">
                    <label for="tipo_alerta_id">Tipo de alerta</label>

                    <select id="tipo_alerta_id" name="tipo_alerta_id">
                        <option value="">Todos los tipos</option>

                        <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($tipo->id); ?>"
                                <?php if($filtros['tipo_alerta_id'] == $tipo->id): echo 'selected'; endif; ?>
                            >
                                <?php echo e($tipo->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="al-field">
                    <label for="nivel">Prioridad</label>

                    <select id="nivel" name="nivel">
                        <?php $__currentLoopData = [
                            'todos' => 'Todas',
                            'critica' => 'Crítica',
                            'alta' => 'Alta',
                            'media' => 'Media',
                            'baja' => 'Baja',
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $texto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($valor); ?>"
                                <?php if($filtros['nivel'] === $valor): echo 'selected'; endif; ?>
                            >
                                <?php echo e($texto); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="al-field">
                    <label for="estado">Estado</label>

                    <select id="estado" name="estado">
                        <?php $__currentLoopData = [
                            'pendiente' => 'Pendientes',
                            'resuelta' => 'Resueltas',
                            'todos' => 'Todas',
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $texto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($valor); ?>"
                                <?php if($filtros['estado'] === $valor): echo 'selected'; endif; ?>
                            >
                                <?php echo e($texto); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="al-field">
                    <label for="lectura">Lectura</label>

                    <select id="lectura" name="lectura">
                        <?php $__currentLoopData = [
                            'todos' => 'Todas',
                            'no_leida' => 'Sin leer',
                            'leida' => 'Leídas',
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $texto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($valor); ?>"
                                <?php if($filtros['lectura'] === $valor): echo 'selected'; endif; ?>
                            >
                                <?php echo e($texto); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="al-field">
                    <label for="almacen_id">Almacén asignado a la alerta</label>

                    <select id="almacen_id" name="almacen_id">
                        <option value="">Todos, incluidas las generales</option>

                        <?php $__currentLoopData = $almacenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $almacen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($almacen->id); ?>"
                                <?php if($filtros['almacen_id'] == $almacen->id): echo 'selected'; endif; ?>
                            >
                                <?php echo e($almacen->codigo); ?> · <?php echo e($almacen->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="al-actions" style="margin-top:18px;">
                <button type="submit" class="al-button primary">
                    Aplicar filtros
                </button>

                <a
                    href="<?php echo e(route('admin.alertas.index')); ?>"
                    class="al-button"
                >
                    Limpiar
                </a>
            </div>

            <p class="al-help" style="margin-top:12px;">
                Las alertas generales de stock y lotes no tienen un almacén
                específico y no aparecerán al filtrar por un almacén.
            </p>
        </form>
    </section>

    <div class="al-help">
        <?php echo e($alertas->total()); ?> alertas encontradas.
    </div>

    <section class="al-list">
        <?php $__empty_1 = true; $__currentLoopData = $alertas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $nivel = in_array(
                    $alerta->nivel,
                    ['critica', 'alta', 'media', 'baja'],
                    true
                ) ? $alerta->nivel : 'media';

                $nivelTexto = match ($nivel) {
                    'critica' => 'Crítica',
                    'alta' => 'Alta',
                    'baja' => 'Baja',
                    default => 'Media',
                };

                $leida = (int) $alerta->leida === 1;
            ?>

            <article class="al-item <?php echo e($nivel); ?> <?php echo e(!$leida ? 'unread' : ''); ?>">
                <div class="al-item-header">
                    <div class="al-badges">
                        <span class="al-badge <?php echo e($nivel); ?>">
                            <?php echo e($nivelTexto); ?>

                        </span>

                        <span class="al-badge">
                            <?php echo e($alerta->tipo_nombre); ?>

                        </span>

                        <span class="al-badge <?php echo e($alerta->estado === 'resuelta' ? 'resuelta' : ''); ?>">
                            <?php echo e(ucfirst($alerta->estado ?? 'pendiente')); ?>

                        </span>

                        <?php if(!$leida): ?>
                            <span class="al-badge unread">Sin leer</span>
                        <?php endif; ?>
                    </div>

                    <span class="al-date">
                        <?php echo e($alerta->fecha_generacion
                            ? \Illuminate\Support\Carbon::parse($alerta->fecha_generacion)->format('d/m/Y H:i')
                            : '—'); ?>

                    </span>
                </div>

                <h2><?php echo e($alerta->titulo); ?></h2>

                <p class="al-message"><?php echo e($alerta->mensaje); ?></p>

                <div class="al-meta">
                    <?php if($alerta->producto): ?>
                        <span>
                            Producto:
                            <?php echo e($alerta->codigo_interno); ?>

                            · <?php echo e($alerta->producto); ?>

                        </span>
                    <?php endif; ?>

                    <?php if($alerta->codigo_lote): ?>
                        <span>Lote: <?php echo e($alerta->codigo_lote); ?></span>
                    <?php endif; ?>

                    <?php if($alerta->codigo_tarea): ?>
                        <span>Tarea: <?php echo e($alerta->codigo_tarea); ?></span>
                    <?php endif; ?>

                    <span>
                        Ámbito:
                        <?php echo e($alerta->almacen ?? 'General / sin almacén específico'); ?>

                    </span>

                    <?php if($alerta->fecha_resolucion): ?>
                        <span>
                            Resuelta:
                            <?php echo e(\Illuminate\Support\Carbon::parse(
                                $alerta->fecha_resolucion
                            )->format('d/m/Y H:i')); ?>

                        </span>
                    <?php endif; ?>
                </div>

                <div class="al-actions">
                    <?php if(!$leida): ?>
                        <form
                            action="<?php echo e(route('admin.alertas.leida', [
                                'alerta' => $alerta->id,
                            ])); ?>"
                            method="POST"
                        >
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>

                            <button type="submit" class="al-button">
                                Marcar como leída
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if(
                        $alerta->producto_id
                        && \Illuminate\Support\Facades\Route::has('admin.inventario.historial')
                    ): ?>
                        <a
                            href="<?php echo e(route('admin.inventario.historial', [
                                'producto' => $alerta->producto_id,
                            ])); ?>"
                            class="al-button"
                        >
                            Ver historial del producto
                        </a>
                    <?php endif; ?>

                    <?php if(
                        $alerta->tarea_id
                        && \Illuminate\Support\Facades\Route::has('admin.tareas.show')
                    ): ?>
                        <a
                            href="<?php echo e(route('admin.tareas.show', [
                                'tarea' => $alerta->tarea_id,
                            ])); ?>"
                            class="al-button"
                        >
                            Ver tarea
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="al-card al-empty">
                <h2>No hay alertas para estos filtros</h2>

                <p class="al-help">
                    Puedes cambiar los filtros o consultar las alertas resueltas.
                </p>
            </div>
        <?php endif; ?>
    </section>

    <?php if($alertas->hasPages()): ?>
        <div class="al-card">
            <?php echo e($alertas->links()); ?>

        </div>
    <?php endif; ?>

    <p class="al-help">
        Leer una alerta no la resuelve. Las alertas automáticas se resuelven
        cuando, al actualizar, deja de existir la situación que las originó.
    </p>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\nanosminisuper20262.1\resources\views/admin/alertas/index.blade.php ENDPATH**/ ?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Panel pendiente | NANOS</title></head>
<body style="background:#121319;color:#fff;font:16px system-ui;max-width:700px;margin:12vh auto;padding:24px;">
<h1>Tu rol aún no tiene panel habilitado</h1>
<p>Tu cuenta está activa. El administrador está preparando el módulo correspondiente a tu rol.</p>
<form method="POST" action="{{ route('logout') }}">@csrf <button type="submit" style="padding:12px 20px;">Cerrar sesión</button></form>
</body></html>
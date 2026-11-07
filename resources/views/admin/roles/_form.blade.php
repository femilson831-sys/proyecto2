<div class="nr-fields">
    <div class="nr-field">
        <label for="nombre">Nombre del rol <span class="nr-required">*</span></label>
        <input class="nr-input" id="nombre" name="nombre" type="text" maxlength="80" required value="{{ old('nombre', $rol->nombre ?? '') }}" placeholder="Ejemplo: Encargado de almacén" autocomplete="off" aria-describedby="nombre-help @error('nombre') nombre-error @enderror" @error('nombre') aria-invalid="true" @enderror>
        <small class="nr-field-note" id="nombre-help">Usa un nombre claro que identifique la función del equipo.</small>
        @error('nombre')<small class="nr-error" id="nombre-error">{{ $message }}</small>@enderror
    </div>
    <div class="nr-field">
        <label for="descripcion">Descripción <span style="color:var(--r-muted);font-weight:400">· Opcional</span></label>
        <textarea class="nr-textarea" id="descripcion" name="descripcion" rows="4" maxlength="255" placeholder="Describe la función y las responsabilidades de este perfil." aria-describedby="descripcion-help @error('descripcion') descripcion-error @enderror" @error('descripcion') aria-invalid="true" @enderror>{{ old('descripcion', $rol->descripcion ?? '') }}</textarea>
        <span class="nr-counter" id="description-count"></span><small class="nr-field-note" id="descripcion-help">Máximo 255 caracteres.</small>
        @error('descripcion')<small class="nr-error" id="descripcion-error">{{ $message }}</small>@enderror
    </div>
</div>

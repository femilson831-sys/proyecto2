{{-- =========================================================



     FORMULARIO REUTILIZABLE DE EMPLEADOS



     Minisúper NANOS



\========================================================= --}}







@php







    $editando = isset($empleado);







@endphp











<style>







    /* =========================================================



       SECCIONES



    ========================================================= */







    .employee-form-section {



        padding: 25px 0;



        border-bottom: 1px solid rgba(255,255,255,.06);



    }







    .employee-form-section:last-child {



        border-bottom: none;



    }







    .employee-section-heading {



        display: flex;



        align-items: center;



        gap: 13px;



        margin-bottom: 22px;



    }







    .employee-section-heading-icon {



        width: 40px;



        height: 40px;



        flex: 0 0 40px;







        display: flex;



        align-items: center;



        justify-content: center;







        border-radius: 11px;







        background: rgba(227,27,35,.09);



        color: #ff4148;



    }







    .employee-section-heading-icon svg {



        width: 19px;



        height: 19px;



    }







    .employee-section-heading-text h3 {



        margin: 0 0 3px;







        color: #f3f4f5;







        font-size: 14px;



        font-weight: 800;



    }







    .employee-section-heading-text p {



        margin: 0;







        color: #747a82;







        font-size: 11px;



        line-height: 1.5;



    }











    /* =========================================================



       GRID



    ========================================================= */







    .employee-form-grid {



        display: grid;



        grid-template-columns:



            repeat(2, minmax(0, 1fr));







        gap: 18px;



    }







    .employee-form-group {



        min-width: 0;



    }







    .employee-form-group.full {



        grid-column: 1 / -1;



    }











    /* =========================================================



       LABEL



    ========================================================= */







    .employee-form-label {



        display: flex;



        align-items: center;



        gap: 5px;







        margin-bottom: 8px;







        color: #aeb3ba;







        font-size: 11px;



        font-weight: 800;







        text-transform: uppercase;



        letter-spacing: .4px;



    }







    .required-star {



        color: #ff4148;



        font-size: 13px;



    }







    .optional-label {



        margin-left: auto;







        color: #5f656d;







        font-size: 9px;



        font-weight: 700;







        text-transform: none;



        letter-spacing: 0;



    }











    /* =========================================================



       INPUTS



    ========================================================= */







    .employee-input,



    .employee-select,



    .employee-textarea {



        width: 100%;







        border:



            1px solid



            rgba(255,255,255,.08);







        border-radius: 11px;







        outline: none;







        background: rgba(255,255,255,.035);







        color: #e9eaec;







        font-family: inherit;







        font-size: 13px;







        transition:



            border-color .2s ease,



            *background* .2s ease,



            box-shadow .2s ease;



    }







    .employee-input,



    .employee-select {



        height: 44px;



        padding: 0 13px;



    }







    .employee-textarea {



        min-height: 105px;







        padding: 12px 13px;







        resize: vertical;







        line-height: 1.6;



    }







    .employee-input::placeholder,



    .employee-textarea::placeholder {



        color: #555b63;



    }







    .employee-input:hover,



    .employee-select:hover,



    .employee-textarea:hover {



        border-color: rgba(255,255,255,.13);



    }







    .employee-input:focus,



    .employee-select:focus,



    .employee-textarea:focus {



        border-color: rgba(227,27,35,.60);







        background: rgba(255,255,255,.05);







        box-shadow:



            0 0 0 3px



            rgba(227,27,35,.08);



    }







    .employee-select {



        cursor: pointer;



    }







    .employee-select option {



        background: #17191c;



        color: #fff;



    }











    /* =========================================================



       ERROR EN CAMPO



    ========================================================= */







    .employee-input.input-error,



    .employee-select.input-error,



    .employee-textarea.input-error {



        border-color: rgba(239,68,68,.65);







        box-shadow:



            0 0 0 3px



            rgba(239,68,68,.06);



    }







    .field-error {



        display: flex;



        align-items: center;



        gap: 5px;







        margin-top: 6px;







        color: #f87171;







        font-size: 10px;



        font-weight: 600;



    }







    .field-error svg {



        width: 12px;



        height: 12px;



        flex: 0 0 12px;



    }











    /* =========================================================



       AYUDA



    ========================================================= */







    .field-help {



        display: block;







        margin-top: 6px;







        color: #5f656d;







        font-size: 10px;



        line-height: 1.5;



    }











    /* =========================================================



       ESTADO



    ========================================================= */







    .employee-status-options {



        display: grid;



        grid-template-columns:



            repeat(2, minmax(0, 1fr));







        gap: 10px;



    }







    .status-option {



        position: relative;



    }







    .status-option input {



        position: absolute;



        opacity: 0;



        pointer-events: none;



    }







    .status-option label {



        min-height: 44px;







        padding: 0 13px;







        display: flex;



        align-items: center;



        gap: 9px;







        border:



            1px solid



            rgba(255,255,255,.08);







        border-radius: 11px;







        background: rgba(255,255,255,.025);







        color: #8c9299;







        font-size: 12px;



        font-weight: 700;







        cursor: pointer;







        transition:



            *background* .2s ease,



            border-color .2s ease,



            color .2s ease;



    }







    .status-option-dot {



        width: 8px;



        height: 8px;







        border-radius: 50%;







        background: #555b63;



    }







    .status-option.active-option



        input:checked + label {







        border-color: rgba(34,197,94,.35);







        background: rgba(34,197,94,.07);







        color: #4ade80;



    }







    .status-option.active-option



        input:checked + label



        .status-option-dot {







        background: #22c55e;







        box-shadow:



            0 0 0 4px



            rgba(34,197,94,.10);



    }







    .status-option.inactive-option



        input:checked + label {







        border-color: rgba(239,68,68,.30);







        background: rgba(239,68,68,.06);







        color: #f87171;



    }







    .status-option.inactive-option



        input:checked + label



        .status-option-dot {







        background: #ef4444;







        box-shadow:



            0 0 0 4px



            rgba(239,68,68,.09);



    }











    /* =========================================================



       RESPONSIVE



    ========================================================= */







    @media (max-width: 700px) {







        .employee-form-grid {



            grid-template-columns: 1fr;



        }







        .employee-form-group.full {



            grid-column: auto;



        }







    }







    @media (max-width: 420px) {







        .employee-status-options {



            grid-template-columns: 1fr;



        }







    }











    /* =========================================================

       FOTOGRAFÍA DEL EMPLEADO

    ========================================================= */

    .employee-photo-wrapper {

        display: grid;

        grid-template-columns: 150px minmax(0, 1fr);

        gap: 22px;

        align-items: center;

        margin-bottom: 22px;

        padding: 18px;

        border: 1px solid rgba(255,255,255,.07);

        border-radius: 15px;

        background: rgba(255,255,255,.018);

    }

    .employee-photo-preview {

        width: 140px;

        height: 140px;

        overflow: hidden;

        border-radius: 18px;

        border: 2px solid rgba(227,27,35,.25);

        background: linear-gradient(145deg, rgba(227,27,35,.08), rgba(255,255,255,.025));

        display: flex;

        align-items: center;

        justify-content: center;

    }

    .employee-photo-preview img { width:100%; height:100%; object-fit:cover; display:block; }

    .employee-photo-placeholder {

        width:100%; height:100%; display:flex; flex-direction:column;

        align-items:center; justify-content:center; gap:8px; color:#6f757d;

    }

    .employee-photo-placeholder svg { width:45px; height:45px; }

    .employee-photo-placeholder span { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.4px; }

    .employee-photo-info { min-width:0; }

    .employee-photo-title { margin-bottom:6px; color:#e7e8ea; font-size:13px; font-weight:800; }

    .employee-photo-description { margin-bottom:13px; color:#686e76; font-size:10px; line-height:1.6; }

    .employee-photo-input {

        width:100%; padding:10px; border:1px solid rgba(255,255,255,.08);

        border-radius:11px; background:rgba(255,255,255,.035); color:#aeb3ba;

        font-family:inherit; font-size:11px;

    }

    .employee-photo-input::file-selector-button {

        margin-right:10px; padding:8px 12px; border:0; border-radius:8px;

        background:rgba(227,27,35,.12); color:#ff555b; font-family:inherit;

        font-size:10px; font-weight:800; cursor:pointer;

    }

    .employee-photo-input.input-error { border-color:rgba(239,68,68,.65); }

    @media (max-width:600px) {

        .employee-photo-wrapper { grid-template-columns:1fr; }

        .employee-photo-preview { margin:0 auto; }

    }





    /* CÁMARA DEL EMPLEADO */

    .employee-camera-buttons { margin-top:12px; display:flex; gap:10px; flex-wrap:wrap; }

    .employee-camera-btn {

        border:1px solid rgba(227,27,35,.28); border-radius:10px; padding:10px 14px;

        background:rgba(227,27,35,.09); color:#ff6268; font:inherit; font-size:11px;

        font-weight:800; cursor:pointer; transition:.2s ease;

    }

    .employee-camera-btn:hover { background:rgba(227,27,35,.16); transform:translateY(-1px); }

    .employee-camera-box {

        margin-top:14px; padding:12px; border:1px solid rgba(255,255,255,.08);

        border-radius:14px; background:#0b0c0e;

    }

    .employee-camera-box video {

        width:100%; max-height:360px; display:block; object-fit:cover; border-radius:11px;

        background:#050506; transform:scaleX(-1);

    }

    .employee-camera-actions { margin-top:10px; display:flex; gap:10px; flex-wrap:wrap; }

    .employee-camera-btn.capture { background:#e31b23; border-color:#e31b23; color:#fff; }

    .employee-camera-btn.close { background:rgba(255,255,255,.04); border-color:rgba(255,255,255,.10); color:#b9bec5; }

    @media (max-width:600px) {

        .employee-camera-actions { flex-direction:column; }

        .employee-camera-btn { width:100%; }

    }



</style>











{{-- =========================================================



     INFORMACIÓN PERSONAL



\========================================================= --}}







<section class="employee-form-section">







    <div class="employee-section-heading">







        <div class="employee-section-heading-icon">







            <svg



                viewBox="0 0 24 24"



                fill="none"



                stroke="currentColor"



                stroke-width="2"



            >



                <circle cx="12" cy="8" r="4"/>







                <path



                    d="M4 21a8 8 0 0 1 16 0"



                />



            </svg>







        </div>











        <div class="employee-section-heading-text">







            <h3>



                Información personal



            </h3>







            <p>



                Datos de identificación del empleado.



            </p>







        </div>







    </div>















    {{-- FOTOGRAFÍA --}}

    <div class="employee-photo-wrapper">

        <div class="employee-photo-preview">

            @if($editando && !empty($empleado->foto))

                <img src="{{ asset(ltrim($empleado->foto, '/')) }}"

                     alt="Fotografía de {{ $empleado->nombres }}"

                     id="employeePhotoImage">

                <div class="employee-photo-placeholder" id="employeePhotoPlaceholder" style="display:none;">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">

                        <circle cx="12" cy="8" r="4"/>

                        <path d="M4 21a8 8 0 0 1 16 0"/>

                    </svg>

                    <span>Sin fotografía</span>

                </div>

            @else

                <img src="" alt="Vista previa" id="employeePhotoImage" style="display:none;">

                <div class="employee-photo-placeholder" id="employeePhotoPlaceholder">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">

                        <circle cx="12" cy="8" r="4"/>

                        <path d="M4 21a8 8 0 0 1 16 0"/>

                    </svg>

                    <span>Sin fotografía</span>

                </div>

            @endif

        </div>

        <div class="employee-photo-info">

            <div class="employee-photo-title">Fotografía del empleado</div>

            <div class="employee-photo-description">

                Selecciona una fotografía para identificar al empleado. JPG, JPEG, PNG o WEBP. Máximo 4 MB.

            </div>

            <input type="file" name="foto" id="foto"

                   class="employee-photo-input @error('foto') input-error @enderror"

                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            <input type="hidden" name="foto_capturada" id="foto_capturada" value="">



            <div class="employee-camera-buttons">

                <button type="button" class="employee-camera-btn" id="btnAbrirCamara">📷 Abrir cámara</button>

            </div>



            <div class="employee-camera-box" id="cameraBox" style="display:none;">

                <video id="cameraVideo" autoplay playsinline muted></video>

                <canvas id="cameraCanvas" style="display:none;"></canvas>

                <div class="employee-camera-actions">

                    <button type="button" class="employee-camera-btn capture" id="btnCapturarFoto">📸 Capturar foto</button>

                    <button type="button" class="employee-camera-btn close" id="btnCerrarCamara">✕ Cerrar cámara</button>

                </div>

                <div class="field-error" id="cameraError" style="display:none;"></div>

            </div>

            @error('foto')

                <div class="field-error">{{ $message }}</div>

            @enderror

            @if($editando && !empty($empleado->foto))

                <span class="field-help">Si no seleccionas otra fotografía, se conservará la actual.</span>

            @endif

        </div>

    </div>



    <div class="employee-form-grid">











        {{-- CÓDIGO AUTOMÁTICO --}}



        <div class="employee-form-group">



            <label for="codigo_empleado_visual" class="employee-form-label">

                Código de empleado

                <span class="optional-label">Automático</span>

            </label>



            <input

                type="text"

                id="codigo_empleado_visual"

                class="employee-input"

                value="{{ $editando ? $empleado->codigo_empleado : 'Se generará automáticamente' }}"

                disabled

                readonly

                style="cursor:not-allowed;color:#9ca3af;background:rgba(255,255,255,.02);"

            >



            <span class="field-help">

                @if($editando)

                    Código único asignado al empleado. No puede modificarse.

                @else

                    El sistema asignará automáticamente un código como EMP-001, EMP-002, EMP-003...

                @endif

            </span>



        </div>





        {{-- CI --}}







        <div class="employee-form-group">







            <label



                for="ci"



                class="employee-form-label"



            >



                Cédula de identidad







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <input



                type="text"



                id="ci"



                name="ci"



                value="{{ old('ci', $empleado->ci ?? '') }}"



                class="employee-input @error('ci') input-error @enderror"



                placeholder="Ej: 12345678 LP"



                maxlength="30"



            >











            @error('ci')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- NOMBRES --}}







        <div class="employee-form-group">







            <label



                for="nombres"



                class="employee-form-label"



            >



                Nombres







                <span class="required-star">*</span>



            </label>











            <input



                type="text"



                id="nombres"



                name="nombres"



                value="{{ old('nombres', $empleado->nombres ?? '') }}"



                class="employee-input @error('nombres') input-error @enderror"



                placeholder="Nombres del empleado"



                maxlength="100"



                required



            >











            @error('nombres')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- APELLIDOS --}}







        <div class="employee-form-group">







            <label



                for="apellidos"



                class="employee-form-label"



            >



                Apellidos







                <span class="required-star">*</span>



            </label>











            <input



                type="text"



                id="apellidos"



                name="apellidos"



                value="{{ old('apellidos', $empleado->apellidos ?? '') }}"



                class="employee-input @error('apellidos') input-error @enderror"



                placeholder="Apellidos del empleado"



                maxlength="100"



                required



            >











            @error('apellidos')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- FECHA NACIMIENTO --}}







        <div class="employee-form-group">







            <label



                for="fecha_nacimiento"



                class="employee-form-label"



            >



                Fecha de nacimiento







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <input



                type="date"



                id="fecha_nacimiento"



                name="fecha_nacimiento"



                value="{{ old(



                    'fecha_nacimiento',



                    isset($empleado) && $empleado->fecha_nacimiento



                        ? $empleado->fecha_nacimiento->format('Y-m-d')



                        : ''



                ) }}"



                max="{{ now()->format('Y-m-d') }}"



                class="employee-input @error('fecha_nacimiento') input-error @enderror"



            >











            @error('fecha_nacimiento')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- GÉNERO --}}







        <div class="employee-form-group">







            <label



                for="genero"



                class="employee-form-label"



            >



                Género







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <select



                id="genero"



                name="genero"



                class="employee-select @error('genero') input-error @enderror"



            >







                <option value="">



                    Seleccionar



                </option>







                <option



                    value="masculino"



                    @selected(



                        old(



                            'genero',



                            $empleado->genero ?? ''



                        ) === 'masculino'



                    )



                >



                    Masculino



                </option>







                <option



                    value="femenino"



                    @selected(



                        old(



                            'genero',



                            $empleado->genero ?? ''



                        ) === 'femenino'



                    )



                >



                    Femenino



                </option>







                <option



                    value="otro"



                    @selected(



                        old(



                            'genero',



                            $empleado->genero ?? ''



                        ) === 'otro'



                    )



                >



                    Otro



                </option>







            </select>











            @error('genero')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>







    </div>







</section>











{{-- =========================================================



     CONTACTO



\========================================================= --}}







<section class="employee-form-section">







    <div class="employee-section-heading">







        <div class="employee-section-heading-icon">







            <svg



                viewBox="0 0 24 24"



                fill="none"



                stroke="currentColor"



                stroke-width="2"



            >



                <path



                    d="M22 16.92v3a2 2 0 0 1-2.18 2



                    19.79 19.79 0 0 1-8.63-3.07



                    19.5 19.5 0 0 1-6-6



                    19.79 19.79 0 0 1-3.07-8.67"



                />







                <path



                    d="M4.11 2h3a2 2 0 0 1 2 1.72"



                />



            </svg>







        </div>











        <div class="employee-section-heading-text">







            <h3>



                Información de contacto



            </h3>







            <p>



                Teléfonos, correo y ubicación.



            </p>







        </div>







    </div>











    <div class="employee-form-grid">











        {{-- CELULAR --}}







        <div class="employee-form-group">







            <label



                for="celular"



                class="employee-form-label"



            >



                Celular







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <input



                type="text"



                id="celular"



                name="celular"



                value="{{ old('celular', $empleado->celular ?? '') }}"



                class="employee-input @error('celular') input-error @enderror"



                placeholder="Ej: 71234567"



                maxlength="30"



            >







            @error('celular')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- TELÉFONO --}}







        <div class="employee-form-group">







            <label



                for="telefono"



                class="employee-form-label"



            >



                Teléfono







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <input



                type="text"



                id="telefono"



                name="telefono"



                value="{{ old('telefono', $empleado->telefono ?? '') }}"



                class="employee-input @error('telefono') input-error @enderror"



                placeholder="Ej: 22800000"



                maxlength="30"



            >







            @error('telefono')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- CORREO --}}







        <div class="employee-form-group">







            <label



                for="correo"



                class="employee-form-label"



            >



                Correo electrónico







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <input



                type="email"



                id="correo"



                name="correo"



                value="{{ old('correo', $empleado->correo ?? '') }}"



                class="employee-input @error('correo') input-error @enderror"



                placeholder="empleado@nanos.com"



                maxlength="150"



            >







            @error('correo')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- CIUDAD --}}







        <div class="employee-form-group">







            <label



                for="ciudad"



                class="employee-form-label"



            >



                Ciudad







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <input



                type="text"



                id="ciudad"



                name="ciudad"



                value="{{ old('ciudad', $empleado->ciudad ?? '') }}"



                class="employee-input @error('ciudad') input-error @enderror"



                placeholder="Ej: El Alto"



                maxlength="100"



            >







            @error('ciudad')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- DIRECCIÓN --}}







        <div class="employee-form-group full">







            <label



                for="direccion"



                class="employee-form-label"



            >



                Dirección







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <input



                type="text"



                id="direccion"



                name="direccion"



                value="{{ old('direccion', $empleado->direccion ?? '') }}"



                class="employee-input @error('direccion') input-error @enderror"



                placeholder="Zona, avenida, calle, número..."



                maxlength="255"



            >







            @error('direccion')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>







    </div>







</section>











{{-- =========================================================



     INFORMACIÓN LABORAL



\========================================================= --}}







<section class="employee-form-section">







    <div class="employee-section-heading">







        <div class="employee-section-heading-icon">







            <svg



                viewBox="0 0 24 24"



                fill="none"



                stroke="currentColor"



                stroke-width="2"



            >



                <rect



                    x="3"



                    y="7"



                    width="18"



                    height="13"



                    rx="2"



                />







                <path



                    d="M8 7V5a2 2 0 0 1 2-2h4



                    a2 2 0 0 1 2 2v2"



                />







                <path d="M3 12h18"/>



            </svg>







        </div>











        <div class="employee-section-heading-text">







            <h3>



                Información laboral



            </h3>







            <p>



                Datos relacionados con su ingreso a NANOS.



            </p>







        </div>







    </div>











    <div class="employee-form-grid">



        {{-- ÁREA --}}

        <div class="employee-form-group">

            <label for="area_id" class="employee-form-label">

                Área <span class="required-star">*</span>

            </label>

            <select id="area_id" name="area_id"

                    class="employee-select @error('area_id') input-error @enderror" required>

                <option value="">Seleccionar área</option>

                @foreach($areas ?? [] as $area)

                    <option value="{{ $area->id }}"

                        @selected((string) old('area_id', $empleado->area_id ?? '') === (string) $area->id)>

                        {{ $area->nombre }}@if(!$area->estado) (Inactiva) @endif

                    </option>

                @endforeach

            </select>

            @error('area_id')

                <div class="field-error">{{ $message }}</div>

            @enderror

            <span class="field-help">Área donde desempeñará sus funciones.</span>

        </div>



        {{-- CARGO --}}

        <div class="employee-form-group">

            <label for="cargo_id" class="employee-form-label">

                Cargo <span class="required-star">*</span>

            </label>

            <select id="cargo_id" name="cargo_id"

                    class="employee-select @error('cargo_id') input-error @enderror" required>

                <option value="">Seleccionar cargo</option>

                @foreach($cargos ?? [] as $cargo)

                    <option value="{{ $cargo->id }}"

                        @selected((string) old('cargo_id', $empleado->cargo_id ?? '') === (string) $cargo->id)>

                        {{ $cargo->nombre }}@if(!$cargo->estado) (Inactivo) @endif

                    </option>

                @endforeach

            </select>

            @error('cargo_id')

                <div class="field-error">{{ $message }}</div>

            @enderror

            <span class="field-help">Cargo o función laboral del empleado.</span>

        </div>













        {{-- FECHA INGRESO --}}







        <div class="employee-form-group">







            <label



                for="fecha_ingreso"



                class="employee-form-label"



            >



                Fecha de ingreso







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <input



                type="date"



                id="fecha_ingreso"



                name="fecha_ingreso"



                value="{{ old(



                    'fecha_ingreso',



                    isset($empleado) && $empleado->fecha_ingreso



                        ? $empleado->fecha_ingreso->format('Y-m-d')



                        : ''



                ) }}"



                class="employee-input @error('fecha_ingreso') input-error @enderror"



            >











            @error('fecha_ingreso')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- TIPO CONTRATO --}}







        <div class="employee-form-group">







            <label



                for="tipo_contrato"



                class="employee-form-label"



            >



                Tipo de contrato







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <select



                id="tipo_contrato"



                name="tipo_contrato"



                class="employee-select @error('tipo_contrato') input-error @enderror"



            >







                <option value="">



                    Seleccionar



                </option>







                @php







                    $contratos = [



                        'Indefinido',



                        'Temporal',



                        'Eventual',



                        'Prácticas',



                        'Otro',



                    ];







                    $contratoActual =



                        old(



                            'tipo_contrato',



                            $empleado->tipo_contrato ?? ''



                        );







                @endphp











                @foreach($contratos as $contrato)







                    <option



                        value="{{ $contrato }}"



                        @selected(



                            $contratoActual === $contrato



                        )



                    >



                        {{ $contrato }}



                    </option>







                @endforeach







            </select>











            @error('tipo_contrato')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>











        {{-- ESTADO --}}







        <div class="employee-form-group full">







            <label class="employee-form-label">



                Estado







                <span class="required-star">*</span>



            </label>











            @php







                $estadoActual =



                    old(



                        'estado',



                        $empleado->estado ?? 'activo'



                    );







            @endphp











            <div class="employee-status-options">











                <div



                    class="status-option active-option"



                >







                    <input



                        type="radio"



                        id="estado_activo"



                        name="estado"



                        value="activo"



                        @checked(



                            $estadoActual === 'activo'



                        )



                    >







                    <label for="estado_activo">







                        <span class="status-option-dot"></span>







                        Activo







                    </label>







                </div>











                <div



                    class="status-option inactive-option"



                >







                    <input



                        type="radio"



                        id="estado_inactivo"



                        name="estado"



                        value="inactivo"



                        @checked(



                            $estadoActual === 'inactivo'



                        )



                    >







                    <label for="estado_inactivo">







                        <span class="status-option-dot"></span>







                        Inactivo







                    </label>







                </div>







            </div>











            @error('estado')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>







    </div>







</section>











{{-- =========================================================



     OBSERVACIONES



\========================================================= --}}







<section class="employee-form-section">







    <div class="employee-section-heading">







        <div class="employee-section-heading-icon">







            <svg



                viewBox="0 0 24 24"



                fill="none"



                stroke="currentColor"



                stroke-width="2"



            >



                <path



                    d="M21 15a4 4 0 0 1-4 4H8



                    l-5 3V7a4 4 0 0 1 4-4h10



                    a4 4 0 0 1 4 4Z"



                />



            </svg>







        </div>











        <div class="employee-section-heading-text">







            <h3>



                Observaciones



            </h3>







            <p>



                Información adicional sobre el empleado.



            </p>







        </div>







    </div>











    <div class="employee-form-grid">







        <div class="employee-form-group full">







            <label



                for="observaciones"



                class="employee-form-label"



            >



                Observaciones







                <span class="optional-label">



                    Opcional



                </span>



            </label>











            <textarea



                id="observaciones"



                name="observaciones"



                class="employee-textarea @error('observaciones') input-error @enderror"



                placeholder="Escribe alguna observación adicional..."



            >{{ old('observaciones', $empleado->observaciones ?? '') }}</textarea>











            @error('observaciones')







                <div class="field-error">



                    {{ $message }}



                </div>







            @enderror







        </div>







    </div>







</section>



<script>

document.addEventListener('DOMContentLoaded', function () {

    const inputFoto = document.getElementById('foto');
    const inputFotoCapturada = document.getElementById('foto_capturada');

    const imagen = document.getElementById('employeePhotoImage');

    const placeholder = document.getElementById('employeePhotoPlaceholder');

    const btnAbrir = document.getElementById('btnAbrirCamara');

    const btnCapturar = document.getElementById('btnCapturarFoto');

    const btnCerrar = document.getElementById('btnCerrarCamara');

    const cameraBox = document.getElementById('cameraBox');

    const video = document.getElementById('cameraVideo');

    const canvas = document.getElementById('cameraCanvas');

    const cameraError = document.getElementById('cameraError');

    let stream = null;



    function mostrarPreview(url) {

        if (!imagen) return;

        imagen.src = url;

        imagen.style.display = 'block';

        if (placeholder) placeholder.style.display = 'none';

    }



    function mostrarError(mensaje) {

        if (!cameraError) return;

        cameraError.textContent = mensaje;

        cameraError.style.display = 'block';

    }



    function ocultarError() {

        if (!cameraError) return;

        cameraError.textContent = '';

        cameraError.style.display = 'none';

    }



    function detenerCamara() {

        if (stream) {

            stream.getTracks().forEach(track => track.stop());

            stream = null;

        }

        if (video) video.srcObject = null;

        if (cameraBox) cameraBox.style.display = 'none';

    }



    if (inputFoto) {

        inputFoto.addEventListener('change', function () {

            const archivo = this.files && this.files.length ? this.files[0] : null;

            if (!archivo) return;

            const permitidos = ['image/jpeg', 'image/png', 'image/webp'];

            if (!permitidos.includes(archivo.type)) {

                alert('La fotografía debe ser JPG, JPEG, PNG o WEBP.');

                this.value = '';

                return;

            }

            if (archivo.size > 4 * 1024 * 1024) {

                alert('La fotografía no puede superar los 4 MB.');

                this.value = '';

                return;

            }

            mostrarPreview(URL.createObjectURL(archivo));

        });

    }



    if (btnAbrir) {

        btnAbrir.addEventListener('click', async function () {

            ocultarError();

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {

                mostrarError('Tu navegador no permite acceder a la cámara. Usa HTTPS o localhost y verifica los permisos.');

                if (cameraBox) cameraBox.style.display = 'block';

                return;

            }

            try {

                detenerCamara();

                if (cameraBox) cameraBox.style.display = 'block';

                stream = await navigator.mediaDevices.getUserMedia({

                    video: { facingMode: 'user' }, audio: false

                });

                video.srcObject = stream;

                await video.play();

            } catch (error) {

                let mensaje = 'No se pudo acceder a la cámara.';

                if (error.name === 'NotAllowedError') mensaje = 'Permiso de cámara rechazado. Habilita la cámara en el navegador.';

                if (error.name === 'NotFoundError') mensaje = 'No se encontró una cámara en este dispositivo.';

                if (error.name === 'NotReadableError') mensaje = 'La cámara está siendo utilizada por otra aplicación.';

                mostrarError(mensaje);

            }

        });

    }



    if (btnCapturar) {

        btnCapturar.addEventListener('click', function () {

            if (!video || !video.videoWidth || !video.videoHeight) {

                mostrarError('La cámara todavía no está lista.');

                return;

            }

            // Reducir la captura para evitar archivos demasiado grandes.

            const anchoMaximo = 1280;

            const escala = Math.min(1, anchoMaximo / video.videoWidth);



            canvas.width = Math.round(video.videoWidth * escala);

            canvas.height = Math.round(video.videoHeight * escala);



            const ctx = canvas.getContext('2d');

            ctx.save();

            ctx.translate(canvas.width, 0);

            ctx.scale(-1, 1);

            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            ctx.restore();
            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
            if (!dataUrl || !dataUrl.startsWith('data:image/jpeg;base64,')) {
                mostrarError('No se pudo preparar la fotografía capturada.');
                return;
            }
            if (inputFotoCapturada) inputFotoCapturada.value = dataUrl;
            if (inputFoto) inputFoto.value = '';
            mostrarPreview(dataUrl);
            ocultarError();
            detenerCamara();

        });

    }



    if (btnCerrar) btnCerrar.addEventListener('click', detenerCamara);

    window.addEventListener('beforeunload', detenerCamara);

});

</script>



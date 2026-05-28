    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative py-24 px-8 overflow-hidden">
            <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row items-center gap-16">
                <div class="flex-1">
                    <span
                        class="font-headline font-bold text-primary-container tracking-widest uppercase text-xs mb-4 block">Lorem,
                        ipsum.
                    </span>
                    <h1 class="font-headline text-5xl md:text-6xl font-extrabold text-primary leading-tight mb-6">
                        Protect Our <br /><span class="text-on-primary-container">Lorem, ipsum.</span>
                    </h1>
                    <p class="text-lg text-secondary max-w-lg mb-8 leading-relaxed">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit quas adipisci quasi nihil optio, unde possimus
                        laborum saepe ratione similique.
                    </p>
                    <div class="flex gap-4 items-center p-4 bg-surface-container-low rounded-xl border-l-4 border-primary">
                        <span class="material-symbols-outlined text-primary text-3xl" data-icon="verified_user">verified_user</span>
                        <div>
                            <p class="font-bold text-primary text-sm">Lorem, ipsum dolor.</p>
                            <p class="text-xs text-on-surface-variant">Lorem, ipsum dolor sit amet consectetur adipisicing elit.
                                Atque, debitis!</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 w-full">
                    <div class="aspect-video rounded-2xl overflow-hidden shadow-2xl relative">
                        <img alt="Pristine forest ecosystem" class="object-cover w-full h-full"
                            data-alt="cinematic wide shot of an old growth forest with sunlight rays piercing through thick green canopy and mossy floor"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAm6t951iw86i-jKqVQAzwjsvE__jKpHXaamJ7XCX5Irb_N-2mn_ZgezEggi73BCKT8OEJVMj7adIODcTeO5n65_CO7-SxVU1Fvi6gOsWQa7y42QLJZVE1ZNhUA38-GukVWDfZv92ZjZTGkDxRpLiUrnSQsccmxNLiK4pvj5r8aEH1C6JFeIUlfZu14F34pwEJIWT8yizgfee9SyBP9FXxrKdTaK3KJZvGvMymszPG2XxUSlgG3jkhG8r7d3Ah738YKyW3PmZA1Aw4" />
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent"></div>
                    </div>
                </div>
            </div>
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 w-1/3 h-full bg-surface-container-low -z-10 skew-x-12 translate-x-24"></div>
        </section>
        <!-- Form Section -->
        <section id="formSection" class="py-12 px-8 bg-surface">
            <div class="max-w-4xl mx-auto">
                <div class="bg-surface-container-lowest rounded-2xl p-8 md:p-12 shadow-sm border border-outline-variant/20">
                    <!-- Progress Stepper -->
                    <div class="flex justify-between mb-16 relative">
                        <div class="absolute top-1/2 left-0 w-full h-px bg-surface-container -z-0"></div>
                        <div id="step1Indicator"
                            class="flex flex-col items-center gap-2 relative z-10 bg-surface-container-lowest px-2 cursor-pointer"
                            onclick="goToStep(1)">
                            <div
                                class="w-8 h-8 rounded-full primary-gradient flex items-center justify-center text-white text-xs font-bold">
                                1</div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-primary">Datos</span>
                        </div>
                        <div id="step2Indicator"
                            class="flex flex-col items-center gap-2 relative z-10 bg-surface-container-lowest px-2 cursor-pointer"
                            onclick="goToStep(2)">
                            <div
                                class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant text-xs font-bold">
                                2</div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-secondary">Denuncia</span>
                        </div>
                        <div id="step3Indicator"
                            class="flex flex-col items-center gap-2 relative z-10 bg-surface-container-lowest px-2 cursor-pointer"
                            onclick="goToStep(3)">
                            <div
                                class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant text-xs font-bold">
                                3</div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-secondary">Evidencia</span>
                        </div>
                    </div>
                    <form method="post" id="complaintForm" class="space-y-12" action="<?php echo base_url('inicio/registrarDenuncia'); ?>">
                        <!-- Step 1: Details -->
                        <div id="step1" class="step-content active space-y-8">
                            <!-- Tipo de Persona -->
                            <div class="space-y-2">
                                <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Tipo de
                                    Persona</label>
                                <select id="tipoPersona" required
                                    class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                                    <option value="">Seleccione...</option>
                                    <option value="fisica">Persona Física</option>
                                    <option value="moral">Persona Moral</option>
                                </select>
                            </div>

                            <!-- Datos Personales -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Nombre Completo
                                        del Denunciante</label>
                                    <input id="nombreCompleto" type="text" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="Ingrese nombre completo" />
                                </div>
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Género</label>
                                    <select id="genero" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                                        <option value="">Seleccione...</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                        <option value="otro">Otro</option>
                                        <option value="prefiero-no-decir">Prefiero no decir</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Estado</label>
                                    <input id="estado" type="text" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="Estado" />
                                </div>
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Municipio</label>
                                    <input id="municipio" type="text" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="Municipio" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Colonia</label>
                                    <input id="colonia" type="text" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="Colonia" />
                                </div>
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Código
                                        Postal</label>
                                    <input id="codigoPostal" type="text" required maxlength="5" pattern="[0-9]{5}"
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="00000" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Calle</label>
                                    <input id="calle" type="text" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="Nombre de la calle" />
                                </div>
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Número
                                        Exterior</label>
                                    <input id="numeroExterior" type="text" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="Núm. Ext." />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Número
                                        Interior</label>
                                    <input id="numeroInterior" type="text"
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="Núm. Int. (opcional)" />
                                </div>
                            </div>

                            <!-- Contacto -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Correo
                                        Electrónico</label>
                                    <input id="email" type="email" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="correo@ejemplo.com" />
                                </div>
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Teléfono
                                        Celular</label>
                                    <input id="telefono" type="tel" required maxlength="10" pattern="[0-9]{10}"
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="10 dígitos" />
                                </div>
                            </div>

                            <!-- Documentos de Identificación -->
                            <div class="space-y-6">
                                <div class="space-y-1">
                                    <h3 class="font-headline font-bold text-lg text-primary">Documentos de Identificación</h3>
                                    <p class="text-sm text-secondary">Suba una copia de su identificación oficial (INE, pasaporte, etc.)</p>
                                </div>
                                <div id="dropZoneIdentificacion"
                                    class="border-2 border-dashed border-outline-variant/50 rounded-xl p-8 flex flex-col items-center justify-center gap-4 bg-surface-container-low/30 hover:bg-surface-container-low transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-3xl text-outline"
                                        data-icon="badge">badge</span>
                                    <div class="text-center">
                                        <p class="font-bold text-primary">Arrastra y suelta tu identificación aquí</p>
                                        <p class="text-xs text-secondary mt-1">PNG, JPG o PDF (MAX 10MB)</p>
                                    </div>
                                    <input type="file" id="fileInputIdentificacion" accept="image/*,.pdf" class="hidden" />
                                    <button onclick="document.getElementById('fileInputIdentificacion').click()"
                                        class="mt-2 px-6 py-2 bg-surface-container-highest text-primary font-bold text-xs rounded-lg hover:bg-outline-variant transition-colors"
                                        type="button">Seleccionar Archivo</button>
                                </div>
                                <div id="filePreviewIdentificacion" class="flex flex-wrap gap-4 mt-4"></div>
                            </div>

                            <!-- Representante Legal Checkbox -->
                            <div class="space-y-4 p-6 bg-surface-container-low/50 rounded-xl border border-outline-variant/20">
                                <div class="flex items-center gap-3">
                                    <input id="esRepresentante" type="checkbox" onchange="toggleLegalRepFields()"
                                        class="w-5 h-5 text-primary bg-surface-container-low border-outline-variant rounded focus:ring-primary focus:ring-2" />
                                    <label for="esRepresentante"
                                        class="font-headline font-bold text-sm text-primary uppercase tracking-wider cursor-pointer">¿Es
                                        Representante Legal?</label>
                                </div>

                                <!-- Campos de Representante Legal (inicialmente ocultos) -->
                                <div id="legalRepFields" class="space-y-6 mt-6" style="display: none;">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Razón
                                                Social</label>
                                            <input id="razonSocial" type="text"
                                                class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                                placeholder="Nombre de la empresa/organización" />
                                        </div>
                                        <div class="space-y-2">
                                            <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Nombre del
                                                Representante Legal</label>
                                            <input id="nombreRepresentante" type="text"
                                                class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                                placeholder="Nombre completo del representante" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Step 2: Complaint Details & Location -->
                        <div id="step2" class="step-content space-y-8 pt-8 border-t border-surface-container">
                            <!-- Tipo de Denuncia -->
                            <div class="space-y-2">
                                <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Tipo de
                                    Denuncia</label>
                                <select id="tipoDenuncia" name="id_tipo_denuncia" required onchange="cargarTemasDenuncia(this.value)"
                                    class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                                    <option value="">Seleccione el tipo de denuncia...</option>
                                    <?php if (isset($tiposDenuncia) && !empty($tiposDenuncia)): ?>
                                        <?php foreach ($tiposDenuncia as $tipo): ?>
                                            <option value="<?= esc($tipo['id_tipo_denuncia']) ?>"><?= esc($tipo['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Tema de Denuncia (dinámico) -->
                            <div id="temaDenunciaContainer" class="space-y-2" style="display: none;">
                                <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Tema Específico</label>
                                <select id="temaDenuncia" name="id_tema_denuncia"
                                    class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                                    <option value="">Seleccione el tema...</option>
                                </select>
                            </div>

                            <!-- Centro de Verificación Vehicular (solo para tipo 7) -->
                            <div id="centroVerificacionContainer" class="space-y-2" style="display: none;">
                                <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Centro de Verificación Vehicular</label>
                                <select id="centroVerificacion" name="clave_cvv"
                                    class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                                    <option value="">Seleccione el centro de verificación...</option>
                                </select>
                            </div>

                            <!-- Hechos Denunciados -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Hechos
                                        Denunciados</label>
                                    <span id="charCounter" class="text-xs font-medium text-secondary">0/1000 caracteres</span>
                                </div>
                                <textarea id="hechosDenunciados" required maxlength="1000"
                                    class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body resize-none"
                                    placeholder="Relate los hechos a detalle o descripción..." rows="6"
                                    oninput="updateCharCounter()"></textarea>
                                <p class="text-xs text-secondary mt-1">Proporcione una descripción detallada de los hechos observados
                                    incluyendo fechas, personas involucradas y cualquier evidencia relevante.</p>
                            </div>

                            <!-- Ubicación por Coordenada -->
                            <div class="flex justify-between items-end">
                                <div class="space-y-1">
                                    <h3 class="font-headline font-bold text-xl text-primary">Ubicación Precisa</h3>
                                    <p class="text-sm text-secondary">Marca las coordenadas exactas del impacto ambiental.</p>
                                </div>
                                <button onclick="getCurrentLocation()"
                                    class="text-primary font-bold text-xs flex items-center gap-1 hover:underline" type="button">
                                    <span class="material-symbols-outlined text-sm" data-icon="my_location">my_location</span>
                                    USAR POSICIÓN ACTUAL
                                </button>
                            </div>
                            <div id="mapContainer"
                                class="map-container aspect-[21/9] w-full bg-surface-container rounded-xl overflow-hidden relative border border-outline-variant/20">
                                <div id="map" class="w-full h-full"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="text-secondary font-bold">Latitud:</label>
                                    <input id="latitude" type="text" readonly
                                        class="w-full bg-surface-container-low rounded px-3 py-2 mt-1" placeholder="Haga clic en el mapa" />
                                </div>
                                <div>
                                    <label class="text-secondary font-bold">Longitud:</label>
                                    <input id="longitude" type="text" readonly
                                        class="w-full bg-surface-container-low rounded px-3 py-2 mt-1" placeholder="Haga clic en el mapa" />
                                </div>
                            </div>
                        </div>
                        <!-- Step 3: Reported Party & Evidence -->
                        <div id="step3" class="step-content space-y-8 pt-8 border-t border-surface-container">
                            <!-- Datos del Denunciado -->
                            <div class="space-y-6">
                                <div class="space-y-1">
                                    <h3 class="font-headline font-bold text-xl text-primary">Datos del Denunciado</h3>
                                    <p class="text-sm text-secondary">Información sobre la persona o entidad responsable del hecho
                                        denunciado.</p>
                                </div>

                                <!-- Nombre Completo del Denunciado -->
                                <div class="space-y-2">
                                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Nombre Completo
                                        del Denunciado</label>
                                    <input id="nombreDenunciado" type="text" required
                                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                        placeholder="Ingrese nombre o 'Quien resulte responsable'" />
                                    <p class="text-xs text-secondary mt-1">En caso de no conocer el nombre del denunciado, colocar "Quien
                                        resulte responsable"</p>
                                </div>

                                <!-- Checkbox: ¿Es Persona Moral? -->
                                <div class="space-y-4 p-6 bg-surface-container-low/50 rounded-xl border border-outline-variant/20">
                                    <div class="flex items-center gap-3">
                                        <input id="denunciadoEsMoral" type="checkbox" onchange="toggleDenunciadoMoralFields()"
                                            class="w-5 h-5 text-primary bg-surface-container-low border-outline-variant rounded focus:ring-primary focus:ring-2" />
                                        <label for="denunciadoEsMoral"
                                            class="font-headline font-bold text-sm text-primary uppercase tracking-wider cursor-pointer">¿El
                                            denunciado es Persona Moral?</label>
                                    </div>

                                    <!-- Razón Social del Denunciado (inicialmente oculto) -->
                                    <div id="denunciadoMoralFields" class="space-y-4 mt-4" style="display: none;">
                                        <div class="space-y-2">
                                            <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Razón
                                                Social</label>
                                            <input id="razonSocialDenunciado" type="text"
                                                class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                                placeholder="Nombre de la empresa/organización denunciada" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Dirección del Denunciado -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label
                                            class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Municipio</label>
                                        <input id="municipioDenunciado" type="text" required
                                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                            placeholder="Municipio del denunciado" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Colonia</label>
                                        <input id="coloniaDenunciado" type="text" required
                                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                            placeholder="Colonia del denunciado" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Calle</label>
                                        <input id="calleDenunciado" type="text" required
                                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                            placeholder="Nombre de la calle" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Código
                                            Postal</label>
                                        <input id="codigoPostalDenunciado" type="text" required maxlength="5" pattern="[0-9]{5}"
                                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                            placeholder="00000" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Número
                                            Exterior</label>
                                        <input id="numeroExteriorDenunciado" type="text" required
                                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                            placeholder="Núm. Ext." />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Número
                                            Interior</label>
                                        <input id="numeroInteriorDenunciado" type="text"
                                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                                            placeholder="Núm. Int. (opcional)" />
                                    </div>
                                </div>
                            </div>

                            <!-- Subida de Evidencia -->
                            <div class="space-y-6">
                                <div class="space-y-1">
                                    <h3 class="font-headline font-bold text-xl text-primary">Subida de Evidencia</h3>
                                    <p class="text-sm text-secondary">Las imágenes de alta resolución son críticas para la revisión
                                        institucional.</p>
                                </div>
                                <div id="dropZone"
                                    class="border-2 border-dashed border-outline-variant/50 rounded-xl p-12 flex flex-col items-center justify-center gap-4 bg-surface-container-low/30 hover:bg-surface-container-low transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-4xl text-outline"
                                        data-icon="cloud_upload">cloud_upload</span>
                                    <div class="text-center">
                                        <p class="font-bold text-primary">Arrastra y suelta archivos aquí</p>
                                        <p class="text-xs text-secondary mt-1">PNG, JPG o PDF (MAX 25MB)</p>
                                    </div>
                                    <input type="file" id="fileInput" multiple accept="image/*,.pdf" class="hidden" />
                                    <button onclick="document.getElementById('fileInput').click()"
                                        class="mt-2 px-6 py-2 bg-surface-container-highest text-primary font-bold text-xs rounded-lg hover:bg-outline-variant transition-colors"
                                        type="button">Seleccionar Archivos</button>
                                </div>
                                <div id="filePreview" class="flex flex-wrap gap-4 mt-4"></div>
                            </div>
                        </div>
                        <!-- Footer Actions -->
                        <div class="flex justify-between items-center pt-8">
                            <button id="prevBtn" onclick="previousStep()"
                                class="text-secondary font-headline font-bold text-sm flex items-center gap-2 hover:text-primary transition-colors"
                                type="button" style="display: none;">
                                <span class="material-symbols-outlined" data-icon="arrow_back">arrow_back</span>
                                Anterior
                            </button>
                            <button id="nextBtn" onclick="nextStep()"
                                class="primary-gradient text-white px-10 py-4 rounded-lg font-headline font-extrabold text-sm tracking-widest uppercase shadow-xl hover:shadow-primary/20 transition-all"
                                type="button">
                                Siguiente Paso
                            </button>
                            <button id="submitBtn" onclick="submitForm(event)" style="display: none;"
                                class="primary-gradient text-white px-10 py-4 rounded-lg font-headline font-extrabold text-sm tracking-widest uppercase shadow-xl hover:shadow-primary/20 transition-all"
                                type="submit">
                                Formalizar Envío
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <!-- Success Toast -->
        <div id="successToast" class="toast max-w-md">
            <div
                class="bg-tertiary-fixed/30 border border-tertiary-fixed p-6 rounded-2xl flex items-center justify-between shadow-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-tertiary-fixed rounded-full flex items-center justify-center text-on-tertiary-fixed">
                        <span class="material-symbols-outlined" data-icon="check_circle"
                            style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-on-tertiary-fixed" id="referenceId">Referencia: LIVA-2024-8842</h4>
                        <p class="text-xs text-on-tertiary-fixed-variant">Este ID único rastrea su caso a través del ciclo de vida
                            de la administración.</p>
                    </div>
                </div>
                <button onclick="document.getElementById('successToast').classList.remove('show')"
                    class="text-on-tertiary-fixed font-headline font-bold text-xs border border-on-tertiary-fixed/20 px-4 py-2 rounded-full hover:bg-tertiary-fixed transition-colors">
                    Rastrear Progreso
                </button>
            </div>
        </div>
    </main>

    <!-- Report Lookup Modal -->
    <div id="reportModal" class="modal-overlay" onclick="closeReportModalOnOverlay(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-headline font-bold text-2xl text-primary">Consultar Reporte</h2>
                <button onclick="closeReportModal()" class="text-secondary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
            </div>

            <!-- Form to enter folio -->
            <div id="searchForm" class="space-y-4">
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Folio del Reporte</label>
                    <input id="folioInput" type="text" placeholder="Ej: LIVA-2026-1234"
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body"
                        onkeypress="if(event.key === 'Enter') searchReport()" />
                    <p class="text-xs text-secondary mt-1">Ingrese el folio proporcionado al momento de enviar su denuncia</p>
                </div>
                <button onclick="searchReport()"
                    class="w-full primary-gradient text-white px-6 py-3 rounded-lg font-headline font-bold text-sm tracking-widest uppercase shadow-xl hover:shadow-primary/20 transition-all">
                    Buscar Reporte
                </button>
            </div>

            <!-- Report result -->
            <div id="reportResult" style="display: none;" class="space-y-6 animate-fadeIn">
                <div class="bg-surface-container-low rounded-xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-secondary uppercase tracking-wider">Folio</p>
                            <p id="resultFolio" class="font-headline font-bold text-xl text-primary">LIVA-2026-1234</p>
                        </div>
                        <div id="resultStatus" class="status-badge status-en-revision">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">pending</span>
                            <span>En Revisión</span>
                        </div>
                    </div>

                    <div class="border-t border-outline-variant/20 pt-4 space-y-3">
                        <div>
                            <p class="text-xs text-secondary uppercase tracking-wider">Tipo de Denuncia</p>
                            <p id="resultTipo" class="font-medium text-on-surface">Impacto Ambiental</p>
                        </div>
                        <div>
                            <p class="text-xs text-secondary uppercase tracking-wider">Fecha de Registro</p>
                            <p id="resultFecha" class="font-medium text-on-surface">15 de Marzo, 2026</p>
                        </div>
                        <div>
                            <p class="text-xs text-secondary uppercase tracking-wider">Última Actualización</p>
                            <p id="resultActualizacion" class="font-medium text-on-surface">28 de Marzo, 2026</p>
                        </div>
                        <div>
                            <p class="text-xs text-secondary uppercase tracking-wider">Descripción del Estado</p>
                            <p id="resultDescripcion" class="text-sm text-on-surface-variant leading-relaxed">
                                Su denuncia ha sido recibida y se encuentra en proceso de revisión por nuestro equipo técnico.
                                Le notificaremos cualquier actualización.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Documento de Resolución (solo visible cuando estado es Resuelto) -->
                <div id="documentoResolucionContainer" style="display: none;" class="bg-tertiary-fixed/10 border-2 border-tertiary-fixed rounded-xl p-6 space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-tertiary-fixed rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-on-tertiary-fixed" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </div>
                        <div>
                            <h4 class="font-headline font-bold text-primary">Documento de Resolución Disponible</h4>
                            <p class="text-xs text-secondary">Su denuncia ha sido resuelta oficialmente</p>
                        </div>
                    </div>
                    
                    <div id="documentoResolucionDetalle" class="flex items-center justify-between gap-4 p-4 bg-surface-container-lowest rounded-lg border border-outline-variant/20">
                        <!-- El contenido se llenará dinámicamente con JavaScript -->
                    </div>
                </div>

                <button onclick="resetReportModal()"
                    class="w-full text-primary border-2 border-primary px-6 py-3 rounded-lg font-headline font-bold text-sm tracking-widest uppercase hover:bg-primary hover:text-white transition-all">
                    Buscar Otro Reporte
                </button>
            </div>

            <!-- Not found message -->
            <div id="notFoundMessage" style="display: none;" class="space-y-4">
                <div class="bg-error-container/20 border-l-4 border-error rounded-lg p-6 flex items-start gap-4">
                    <span class="material-symbols-outlined text-error text-3xl">error</span>
                    <div>
                        <h4 class="font-bold text-error mb-2">Reporte no encontrado</h4>
                        <p class="text-sm text-on-surface-variant">
                            No se encontró ningún reporte con el folio ingresado. Por favor verifique el número e intente nuevamente.
                        </p>
                    </div>
                </div>
                <button onclick="resetReportModal()"
                    class="w-full text-primary border-2 border-primary px-6 py-3 rounded-lg font-headline font-bold text-sm tracking-widest uppercase hover:bg-primary hover:text-white transition-all">
                    Intentar Nuevamente
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Información Legal -->
    <div id="legalInfoModal" class="modal-overlay" style="display: flex;" onclick="closeLegalModalOnOverlay(event)">
        <div class="modal-content max-w-3xl" onclick="event.stopPropagation()">
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-3xl" data-icon="gavel">gavel</span>
                    </div>
                    <div>
                        <h2 class="font-headline font-bold text-2xl text-primary">Derecho a un Medio Ambiente Adecuado</h2>
                        <p class="text-sm text-secondary">Marco Legal de Denuncias Ambientales</p>
                    </div>
                </div>
                <button onclick="closeLegalModal()" class="text-secondary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container-low/50 rounded-xl p-6 border-l-4 border-primary">
                    <div class="flex items-start gap-3 mb-4">
                        <span class="material-symbols-outlined text-primary text-xl flex-shrink-0 mt-1" data-icon="article">article</span>
                        <div class="text-sm text-on-surface leading-relaxed space-y-4">
                            <p>
                                Toda persona tiene derecho a un medio ambiente adecuado para su desarrollo, salud y bienestar. El Estado y los Municipios promoverán y garantizarán, en sus respectivos ámbitos de competencia, mejorar la calidad de vida y la productividad de las personas, a través de la protección al ambiente y la preservación, restauración y mejoramiento del equilibrio ecológico, de manera que no se comprometa la satisfacción de las necesidades de las generaciones futuras.
                            </p>
                            <p>
                                Toda persona, grupos sociales, organizaciones no gubernamentales, asociaciones y sociedades podrán denunciar ante la Secretaría u otras autoridades competentes todo acto u omisión que produzca o pueda producir desequilibrio ecológico o daños al ambiente o a los recursos naturales, o contravenga las disposiciones de la presente Ley, y de los demás ordenamientos que regulen las materias relacionadas con la protección al ambiente natural y la preservación y restauración del equilibrio ecológico.
                            </p>
                            <p>
                                Si la denuncia fuera presentada ante la autoridad estatal o municipal y resulta del orden federal, deberá ser remitida de manera inmediata para su atención y trámite a la Procuraduría Federal de Protección al Ambiente.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-tertiary-fixed/10 rounded-xl p-6 border border-tertiary-fixed/30">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="material-symbols-outlined text-tertiary-fixed text-xl" data-icon="info" style="font-variation-settings: 'FILL' 1;">info</span>
                        <h4 class="font-headline font-bold text-primary">Importante</h4>
                    </div>
                    <p class="text-sm text-on-surface-variant leading-relaxed">
                        Este sistema garantiza la confidencialidad de su información y facilita el seguimiento de su denuncia mediante un folio único. Sus datos personales son tratados conforme a la legislación aplicable en materia de protección de datos.
                    </p>
                </div>

                <div class="flex gap-4">
                    <button onclick="closeLegalModal()" class="flex-1 primary-gradient text-white px-6 py-4 rounded-lg font-headline font-bold text-sm tracking-widest uppercase shadow-xl hover:shadow-primary/20 transition-all">
                        Entendido, Continuar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mostrar el modal de información legal al cargar la página
        window.addEventListener('DOMContentLoaded', function() {
            // Verificar si el usuario ya vio el modal en esta sesión
            if (!sessionStorage.getItem('legalInfoShown')) {
                document.getElementById('legalInfoModal').style.display = 'flex';
                sessionStorage.setItem('legalInfoShown', 'true');
            }
        });

        // Funciones para cerrar el modal legal
        function closeLegalModal() {
            document.getElementById('legalInfoModal').style.display = 'none';
        }

        function closeLegalModalOnOverlay(event) {
            if (event.target.id === 'legalInfoModal') {
                closeLegalModal();
            }
        }

        // ─── Funciones para cargar dinámicamente temas y centros ───────────────────────
        async function cargarTemasDenuncia(idTipoDenuncia) {
            const temaDenunciaContainer = document.getElementById('temaDenunciaContainer');
            const temaDenunciaSelect = document.getElementById('temaDenuncia');
            const centroVerificacionContainer = document.getElementById('centroVerificacionContainer');
            const centroVerificacionSelect = document.getElementById('centroVerificacion');

            // Limpiar selects
            temaDenunciaSelect.innerHTML = '<option value="">Seleccione el tema...</option>';
            centroVerificacionSelect.innerHTML = '<option value="">Seleccione el centro de verificación...</option>';
            
            // Ocultar ambos contenedores inicialmente
            temaDenunciaContainer.style.display = 'none';
            centroVerificacionContainer.style.display = 'none';
            
            // Remover requerido de ambos campos
            temaDenunciaSelect.removeAttribute('required');
            centroVerificacionSelect.removeAttribute('required');

            if (!idTipoDenuncia) {
                return;
            }

            // Si es tipo 7 (Centros de verificación vehicular), cargar centros
            if (idTipoDenuncia === '7') {
                try {
                    const response = await fetch('<?= base_url('inicio/getCentrosVerificacion') ?>');
                    const result = await response.json();
                    
                    if (result.success && result.data.length > 0) {
                        result.data.forEach(centro => {
                            const option = document.createElement('option');
                            option.value = centro.clave;
                            option.textContent = `${centro.clave} - ${centro.municipio} - ${centro.direccion}`;
                            centroVerificacionSelect.appendChild(option);
                        });
                        centroVerificacionContainer.style.display = 'block';
                        centroVerificacionSelect.setAttribute('required', 'required');
                    }
                } catch (error) {
                    console.error('Error al cargar centros de verificación:', error);
                }
            } else {
                // Para otros tipos, cargar temas
                try {
                    const response = await fetch(`<?= base_url('inicio/getTemasPorTipo') ?>?id_tipo=${idTipoDenuncia}`);
                    const result = await response.json();
                    
                    if (result.success && result.data.length > 0) {
                        result.data.forEach(tema => {
                            const option = document.createElement('option');
                            option.value = tema.id_tema_denuncia;
                            option.textContent = tema.nombre;
                            temaDenunciaSelect.appendChild(option);
                        });
                        temaDenunciaContainer.style.display = 'block';
                        temaDenunciaSelect.setAttribute('required', 'required');
                    }
                } catch (error) {
                    console.error('Error al cargar temas de denuncia:', error);
                }
            }
        }
    </script>
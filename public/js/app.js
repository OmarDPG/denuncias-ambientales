// Global state
let currentStep = 1;
let uploadedFiles = [];
let uploadedFilesIdentificacion = [];
let map = null;
let marker = null;
let formData = {
    tipoPersona: '',
    nombreCompleto: '',
    genero: '',
    estado: '',
    municipio: '',
    colonia: '',
    codigoPostal: '',
    calle: '',
    numeroExterior: '',
    numeroInterior: '',
    email: '',
    telefono: '',
    esRepresentante: false,
    razonSocial: '',
    nombreRepresentante: '',
    idTipoDenuncia: '',
    tipoDenuncia: '',
    idTemaDenuncia: '',
    claveCvv: '',
    hechosDenunciados: '',
    latitude: null,
    longitude: null,
    nombreDenunciado: '',
    denunciadoEsMoral: false,
    razonSocialDenunciado: '',
    municipioDenunciado: '',
    coloniaDenunciado: '',
    calleDenunciado: '',
    codigoPostalDenunciado: '',
    numeroExteriorDenunciado: '',
    numeroInteriorDenunciado: '',
    files: []
};

// Initialize
document.addEventListener('DOMContentLoaded', function () {
    // Load draft if exists
    loadDraft();

    // Initialize character counter
    updateCharCounter();

    // Setup file upload
    setupFileUpload();
    setupFileUploadIdentificacion();

    // Initialize map when switching to step 2
    // Map will be initialized on first view of step 2
});

// Toggle Legal Representative Fields
function toggleLegalRepFields() {
    const checkbox = document.getElementById('esRepresentante');
    const legalRepFields = document.getElementById('legalRepFields');
    const razonSocial = document.getElementById('razonSocial');
    const nombreRepresentante = document.getElementById('nombreRepresentante');

    if (checkbox.checked) {
        legalRepFields.style.display = 'block';
        razonSocial.required = true;
        nombreRepresentante.required = true;
    } else {
        legalRepFields.style.display = 'none';
        razonSocial.required = false;
        nombreRepresentante.required = false;
        razonSocial.value = '';
        nombreRepresentante.value = '';
    }
}

// Toggle Denunciado Moral Fields
function toggleDenunciadoMoralFields() {
    const checkbox = document.getElementById('denunciadoEsMoral');
    const denunciadoMoralFields = document.getElementById('denunciadoMoralFields');
    const razonSocialDenunciado = document.getElementById('razonSocialDenunciado');

    if (checkbox.checked) {
        denunciadoMoralFields.style.display = 'block';
        razonSocialDenunciado.required = true;
    } else {
        denunciadoMoralFields.style.display = 'none';
        razonSocialDenunciado.required = false;
        razonSocialDenunciado.value = '';
    }
}

// Step Navigation
function goToStep(step) {
    if (step < currentStep || validateCurrentStep()) {
        currentStep = step;
        updateSteps();
    }
}

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < 3) {
            currentStep++;
            updateSteps();
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateSteps();
    }
}

function updateSteps() {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.remove('active');
    });

    // Show current step
    document.getElementById('step' + currentStep).classList.add('active');

    // Initialize map on step 2
    if (currentStep === 2 && !map) {
        setTimeout(() => initializeMap(), 100);
    }

    // Update indicators
    for (let i = 1; i <= 3; i++) {
        const indicator = document.getElementById('step' + i + 'Indicator');
        const circle = indicator.querySelector('div');
        const text = indicator.querySelector('span');

        if (i < currentStep) {
            // Completed step
            circle.className = 'w-8 h-8 rounded-full bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed text-xs font-bold';
            text.className = 'text-[10px] font-bold uppercase tracking-widest text-tertiary';
        } else if (i === currentStep) {
            // Current step
            circle.className = 'w-8 h-8 rounded-full primary-gradient flex items-center justify-center text-white text-xs font-bold';
            text.className = 'text-[10px] font-bold uppercase tracking-widest text-primary';
        } else {
            // Future step
            circle.className = 'w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant text-xs font-bold';
            text.className = 'text-[10px] font-bold uppercase tracking-widest text-secondary';
        }
    }

    // Update buttons
    document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'flex';
    document.getElementById('nextBtn').style.display = currentStep === 3 ? 'none' : 'flex';
    document.getElementById('submitBtn').style.display = currentStep === 3 ? 'flex' : 'none';

    // Scroll to top of form
    document.getElementById('formSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function validateCurrentStep() {
    if (currentStep === 1) {
        // Validar campos básicos
        const tipoPersona = document.getElementById('tipoPersona').value;
        const nombreCompleto = document.getElementById('nombreCompleto').value;
        const genero = document.getElementById('genero').value;
        const estado = document.getElementById('estado').value;
        const municipio = document.getElementById('municipio').value;
        const colonia = document.getElementById('colonia').value;
        const codigoPostal = document.getElementById('codigoPostal').value;
        const calle = document.getElementById('calle').value;
        const numeroExterior = document.getElementById('numeroExterior').value;
        const email = document.getElementById('email').value;
        const telefono = document.getElementById('telefono').value;

        if (!tipoPersona || !nombreCompleto || !genero || !estado || !municipio ||
            !colonia || !codigoPostal || !calle || !numeroExterior || !email || !telefono) {
            alert('Por favor complete todos los campos requeridos');
            return false;
        }

        // Validar campos de representante legal si está marcado
        const esRepresentante = document.getElementById('esRepresentante').checked;
        if (esRepresentante) {
            const razonSocial = document.getElementById('razonSocial').value;
            const nombreRepresentante = document.getElementById('nombreRepresentante').value;

            if (!razonSocial || !nombreRepresentante) {
                alert('Por favor complete los campos de representante legal');
                return false;
            }

            formData.razonSocial = razonSocial;
            formData.nombreRepresentante = nombreRepresentante;
        }

        // Guardar datos en formData
        formData.tipoPersona = tipoPersona;
        formData.nombreCompleto = nombreCompleto;
        formData.genero = genero;
        formData.estado = estado;
        formData.municipio = municipio;
        formData.colonia = colonia;
        formData.codigoPostal = codigoPostal;
        formData.calle = calle;
        formData.numeroExterior = numeroExterior;
        formData.numeroInterior = document.getElementById('numeroInterior').value;
        formData.email = email;
        formData.telefono = telefono;
        formData.esRepresentante = esRepresentante;
    } else if (currentStep === 2) {
        // Validar tipo de denuncia y hechos
        const tipoDenuncia = document.getElementById('tipoDenuncia').value;
        const hechosDenunciados = document.getElementById('hechosDenunciados').value;

        if (!tipoDenuncia || !hechosDenunciados) {
            alert('Por favor complete el tipo de denuncia y los hechos denunciados');
            return false;
        }

        // Validar campos condicionales según el tipo de denuncia
        if (tipoDenuncia === '7') {
            // Si es tipo 7, validar centro de verificación
            const claveCvv = document.getElementById('centroVerificacion').value;
            if (!claveCvv) {
                alert('Por favor seleccione un centro de verificación vehicular');
                return false;
            }
            formData.claveCvv = claveCvv;
            formData.idTemaDenuncia = ''; // Limpiar tema si es tipo 7
        } else {
            // Para otros tipos, validar tema de denuncia
            const temaDenuncia = document.getElementById('temaDenuncia').value;
            if (!temaDenuncia) {
                alert('Por favor seleccione un tema de denuncia');
                return false;
            }
            formData.idTemaDenuncia = temaDenuncia;
            formData.claveCvv = ''; // Limpiar centro si no es tipo 7
        }

        // Validar ubicación
        if (!formData.latitude || !formData.longitude) {
            alert('Por favor seleccione una ubicación en el mapa');
            return false;
        }

        // Guardar datos en formData
        formData.idTipoDenuncia = tipoDenuncia;
        // Obtener el texto del tipo de denuncia seleccionado
        const tipoDenunciaSelect = document.getElementById('tipoDenuncia');
        formData.tipoDenuncia = tipoDenunciaSelect.options[tipoDenunciaSelect.selectedIndex].text;
        formData.hechosDenunciados = hechosDenunciados;
    } else if (currentStep === 3) {
        // Validar campos del denunciado
        const nombreDenunciado = document.getElementById('nombreDenunciado').value;
        const municipioDenunciado = document.getElementById('municipioDenunciado').value;
        const coloniaDenunciado = document.getElementById('coloniaDenunciado').value;
        const calleDenunciado = document.getElementById('calleDenunciado').value;
        const codigoPostalDenunciado = document.getElementById('codigoPostalDenunciado').value;
        const numeroExteriorDenunciado = document.getElementById('numeroExteriorDenunciado').value;

        if (!nombreDenunciado || !municipioDenunciado || !coloniaDenunciado ||
            !calleDenunciado || !codigoPostalDenunciado || !numeroExteriorDenunciado) {
            alert('Por favor complete todos los campos requeridos del denunciado');
            return false;
        }

        // Validar campo razón social si es persona moral
        const denunciadoEsMoral = document.getElementById('denunciadoEsMoral').checked;
        if (denunciadoEsMoral) {
            const razonSocialDenunciado = document.getElementById('razonSocialDenunciado').value;
            if (!razonSocialDenunciado) {
                alert('Por favor complete la razón social del denunciado');
                return false;
            }
            formData.razonSocialDenunciado = razonSocialDenunciado;
        }

        // Guardar datos en formData
        formData.nombreDenunciado = nombreDenunciado;
        formData.denunciadoEsMoral = denunciadoEsMoral;
        formData.municipioDenunciado = municipioDenunciado;
        formData.coloniaDenunciado = coloniaDenunciado;
        formData.calleDenunciado = calleDenunciado;
        formData.codigoPostalDenunciado = codigoPostalDenunciado;
        formData.numeroExteriorDenunciado = numeroExteriorDenunciado;
        formData.numeroInteriorDenunciado = document.getElementById('numeroInteriorDenunciado').value;
    }

    return true;
}

// ─── Funciones del Modal de Previsualización ───────────────────────────────────
function showPreviewModal() {
    // Validar step 3 antes de mostrar el modal
    if (!validateCurrentStep()) {
        return;
    }

    // Recolectar todos los datos faltantes del step 3 si no están en formData
    if (!formData.nombreDenunciado) {
        formData.nombreDenunciado = document.getElementById('nombreDenunciado').value;
        formData.denunciadoEsMoral = document.getElementById('denunciadoEsMoral').checked;
        formData.municipioDenunciado = document.getElementById('municipioDenunciado').value;
        formData.coloniaDenunciado = document.getElementById('coloniaDenunciado').value;
        formData.calleDenunciado = document.getElementById('calleDenunciado').value;
        formData.codigoPostalDenunciado = document.getElementById('codigoPostalDenunciado').value;
        formData.numeroExteriorDenunciado = document.getElementById('numeroExteriorDenunciado').value;
        formData.numeroInteriorDenunciado = document.getElementById('numeroInteriorDenunciado').value;
        
        if (formData.denunciadoEsMoral) {
            formData.razonSocialDenunciado = document.getElementById('razonSocialDenunciado').value;
        }
    }

    // Poblar el modal con los datos
    populatePreviewModal();

    // Mostrar el modal
    document.getElementById('previewModal').style.display = 'flex';
}

function populatePreviewModal() {
    // Sección 1: Datos del Denunciante
    document.getElementById('prev_tipoPersona').textContent = formData.tipoPersona === 'fisica' ? 'Persona Física' : 'Persona Moral';
    document.getElementById('prev_nombreCompleto').textContent = formData.nombreCompleto;
    document.getElementById('prev_genero').textContent = formData.genero.charAt(0).toUpperCase() + formData.genero.slice(1);
    document.getElementById('prev_email').textContent = formData.email;
    document.getElementById('prev_telefono').textContent = formData.telefono;

    // Dirección del denunciante
    const direccion = `${formData.calle} ${formData.numeroExterior}${formData.numeroInterior ? ' Int. ' + formData.numeroInterior : ''}, Col. ${formData.colonia}, ${formData.municipio}, ${formData.estado}. CP: ${formData.codigoPostal}`;
    document.getElementById('prev_direccion').textContent = direccion;

    // Documentos de identificación
    if (uploadedFilesIdentificacion.length > 0) {
        document.getElementById('prev_identificacionContainer').style.display = 'block';
        const container = document.getElementById('prev_identificacionFiles');
        container.innerHTML = '';
        uploadedFilesIdentificacion.forEach(file => {
            const fileChip = document.createElement('div');
            fileChip.className = 'flex items-center gap-2 bg-primary/10 px-3 py-1 rounded-full';
            fileChip.innerHTML = `
                <span class="material-symbols-outlined text-primary text-sm">badge</span>
                <span class="text-xs text-primary font-medium">${file.name}</span>
            `;
            container.appendChild(fileChip);
        });
    }

    // Representante Legal
    if (formData.esRepresentante) {
        document.getElementById('prev_representanteContainer').style.display = 'block';
        document.getElementById('prev_razonSocial').textContent = formData.razonSocial;
        document.getElementById('prev_nombreRepresentante').textContent = formData.nombreRepresentante;
    } else {
        document.getElementById('prev_representanteContainer').style.display = 'none';
    }

    // Sección 2: Datos de la Denuncia
    document.getElementById('prev_tipoDenuncia').textContent = formData.tipoDenuncia;

    // Tema o Centro de Verificación
    if (formData.idTipoDenuncia === '7') {
        document.getElementById('prev_temaContainer').style.display = 'none';
        document.getElementById('prev_cvvContainer').style.display = 'block';
        const cvvSelect = document.getElementById('centroVerificacion');
        const cvvText = cvvSelect.options[cvvSelect.selectedIndex]?.text || 'N/A';
        document.getElementById('prev_centroVerificacion').textContent = cvvText;
    } else {
        document.getElementById('prev_cvvContainer').style.display = 'none';
        document.getElementById('prev_temaContainer').style.display = 'block';
        const temaSelect = document.getElementById('temaDenuncia');
        const temaText = temaSelect.options[temaSelect.selectedIndex]?.text || 'N/A';
        document.getElementById('prev_temaDenuncia').textContent = temaText;
    }

    document.getElementById('prev_hechosDenunciados').textContent = formData.hechosDenunciados;
    document.getElementById('prev_latitud').textContent = formData.latitude ? parseFloat(formData.latitude).toFixed(6) : 'N/A';
    document.getElementById('prev_longitud').textContent = formData.longitude ? parseFloat(formData.longitude).toFixed(6) : 'N/A';

    // Sección 3: Datos del Denunciado
    document.getElementById('prev_nombreDenunciado').textContent = formData.nombreDenunciado;

    if (formData.denunciadoEsMoral) {
        document.getElementById('prev_denunciadoMoralContainer').style.display = 'block';
        document.getElementById('prev_razonSocialDenunciado').textContent = formData.razonSocialDenunciado;
    } else {
        document.getElementById('prev_denunciadoMoralContainer').style.display = 'none';
    }

    // Dirección del denunciado
    const direccionDenunciado = `${formData.calleDenunciado} ${formData.numeroExteriorDenunciado}${formData.numeroInteriorDenunciado ? ' Int. ' + formData.numeroInteriorDenunciado : ''}, Col. ${formData.coloniaDenunciado}, ${formData.municipioDenunciado}. CP: ${formData.codigoPostalDenunciado}`;
    document.getElementById('prev_direccionDenunciado').textContent = direccionDenunciado;

    // Sección 4: Evidencias
    if (uploadedFiles.length > 0) {
        document.getElementById('prev_evidenciasContainer').style.display = 'block';
        const container = document.getElementById('prev_evidenciasFiles');
        container.innerHTML = '';
        
        uploadedFiles.forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'relative group';
            
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = file.name;
                img.className = 'w-24 h-24 object-cover rounded-lg border-2 border-outline-variant';
                fileItem.appendChild(img);
            } else {
                const pdfIcon = document.createElement('div');
                pdfIcon.className = 'w-24 h-24 bg-surface-container rounded-lg flex flex-col items-center justify-center border-2 border-outline-variant';
                pdfIcon.innerHTML = `
                    <span class="material-symbols-outlined text-3xl text-primary">picture_as_pdf</span>
                    <span class="text-xs text-secondary mt-1 text-center px-1">${file.name.substring(0, 10)}...</span>
                `;
                fileItem.appendChild(pdfIcon);
            }
            
            container.appendChild(fileItem);
        });
    } else {
        document.getElementById('prev_evidenciasContainer').style.display = 'none';
    }
}

function closePreviewModal() {
    document.getElementById('previewModal').style.display = 'none';
}

function closePreviewModalOnOverlay(event) {
    if (event.target.id === 'previewModal') {
        closePreviewModal();
    }
}

function editFormData() {
    // Cerrar el modal
    closePreviewModal();
    
    // Los datos ya están en los campos del formulario y en formData
    // El usuario puede navegar entre los steps para editar
    
    // Opcionalmente, regresar al step 1
    currentStep = 1;
    updateSteps();
}

function confirmAndSubmit() {
    // Deshabilitar botón para prevenir doble clic
    const confirmBtn = document.getElementById('confirmSubmitBtn');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="flex items-center justify-center gap-2"><span class="material-symbols-outlined animate-spin">progress_activity</span>Enviando...</span>';

    // Construir FormData con todos los campos recolectados
    const fd = new FormData();

    // Agregar token CSRF
    fd.append('csrf_test_name', getCsrfToken());

    // Paso 1 – Denunciante
    fd.append('tipo_persona',         formData.tipoPersona);
    fd.append('nombre_completo',      formData.nombreCompleto);
    fd.append('genero',               formData.genero);
    fd.append('estado',               formData.estado);
    fd.append('municipio',            formData.municipio);
    fd.append('colonia',              formData.colonia);
    fd.append('codigo_postal',        formData.codigoPostal);
    fd.append('calle',                formData.calle);
    fd.append('numero_exterior',      formData.numeroExterior);
    fd.append('numero_interior',      formData.numeroInterior || '');
    fd.append('email',                formData.email);
    fd.append('telefono',             formData.telefono);
    fd.append('es_representante',     formData.esRepresentante     ? 'true' : 'false');
    fd.append('razon_social',         formData.razonSocial         || '');
    fd.append('nombre_representante', formData.nombreRepresentante || '');

    // Paso 2 – Denuncia y ubicación    
    fd.append('id_tipo_denuncia',   formData.idTipoDenuncia);
    fd.append('id_tema_denuncia',   formData.idTemaDenuncia || '');
    fd.append('clave_cvv',          formData.claveCvv || '');
    fd.append('tipo_denuncia',      formData.tipoDenuncia);
    fd.append('hechos_denunciados', formData.hechosDenunciados);
    fd.append('latitud',            formData.latitude  ?? '');
    fd.append('longitud',           formData.longitude ?? '');

    // Paso 3 – Denunciado
    fd.append('nombre_denunciado',          formData.nombreDenunciado);
    fd.append('denunciado_es_moral',        formData.denunciadoEsMoral ? 'true' : 'false');
    fd.append('razon_social_denunciado',    formData.razonSocialDenunciado    || '');
    fd.append('municipio_denunciado',       formData.municipioDenunciado);
    fd.append('colonia_denunciado',         formData.coloniaDenunciado);
    fd.append('calle_denunciado',           formData.calleDenunciado);
    fd.append('codigo_postal_denunciado',   formData.codigoPostalDenunciado);
    fd.append('numero_exterior_denunciado', formData.numeroExteriorDenunciado);
    fd.append('numero_interior_denunciado', formData.numeroInteriorDenunciado || '');

    // Archivos de evidencia
    uploadedFiles.forEach(function (file) {
        fd.append('evidencias[]', file, file.name);
    });

    // Archivos de identificación
    uploadedFilesIdentificacion.forEach(function (file) {
        fd.append('identificacion[]', file, file.name);
    });

    fetch(document.getElementById('complaintForm').action, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken() },
        body:    fd,
    })
        .then(function (res) {
            if (!res.ok) { throw new Error('HTTP ' + res.status); }
            return res.json();
        })
        .then(function (data) {
            if (data.success) {
                // Cerrar modal de previsualización
                closePreviewModal();

                // ¿Necesita verificación OTP?
                if (data.necesita_verificacion) {
                    // Mostrar modal de verificación OTP
                    mostrarModalVerificacion(data.folio);
                } else {
                    // Flujo antiguo (por si acaso)
                    mostrarExitoFinal(data.folio);
                }
            } else {
                const errorMessages = Object.values(data.errors || {}).join('\n');
                alert('Error al enviar la denuncia:\n' + (errorMessages || 'Error desconocido.'));
            }
        })
        .catch(function (error) {
            console.error('Error:', error);
            alert('Error de conexión. Por favor intente nuevamente.');
        })
        .finally(function () {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<span class="flex items-center justify-center gap-2"><span class="material-symbols-outlined">send</span>Confirmar y Enviar</span>';
        });
}

// Map Interaction
function initializeMap() {
    if (map) return; // Already initialized

    // Initialize map centered on world view
    map = L.map('map').setView([20, 0], 2);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
        minZoom: 2
    }).addTo(map);

    // Custom marker icon
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: '<span class="material-symbols-outlined text-error text-5xl drop-shadow-lg" style="font-variation-settings: \'FILL\' 1;">location_on</span>',
        iconSize: [50, 50],
        iconAnchor: [25, 50]
    });

    // Add click event to map
    map.on('click', function (e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);

        updateMapMarker(lat, lng);
    });
}

function updateMapMarker(lat, lng) {
    // Remove existing marker if any
    if (marker) {
        map.removeLayer(marker);
    }

    // Create custom icon
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: '<span class="material-symbols-outlined text-error text-5xl drop-shadow-lg" style="font-variation-settings: \'FILL\' 1; display: block; margin-top: -40px;">location_on</span>',
        iconSize: [50, 50],
        iconAnchor: [25, 50]
    });

    // Add new marker
    marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);

    // Update form fields
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    formData.latitude = lat;
    formData.longitude = lng;
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);

                // Update marker and center map
                updateMapMarker(lat, lng);
                map.setView([lat, lng], 15);

                // Show success message
                showNotification('Location set to your current position', 'success');
            },
            function (error) {
                showNotification('Unable to get your location. Please click on the map instead.', 'error');
            }
        );
    } else {
        showNotification('Geolocation is not supported by your browser', 'error');
    }
}

function showNotification(message, type) {
    // Create a temporary toast notification
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-8 left-1/2 transform -translate-x-1/2 z-[10000] px-6 py-3 rounded-lg shadow-2xl animate-bounce';

    if (type === 'success') {
        notification.classList.add('bg-tertiary-fixed', 'text-on-tertiary-fixed');
        notification.innerHTML = `
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            <span class="font-bold">${message}</span>
          </div>
        `;
    } else {
        notification.classList.add('bg-error-container', 'text-error');
        notification.innerHTML = `
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined">error</span>
            <span class="font-bold">${message}</span>
          </div>
        `;
    }

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// File Upload
function setupFileUpload() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    // Drag and drop
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('bg-surface-container');
    });

    dropZone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dropZone.classList.remove('bg-surface-container');
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('bg-surface-container');
        handleFiles(e.dataTransfer.files);
    });

    // File input change
    fileInput.addEventListener('change', function (e) {
        handleFiles(e.target.files);
    });
}

function setupFileUploadIdentificacion() {
    const dropZone = document.getElementById('dropZoneIdentificacion');
    const fileInput = document.getElementById('fileInputIdentificacion');

    // Drag and drop
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('bg-surface-container');
    });

    dropZone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dropZone.classList.remove('bg-surface-container');
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('bg-surface-container');
        handleFilesIdentificacion(e.dataTransfer.files);
    });

    // File input change
    fileInput.addEventListener('change', function (e) {
        handleFilesIdentificacion(e.target.files);
    });
}

function handleFiles(files) {
    const maxSize = 25 * 1024 * 1024; // 25MB
    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'];

    Array.from(files).forEach(file => {
        if (file.size > maxSize) {
            alert(`${file.name} is too large. Maximum size is 25MB.`);
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            alert(`${file.name} is not a supported format. Please upload PNG, JPG, or PDF.`);
            return;
        }

        uploadedFiles.push(file);
        displayFilePreview(file);
    });

    formData.files = uploadedFiles;
}

function handleFilesIdentificacion(files) {
    const maxSize = 10 * 1024 * 1024; // 10MB
    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'];

    // Solo permitir un archivo de identificación
    if (uploadedFilesIdentificacion.length >= 1) {
        alert('Solo puede subir un documento de identificación. Elimine el anterior si desea cambiarlo.');
        return;
    }

    Array.from(files).forEach(file => {
        if (uploadedFilesIdentificacion.length >= 1) {
            return;
        }

        if (file.size > maxSize) {
            alert(`${file.name} es demasiado grande. El tamaño máximo es 10MB.`);
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            alert(`${file.name} no es un formato soportado. Por favor suba PNG, JPG, o PDF.`);
            return;
        }

        uploadedFilesIdentificacion.push(file);
        displayFilePreviewIdentificacion(file);
    });
}

function displayFilePreview(file) {
    const preview = document.getElementById('filePreview');
    const fileDiv = document.createElement('div');
    fileDiv.className = 'file-preview';

    if (file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = file.name;
        fileDiv.appendChild(img);
    } else {
        const pdfIcon = document.createElement('div');
        pdfIcon.className = 'w-24 h-24 bg-surface-container rounded-lg flex items-center justify-center';
        pdfIcon.innerHTML = '<span class="material-symbols-outlined text-4xl">picture_as_pdf</span>';
        fileDiv.appendChild(pdfIcon);
    }

    const removeBtn = document.createElement('button');
    removeBtn.innerHTML = '�-';
    removeBtn.onclick = function () {
        uploadedFiles = uploadedFiles.filter(f => f !== file);
        formData.files = uploadedFiles;
        fileDiv.remove();
    };
    fileDiv.appendChild(removeBtn);

    preview.appendChild(fileDiv);
}

function displayFilePreviewIdentificacion(file) {
    const preview = document.getElementById('filePreviewIdentificacion');
    const fileDiv = document.createElement('div');
    fileDiv.className = 'file-preview relative';

    if (file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = file.name;
        img.className = 'w-32 h-32 object-cover rounded-lg border-2 border-primary';
        fileDiv.appendChild(img);
    } else {
        const pdfIcon = document.createElement('div');
        pdfIcon.className = 'w-32 h-32 bg-surface-container rounded-lg flex flex-col items-center justify-center border-2 border-primary';
        pdfIcon.innerHTML = '<span class="material-symbols-outlined text-4xl text-primary">picture_as_pdf</span><span class="text-xs text-secondary mt-2">' + file.name + '</span>';
        fileDiv.appendChild(pdfIcon);
    }

    const removeBtn = document.createElement('button');
    removeBtn.className = 'absolute -top-2 -right-2 w-8 h-8 bg-error text-white rounded-full flex items-center justify-center hover:bg-error/80 transition-colors';
    removeBtn.innerHTML = '<span class="material-symbols-outlined text-sm">close</span>';
    removeBtn.type = 'button';
    removeBtn.onclick = function () {
        uploadedFilesIdentificacion = uploadedFilesIdentificacion.filter(f => f !== file);
        fileDiv.remove();
    };
    fileDiv.appendChild(removeBtn);

    preview.appendChild(fileDiv);
}

// �"?�"?�"? Helpers de seguridad �"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?

// Lee el token CSRF desde la cookie que emite CodeIgniter
function getCsrfToken() {
    const cookieName = 'csrf_cookie_name';
    for (const raw of document.cookie.split(';')) {
        const c = raw.trim();
        if (c.startsWith(cookieName + '=')) {
            return decodeURIComponent(c.substring(cookieName.length + 1));
        }
    }
    return '';
}

// Devuelve la base URL definida en el meta tag por PHP
function getBaseUrl() {
    return document.querySelector('meta[name="base-url"]')?.content ?? '/';
}

// �"?�"?�"? Envío del formulario (AJAX) �"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?�"?
function submitForm(event) {
    event.preventDefault();

    if (!validateCurrentStep()) {
        return;
    }

    // Construir FormData con todos los campos recolectados por el JS
    const fd = new FormData();

    // Agregar token CSRF
    fd.append('csrf_test_name', getCsrfToken());

    // Paso 1 �?" Denunciante
    fd.append('tipo_persona',         formData.tipoPersona);
    fd.append('nombre_completo',      formData.nombreCompleto);
    fd.append('genero',               formData.genero);
    fd.append('estado',               formData.estado);
    fd.append('municipio',            formData.municipio);
    fd.append('colonia',              formData.colonia);
    fd.append('codigo_postal',        formData.codigoPostal);
    fd.append('calle',                formData.calle);
    fd.append('numero_exterior',      formData.numeroExterior);
    fd.append('numero_interior',      formData.numeroInterior || '');
    fd.append('email',                formData.email);
    fd.append('telefono',             formData.telefono);
    fd.append('es_representante',     formData.esRepresentante     ? 'true' : 'false');
    fd.append('razon_social',         formData.razonSocial         || '');
    fd.append('nombre_representante', formData.nombreRepresentante || '');

    // Paso 2 �?" Denuncia y ubicación    
    fd.append('id_tipo_denuncia',   formData.idTipoDenuncia);
    fd.append('id_tema_denuncia',   formData.idTemaDenuncia || '');
    fd.append('clave_cvv',          formData.claveCvv || '');    fd.append('tipo_denuncia',      formData.tipoDenuncia);
    fd.append('hechos_denunciados', formData.hechosDenunciados);
    fd.append('latitud',            formData.latitude  ?? '');
    fd.append('longitud',           formData.longitude ?? '');

    // Paso 3 �?" Denunciado
    fd.append('nombre_denunciado',          formData.nombreDenunciado);
    fd.append('denunciado_es_moral',        formData.denunciadoEsMoral ? 'true' : 'false');
    fd.append('razon_social_denunciado',    formData.razonSocialDenunciado    || '');
    fd.append('municipio_denunciado',       formData.municipioDenunciado);
    fd.append('colonia_denunciado',         formData.coloniaDenunciado);
    fd.append('calle_denunciado',           formData.calleDenunciado);
    fd.append('codigo_postal_denunciado',   formData.codigoPostalDenunciado);
    fd.append('numero_exterior_denunciado', formData.numeroExteriorDenunciado);
    fd.append('numero_interior_denunciado', formData.numeroInteriorDenunciado || '');

    // Archivos de evidencia
    uploadedFiles.forEach(function (file) {
        fd.append('evidencias[]', file, file.name);
    });

    // Archivos de identificación
    uploadedFilesIdentificacion.forEach(function (file) {
        fd.append('identificacion[]', file, file.name);
    });

    // Prevenir doble envío
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled    = true;
    submitBtn.textContent = 'Enviando...';

    fetch(document.getElementById('complaintForm').action, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken() },
        body:    fd,
    })
        .then(function (res) {
            if (!res.ok) { throw new Error('HTTP ' + res.status); }
            return res.json();
        })
        .then(function (data) {
            if (data.success) {
                // Mostrar toast con folio real del servidor
                document.getElementById('referenceId').textContent = 'Referencia: ' + data.folio;
                document.getElementById('successToast').classList.add('show');

                // Resetear formulario
                document.getElementById('complaintForm').reset();
                uploadedFiles = [];
                uploadedFilesIdentificacion = [];
                document.getElementById('filePreview').innerHTML                 = '';
                document.getElementById('filePreviewIdentificacion').innerHTML   = '';
                document.getElementById('legalRepFields').style.display          = 'none';
                document.getElementById('denunciadoMoralFields').style.display   = 'none';

                if (marker) { map.removeLayer(marker); marker = null; }
                document.getElementById('latitude').value  = '';
                document.getElementById('longitude').value = '';
                formData.latitude  = null;
                formData.longitude = null;

                currentStep = 1;
                updateSteps();
                localStorage.removeItem('complaintDraft');

                setTimeout(function () {
                    document.getElementById('successToast').classList.remove('show');
                }, 6000);
            } else {
                const errorMessages = Object.values(data.errors || {}).join('\n');
                alert('Error al enviar la denuncia:\n' + (errorMessages || 'Error desconocido.'));
            }
        })
        .catch(function () {
            alert('Error de conexión. Por favor intente nuevamente.');
        })
        .finally(function () {
            submitBtn.disabled    = false;
            submitBtn.textContent = 'Enviar Denuncia';
        });
}

// Draft Management
function saveDraft() {
    const draft = {
        tipoPersona: document.getElementById('tipoPersona').value,
        nombreCompleto: document.getElementById('nombreCompleto').value,
        genero: document.getElementById('genero').value,
        estado: document.getElementById('estado').value,
        municipio: document.getElementById('municipio').value,
        colonia: document.getElementById('colonia').value,
        codigoPostal: document.getElementById('codigoPostal').value,
        calle: document.getElementById('calle').value,
        numeroExterior: document.getElementById('numeroExterior').value,
        numeroInterior: document.getElementById('numeroInterior').value,
        email: document.getElementById('email').value,
        telefono: document.getElementById('telefono').value,
        esRepresentante: document.getElementById('esRepresentante').checked,
        razonSocial: document.getElementById('razonSocial').value,
        nombreRepresentante: document.getElementById('nombreRepresentante').value,
        tipoDenuncia: document.getElementById('tipoDenuncia').value,
        hechosDenunciados: document.getElementById('hechosDenunciados').value,
        nombreDenunciado: document.getElementById('nombreDenunciado').value,
        denunciadoEsMoral: document.getElementById('denunciadoEsMoral').checked,
        razonSocialDenunciado: document.getElementById('razonSocialDenunciado').value,
        municipioDenunciado: document.getElementById('municipioDenunciado').value,
        coloniaDenunciado: document.getElementById('coloniaDenunciado').value,
        calleDenunciado: document.getElementById('calleDenunciado').value,
        codigoPostalDenunciado: document.getElementById('codigoPostalDenunciado').value,
        numeroExteriorDenunciado: document.getElementById('numeroExteriorDenunciado').value,
        numeroInteriorDenunciado: document.getElementById('numeroInteriorDenunciado').value,
        latitude: formData.latitude,
        longitude: formData.longitude,
        step: currentStep
    };

    localStorage.setItem('complaintDraft', JSON.stringify(draft));

    // Show temporary notification
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="material-symbols-outlined" data-icon="check">check</span> Guardado!';
    setTimeout(() => {
        btn.innerHTML = originalText;
    }, 2000);
}

function loadDraft() {
    const draft = localStorage.getItem('complaintDraft');
    if (draft) {
        const data = JSON.parse(draft);

        if (confirm('¿Desea continuar con su borrador guardado?')) {
            // Cargar datos personales
            document.getElementById('tipoPersona').value = data.tipoPersona || '';
            document.getElementById('nombreCompleto').value = data.nombreCompleto || '';
            document.getElementById('genero').value = data.genero || '';
            document.getElementById('estado').value = data.estado || '';
            document.getElementById('municipio').value = data.municipio || '';
            document.getElementById('colonia').value = data.colonia || '';
            document.getElementById('codigoPostal').value = data.codigoPostal || '';
            document.getElementById('calle').value = data.calle || '';
            document.getElementById('numeroExterior').value = data.numeroExterior || '';
            document.getElementById('numeroInterior').value = data.numeroInterior || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('telefono').value = data.telefono || '';

            // Cargar checkbox y campos de representante legal
            if (data.esRepresentante) {
                document.getElementById('esRepresentante').checked = true;
                toggleLegalRepFields();
                document.getElementById('razonSocial').value = data.razonSocial || '';
                document.getElementById('nombreRepresentante').value = data.nombreRepresentante || '';
            }

            // Cargar datos de la denuncia
            document.getElementById('tipoDenuncia').value = data.tipoDenuncia || '';
            document.getElementById('hechosDenunciados').value = data.hechosDenunciados || '';
            updateCharCounter(); // Update character counter after loading draft

            // Cargar datos del denunciado
            document.getElementById('nombreDenunciado').value = data.nombreDenunciado || '';
            document.getElementById('municipioDenunciado').value = data.municipioDenunciado || '';
            document.getElementById('coloniaDenunciado').value = data.coloniaDenunciado || '';
            document.getElementById('calleDenunciado').value = data.calleDenunciado || '';
            document.getElementById('codigoPostalDenunciado').value = data.codigoPostalDenunciado || '';
            document.getElementById('numeroExteriorDenunciado').value = data.numeroExteriorDenunciado || '';
            document.getElementById('numeroInteriorDenunciado').value = data.numeroInteriorDenunciado || '';

            // Cargar checkbox y campos de persona moral del denunciado
            if (data.denunciadoEsMoral) {
                document.getElementById('denunciadoEsMoral').checked = true;
                toggleDenunciadoMoralFields();
                document.getElementById('razonSocialDenunciado').value = data.razonSocialDenunciado || '';
            }

            if (data.latitude && data.longitude) {
                formData.latitude = data.latitude;
                formData.longitude = data.longitude;
                document.getElementById('latitude').value = data.latitude;
                document.getElementById('longitude').value = data.longitude;

                // Update map marker if on step 2
                if (currentStep === 2 && map) {
                    updateMapMarker(data.latitude, data.longitude);
                    map.setView([data.latitude, data.longitude], 13);
                }
            }

            if (data.step) {
                currentStep = data.step;
                updateSteps();
            }
        } else {
            localStorage.removeItem('complaintDraft');
        }
    }
}

// Character Counter for Hechos Denunciados
function updateCharCounter() {
    const textarea = document.getElementById('hechosDenunciados');
    const counter = document.getElementById('charCounter');
    const currentLength = textarea.value.length;
    const maxLength = 1000;
    
    counter.textContent = `${currentLength}/${maxLength} caracteres`;
    
    // Change color as limit approaches
    if (currentLength >= maxLength * 0.9) {
        counter.classList.add('text-error');
        counter.classList.remove('text-secondary');
    } else {
        counter.classList.add('text-secondary');
        counter.classList.remove('text-error');
    }
}

// Report Modal Functions
function openReportModal() {
    document.getElementById('reportModal').classList.add('show');
    // Focus on input
    setTimeout(() => {
        document.getElementById('folioInput').focus();
    }, 100);
}

function closeReportModal() {
    document.getElementById('reportModal').classList.remove('show');
    resetReportModal();
}

function closeReportModalOnOverlay(event) {
    if (event.target.id === 'reportModal') {
        closeReportModal();
    }
}

function resetReportModal() {
    // Hide results and show search form
    document.getElementById('reportResult').style.display = 'none';
    document.getElementById('notFoundMessage').style.display = 'none';
    document.getElementById('documentoResolucionContainer').style.display = 'none';
    document.getElementById('searchForm').style.display = 'block';
    document.getElementById('folioInput').value = '';
}

function searchReport() {
    const folio = document.getElementById('folioInput').value.trim().toUpperCase();

    if (!folio) {
        alert('Por favor ingrese un folio');
        return;
    }

    const url = new URL('inicio/buscarReporte', getBaseUrl());
    url.searchParams.set('folio', folio);

    fetch(url.toString(), {
        method:  'GET',
        headers: { 'Accept': 'application/json' },
    })
        .then(function (res) {
            if (!res.ok) { throw new Error('HTTP ' + res.status); }
            return res.json();
        })
        .then(function (data) {
            console.log('Datos recibidos del servidor:', data); // Log de depuración
            
            if (data.found) {
                document.getElementById('resultFolio').textContent          = data.folio;
                document.getElementById('resultTipo').textContent           = data.tipo_denuncia;
                document.getElementById('resultFecha').textContent          = data.fecha_captura;
                document.getElementById('resultActualizacion').textContent  = data.fecha_actualizacion;
                document.getElementById('resultDescripcion').textContent    = data.notas_internas || 'Consulte con el personal de atención para más detalles sobre el estado de su denuncia.';

                const statusBadge = document.getElementById('resultStatus');
                statusBadge.className = 'status-badge status-' + data.estatus.class;
                statusBadge.innerHTML =
                    `<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">${data.estatus.icon}</span>` +
                    `<span>${data.estatus.text}</span>`;

                // Mostrar documento de resolución si existe
                const documentoContainer = document.getElementById('documentoResolucionContainer');
                const documentoDetalle = document.getElementById('documentoResolucionDetalle');
                
                console.log('Documento de resolución:', data.documento_resolucion); // Log de depuración
                
                if (data.documento_resolucion) {
                    const doc = data.documento_resolucion;
                    
                    // Determinar icono según tipo de archivo
                    let iconName = 'description';
                    if (doc.tipo.includes('pdf')) iconName = 'picture_as_pdf';
                    else if (doc.tipo.includes('image')) iconName = 'image';
                    
                    // Formatear tamaño
                    const formatBytes = (bytes) => {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                    };
                    
                    const urlDescarga = getBaseUrl() + `inicio/descargarDocumentoResolucion/${doc.id}?download=1`;
                    const urlVer = getBaseUrl() + `inicio/descargarDocumentoResolucion/${doc.id}`;
                    const canPreview = doc.tipo.includes('pdf') || doc.tipo.includes('image');
                    
                    documentoDetalle.innerHTML = `
                        <div class="flex items-center gap-3 flex-1">
                            <span class="material-symbols-outlined text-primary text-2xl">${iconName}</span>
                            <div class="flex-1">
                                <p class="font-medium text-on-surface text-sm">${doc.nombre}</p>
                                <p class="text-xs text-secondary mt-1">${formatBytes(doc.peso)} • Oficio de Resolución</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            ${canPreview ? `
                                <a href="${urlVer}" 
                                   target="_blank"
                                   class="flex items-center gap-1 px-3 py-2 bg-surface-container text-primary border border-outline-variant/30 rounded-lg text-xs font-bold hover:bg-surface-container-high transition-all"
                                   title="Abrir en nueva pestaña">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    Ver
                                </a>
                            ` : ''}
                            <a href="${urlDescarga}" 
                               class="flex items-center gap-1 px-3 py-2 bg-primary text-white rounded-lg text-xs font-bold hover:opacity-90 transition-all"
                               title="Descargar documento">
                                <span class="material-symbols-outlined text-sm">download</span>
                                Descargar
                            </a>
                        </div>
                    `;
                    
                    documentoContainer.style.display = 'block';
                } else {
                    documentoContainer.style.display = 'none';
                }

                document.getElementById('searchForm').style.display   = 'none';
                document.getElementById('reportResult').style.display = 'block';
            } else {
                document.getElementById('searchForm').style.display      = 'none';
                document.getElementById('notFoundMessage').style.display = 'block';
            }
        })
        .catch(function () {
            alert('Error de conexión. Por favor intente nuevamente.');
        });
}

// ─── Funciones del Modal de Verificación OTP ───────────────────────────────────

function mostrarModalVerificacion(folio) {
    // Guardar folio en variable global
    window.folioActual = folio;
    
    // Mostrar el modal
    document.getElementById('verificacionModal').style.display = 'flex';
    
    // Actualizar el folio en el modal
    document.getElementById('folioVerificacion').textContent = folio;
    
    // Limpiar campo de código
    document.getElementById('codigoOTP').value = '';
    document.getElementById('codigoOTP').disabled = false;
    document.getElementById('codigoOTP').focus();
    
    // Limpiar mensajes de error previos
    document.getElementById('errorVerificacion').style.display = 'none';
    
    // Habilitar botones
    document.getElementById('btnVerificar').disabled = false;
    document.getElementById('btnReenviar').disabled = false;
}

function verificarCodigoOTP() {
    const codigo = document.getElementById('codigoOTP').value.trim();
    const folio = window.folioActual;
    
    // Validación básica
    if (!/^\d{6}$/.test(codigo)) {
        mostrarErrorVerificacion('El código debe ser de 6 dígitos');
        return;
    }
    
    // Deshabilitar botón
    const btnVerificar = document.getElementById('btnVerificar');
    btnVerificar.disabled = true;
    btnVerificar.innerHTML = '<span class="flex items-center justify-center gap-2"><span class="material-symbols-outlined animate-spin">progress_activity</span>Verificando...</span>';
    
    // Enviar al backend
    fetch(getBaseUrl() + 'inicio/verificarCodigo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: new URLSearchParams({
            folio: folio,
            codigo: codigo
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // ✅ Verificación exitosa
            cerrarModalVerificacion();
            mostrarExitoFinal(folio);
        } else {
            // ❌ Error
            mostrarErrorVerificacion(data.message);
            
            // Si está bloqueado, deshabilitar input
            if (data.codigo_bloqueado) {
                document.getElementById('codigoOTP').disabled = true;
                document.getElementById('btnVerificar').disabled = true;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarErrorVerificacion('Error de conexión. Intenta nuevamente.');
    })
    .finally(() => {
        btnVerificar.disabled = false;
        btnVerificar.innerHTML = '<span class="flex items-center justify-center gap-2"><span class="material-symbols-outlined">verified</span>Verificar Código</span>';
    });
}

function reenviarCodigoOTP() {
    const folio = window.folioActual;
    const btnReenviar = document.getElementById('btnReenviar');
    
    btnReenviar.disabled = true;
    btnReenviar.innerHTML = '<span class="flex items-center justify-center gap-2"><span class="material-symbols-outlined animate-spin">progress_activity</span>Reenviando...</span>';
    
    fetch(getBaseUrl() + 'inicio/reenviarCodigo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: new URLSearchParams({ folio: folio })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Código reenviado. Revisa tu correo.', 'success');
            // Limpiar campo y errores
            document.getElementById('codigoOTP').value = '';
            document.getElementById('codigoOTP').disabled = false;
            document.getElementById('btnVerificar').disabled = false;
            document.getElementById('errorVerificacion').style.display = 'none';
        } else {
            mostrarErrorVerificacion(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarErrorVerificacion('Error al reenviar el código');
    })
    .finally(() => {
        btnReenviar.disabled = false;
        btnReenviar.innerHTML = '<span class="flex items-center justify-center gap-2"><span class="material-symbols-outlined">mail</span>Reenviar Código</span>';
    });
}

function mostrarErrorVerificacion(mensaje) {
    const errorDiv = document.getElementById('errorVerificacion');
    const errorTexto = document.getElementById('errorVerificacionTexto');
    if (errorTexto) {
        errorTexto.textContent = mensaje;
    } else {
        errorDiv.textContent = mensaje;
    }
    errorDiv.style.display = 'flex';
}

function cerrarModalVerificacion() {
    document.getElementById('verificacionModal').style.display = 'none';
}

function cerrarModalVerificacionOnOverlay(event) {
    // No permitir cerrar haciendo clic fuera (usuario debe verificar)
    // if (event.target.id === 'verificacionModal') {
    //     cerrarModalVerificacion();
    // }
}

function mostrarExitoFinal(folio) {
    // Mostrar toast con folio
    document.getElementById('referenceId').textContent = 'Referencia: ' + folio;
    document.getElementById('successToast').classList.add('show');

    // Resetear formulario
    document.getElementById('complaintForm').reset();
    uploadedFiles = [];
    uploadedFilesIdentificacion = [];
    document.getElementById('filePreview').innerHTML = '';
    document.getElementById('filePreviewIdentificacion').innerHTML = '';
    document.getElementById('legalRepFields').style.display = 'none';
    document.getElementById('denunciadoMoralFields').style.display = 'none';

    if (marker) { 
        map.removeLayer(marker); 
        marker = null; 
    }
    document.getElementById('latitude').value = '';
    document.getElementById('longitude').value = '';
    formData.latitude = null;
    formData.longitude = null;

    currentStep = 1;
    updateSteps();
    localStorage.removeItem('complaintDraft');

    setTimeout(function () {
        document.getElementById('successToast').classList.remove('show');
    }, 8000);
}

// Auto-formatear el código OTP mientras se escribe
document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.getElementById('codigoOTP');
    if (codigoInput) {
        // Solo permitir dígitos
        codigoInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
            
            // Limitar a 6 dígitos
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Permitir pegar código
        codigoInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const codigo = pastedText.replace(/\D/g, '').slice(0, 6);
            this.value = codigo;
        });

        // Permitir Enter para verificar
        codigoInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.length === 6) {
                verificarCodigoOTP();
            }
        });
    }
});



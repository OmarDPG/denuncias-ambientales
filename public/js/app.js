// Global state
let currentStep = 1;
let uploadedFiles = [];
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
    tipoDenuncia: '',
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

    // Setup file upload
    setupFileUpload();

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

        // Validar ubicación
        if (!formData.latitude || !formData.longitude) {
            alert('Por favor seleccione una ubicación en el mapa');
            return false;
        }

        // Guardar datos en formData
        formData.tipoDenuncia = tipoDenuncia;
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
    fd.append('tipo_denuncia',      formData.tipoDenuncia);
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
                document.getElementById('filePreview').innerHTML                 = '';
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


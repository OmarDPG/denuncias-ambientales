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
    removeBtn.innerHTML = '×';
    removeBtn.onclick = function () {
        uploadedFiles = uploadedFiles.filter(f => f !== file);
        formData.files = uploadedFiles;
        fileDiv.remove();
    };
    fileDiv.appendChild(removeBtn);

    preview.appendChild(fileDiv);
}

// Form Submission
function submitForm(event) {
    event.preventDefault();

    if (!validateCurrentStep()) {
        return;
    }

    // Generate reference ID
    const refId = 'LIVA-' + new Date().getFullYear() + '-' + Math.floor(Math.random() * 10000);

    // In a real app, this would send data to server
    console.log('Submitting form:', formData);

    // Show success toast
    document.getElementById('referenceId').textContent = 'Referencia: ' + refId;
    const toast = document.getElementById('successToast');
    toast.classList.add('show');

    // Reset form
    setTimeout(function () {
        document.getElementById('complaintForm').reset();
        uploadedFiles = [];
        document.getElementById('filePreview').innerHTML = '';

        // Hide legal rep fields after reset
        document.getElementById('legalRepFields').style.display = 'none';
        document.getElementById('denunciadoMoralFields').style.display = 'none';

        // Remove marker from map
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }

        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        currentStep = 1;
        updateSteps();
        localStorage.removeItem('complaintDraft');

        // Hide toast after 5 seconds
        setTimeout(function () {
            toast.classList.remove('show');
        }, 5000);
    }, 2000);
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
    document.getElementById('searchForm').style.display = 'block';
    document.getElementById('folioInput').value = '';
}

function searchReport() {
    const folio = document.getElementById('folioInput').value.trim();

    // Validate folio format
    if (!folio) {
        alert('Por favor ingrese un folio');
        return;
    }

    // Simulate different report statuses based on folio
    const mockReports = {
        'LIVA-2026-1234': {
            folio: 'LIVA-2026-1234',
            tipo: 'Impacto Ambiental',
            fecha: '15 de Marzo, 2026',
            actualizacion: '28 de Marzo, 2026',
            status: 'en-revision',
            statusText: 'En Revisión',
            statusIcon: 'pending',
            descripcion: 'Su denuncia ha sido recibida y se encuentra en proceso de revisión por nuestro equipo técnico. Le notificaremos cualquier actualización.'
        },
        'LIVA-2026-5678': {
            folio: 'LIVA-2026-5678',
            tipo: 'Contaminación Atmosférica',
            fecha: '10 de Marzo, 2026',
            actualizacion: '29 de Marzo, 2026',
            status: 'en-proceso',
            statusText: 'En Proceso',
            statusIcon: 'schedule',
            descripcion: 'Su denuncia está siendo investigada activamente. El personal técnico ha realizado una visita de inspección al sitio reportado.'
        },
        'LIVA-2026-9999': {
            folio: 'LIVA-2026-9999',
            tipo: 'Residuos de Manejo Especial',
            fecha: '1 de Marzo, 2026',
            actualizacion: '30 de Marzo, 2026',
            status: 'resuelta',
            statusText: 'Resuelta',
            statusIcon: 'check_circle',
            descripcion: 'Su denuncia ha sido atendida satisfactoriamente. Se han tomado las medidas necesarias y el expediente ha sido cerrado.'
        },
        'LIVA-2026-0001': {
            folio: 'LIVA-2026-0001',
            tipo: 'Ordenamiento Territorial',
            fecha: '5 de Febrero, 2026',
            actualizacion: '10 de Febrero, 2026',
            status: 'recibida',
            statusText: 'Recibida',
            statusIcon: 'inbox',
            descripcion: 'Su denuncia ha sido registrada exitosamente y será asignada a un inspector en breve.'
        }
    };

    const report = mockReports[folio.toUpperCase()];

    if (report) {
        // Show report data
        document.getElementById('resultFolio').textContent = report.folio;
        document.getElementById('resultTipo').textContent = report.tipo;
        document.getElementById('resultFecha').textContent = report.fecha;
        document.getElementById('resultActualizacion').textContent = report.actualizacion;
        document.getElementById('resultDescripcion').textContent = report.descripcion;

        // Update status badge
        const statusBadge = document.getElementById('resultStatus');
        statusBadge.className = 'status-badge status-' + report.status;
        statusBadge.innerHTML = `
          <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">${report.statusIcon}</span>
          <span>${report.statusText}</span>
        `;

        // Hide search form and show result
        document.getElementById('searchForm').style.display = 'none';
        document.getElementById('reportResult').style.display = 'block';
    } else {
        // Show not found message
        document.getElementById('searchForm').style.display = 'none';
        document.getElementById('notFoundMessage').style.display = 'block';
    }
}



//Scripts para manejo del modal
// Datos de ejemplo simulando una base de datos
const complaintsDatabase = {
    'ARC-9821': {
        folio: '#ARC-9821',
        fechaReporte: 'Oct 24, 2023',
        estado: { text: 'Critical', class: 'bg-error-container text-on-error-container' },
        denunciante: {
            tipoPersona: 'Persona Física',
            nombre: 'Juan Pérez García',
            genero: 'Masculino',
            email: 'juan.perez@example.com',
            telefono: '2221234567',
            direccion: {
                calle: 'Calle 5 de Mayo',
                numeroExterior: '123',
                numeroInterior: '',
                colonia: 'Centro',
                municipio: 'Puebla',
                estado: 'Puebla',
                codigoPostal: '72000'
            },
            representanteLegal: null
        },
        denuncia: {
            categoria: { text: 'Contaminación de Agua', icon: 'water_drop' },
            ubicacion: 'Silver Creek Watershed',
            coordenadas: { latitud: '19.0414', longitud: '-98.2063' },
            hechos: 'Se ha detectado contaminación severa en el cuerpo de agua debido a descargas industriales no autorizadas. El agua presenta coloración anormal y olor fétido. Los habitantes de la zona reportan mortandad de peces y fauna acuática. La situación requiere atención inmediata y análisis de calidad del agua.'
        },
        denunciado: {
            nombre: 'Industrias Químicas del Centro S.A. de C.V.',
            esMoral: true,
            razonSocial: 'Industrias Químicas del Centro S.A. de C.V.',
            direccion: {
                calle: 'Av. Industrial',
                numeroExterior: '456',
                numeroInterior: 'Nave 3',
                colonia: 'Parque Industrial',
                municipio: 'Puebla',
                codigoPostal: '72220'
            }
        },
        evidencias: []
    },
    'ARC-9745': {
        folio: '#ARC-9745',
        fechaReporte: 'Oct 22, 2023',
        estado: { text: 'Solved', class: 'bg-tertiary-fixed text-on-tertiary-fixed-variant' },
        denunciante: {
            tipoPersona: 'Persona Física',
            nombre: 'María González López',
            genero: 'Femenino',
            email: 'maria.gonzalez@example.com',
            telefono: '2229876543',
            direccion: {
                calle: 'Calle Reforma',
                numeroExterior: '89',
                numeroInterior: 'A',
                colonia: 'La Paz',
                municipio: 'Puebla',
                estado: 'Puebla',
                codigoPostal: '72160'
            },
            representanteLegal: null
        },
        denuncia: {
            categoria: { text: 'Tala Ilegal', icon: 'forest' },
            ubicacion: 'North Canopy Reserve',
            coordenadas: { latitud: '19.1234', longitud: '-98.3456' },
            hechos: 'Se ha observado tala indiscriminada de árboles en zona protegida durante las últimas dos semanas. Se han talado aproximadamente 50 árboles de especies nativas sin ningún permiso visible. El área afectada es de aproximadamente 2 hectáreas.'
        },
        denunciado: {
            nombre: 'Pedro Martínez Sánchez',
            esMoral: false,
            direccion: {
                calle: 'Carretera Federal',
                numeroExterior: 'Km 12',
                numeroInterior: '',
                colonia: 'San Miguel',
                municipio: 'Cholula',
                codigoPostal: '72810'
            }
        },
        evidencias: []
    },
    'ARC-9612': {
        folio: '#ARC-9612',
        fechaReporte: 'Oct 21, 2023',
        estado: { text: 'Pendiente', class: 'bg-secondary-container text-on-secondary-container' },
        denunciante: {
            tipoPersona: 'Persona Moral',
            nombre: 'Asociación Civil Ambiental Puebla A.C.',
            genero: 'N/A',
            email: 'contacto@acap.org',
            telefono: '2225551234',
            direccion: {
                calle: 'Boulevard Atlixco',
                numeroExterior: '2505',
                numeroInterior: 'Piso 3',
                colonia: 'La Paz',
                municipio: 'Puebla',
                estado: 'Puebla',
                codigoPostal: '72160'
            },
            representanteLegal: {
                razonSocial: 'Asociación Civil Ambiental Puebla A.C.',
                nombreRepresentante: 'Lic. Roberto Flores Méndez'
            }
        },
        denuncia: {
            categoria: { text: 'Contaminación Atmosférica', icon: 'factory' },
            ubicacion: 'Industrial Zone B',
            coordenadas: { latitud: '19.0521', longitud: '-98.2198' },
            hechos: 'Emisiones de humo negro constante desde hace 3 meses. El humo tiene olor químico fuerte que afecta a los vecinos de la zona. No se observan filtros ni chimeneas adecuadas. Los residentes reportan problemas respiratorios.'
        },
        denunciado: {
            nombre: 'Quien resulte responsable',
            esMoral: false,
            direccion: {
                calle: 'Calle Industrial',
                numeroExterior: 's/n',
                numeroInterior: '',
                colonia: 'Zona Industrial',
                municipio: 'Puebla',
                codigoPostal: '72220'
            }
        },
        evidencias: []
    },
    'ARC-9588': {
        folio: '#ARC-9588',
        fechaReporte: 'Oct 19, 2023',
        estado: { text: 'Approved', class: 'bg-primary-fixed text-on-primary-fixed-variant' },
        denunciante: {
            tipoPersona: 'Persona Física',
            nombre: 'Carlos Ramírez Torres',
            genero: 'Masculino',
            email: 'carlos.ramirez@example.com',
            telefono: '2223334455',
            direccion: {
                calle: 'Avenida Juárez',
                numeroExterior: '1234',
                numeroInterior: '',
                colonia: 'Centro',
                municipio: 'Puebla',
                estado: 'Puebla',
                codigoPostal: '72000'
            },
            representanteLegal: null
        },
        denuncia: {
            categoria: { text: 'Caza Furtiva', icon: 'pets' },
            ubicacion: 'Eastern Steppe',
            coordenadas: { latitud: '19.0789', longitud: '-98.1234' },
            hechos: 'Se ha detectado actividad de caza ilegal en zona protegida. Se encontraron trampas para animales silvestres y evidencia de captura de especies en peligro de extinción. Los cazadores operan principalmente durante la noche.'
        },
        denunciado: {
            nombre: 'Quien resulte responsable',
            esMoral: false,
            direccion: {
                calle: 'N/A',
                numeroExterior: 'N/A',
                numeroInterior: '',
                colonia: 'N/A',
                municipio: 'Puebla',
                codigoPostal: 'N/A'
            }
        },
        evidencias: []
    }
};

// Función para abrir el modal con los detalles
function openDetailModal(folio) {
    const data = complaintsDatabase[folio];
    if (!data) {
        console.error('Denuncia no encontrada:', folio);
        return;
    }

    // Llenar información del reporte
    document.getElementById('modalFolio').textContent = data.folio;
    document.getElementById('modalFechaReporte').textContent = data.fechaReporte;
    document.getElementById('modalEstado').textContent = data.estado.text;
    document.getElementById('modalEstado').className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${data.estado.class}`;

    // Llenar datos del denunciante
    document.getElementById('modalTipoPersona').textContent = data.denunciante.tipoPersona;
    document.getElementById('modalNombreDenunciante').textContent = data.denunciante.nombre;
    document.getElementById('modalGenero').textContent = data.denunciante.genero;
    document.getElementById('modalEmail').textContent = data.denunciante.email;
    document.getElementById('modalTelefono').textContent = data.denunciante.telefono;

    // Construir dirección del denunciante
    const dirDen = data.denunciante.direccion;
    const direccionDenunciante = `${dirDen.calle} #${dirDen.numeroExterior}${dirDen.numeroInterior ? ' Int. ' + dirDen.numeroInterior : ''}, Col. ${dirDen.colonia}, ${dirDen.municipio}, ${dirDen.estado}, CP ${dirDen.codigoPostal}`;
    document.getElementById('modalDireccionDenunciante').textContent = direccionDenunciante;

    // Mostrar/ocultar sección de representante legal
    const repSection = document.getElementById('modalRepresentanteSection');
    if (data.denunciante.representanteLegal) {
        repSection.style.display = 'block';
        document.getElementById('modalRazonSocial').textContent = data.denunciante.representanteLegal.razonSocial;
        document.getElementById('modalNombreRepresentante').textContent = data.denunciante.representanteLegal.nombreRepresentante;
    } else {
        repSection.style.display = 'none';
    }

    // Llenar detalles de la denuncia
    document.getElementById('modalCategoria').textContent = data.denuncia.categoria.text;
    document.getElementById('modalCategoriaIcon').textContent = data.denuncia.categoria.icon;
    document.getElementById('modalUbicacion').textContent = data.denuncia.ubicacion;
    document.getElementById('modalLatitud').textContent = data.denuncia.coordenadas.latitud;
    document.getElementById('modalLongitud').textContent = data.denuncia.coordenadas.longitud;
    document.getElementById('modalHechosDenunciados').textContent = data.denuncia.hechos;

    // Llenar datos del denunciado
    document.getElementById('modalNombreDenunciado').textContent = data.denunciado.nombre;

    const razonSocialDiv = document.getElementById('modalRazonSocialDenunciadoDiv');
    if (data.denunciado.esMoral && data.denunciado.razonSocial) {
        razonSocialDiv.style.display = 'block';
        document.getElementById('modalRazonSocialDenunciado').textContent = data.denunciado.razonSocial;
    } else {
        razonSocialDiv.style.display = 'none';
    }

    // Construir dirección del denunciado
    const dirDenun = data.denunciado.direccion;
    const direccionDenunciado = `${dirDenun.calle} #${dirDenun.numeroExterior}${dirDenun.numeroInterior ? ' Int. ' + dirDenun.numeroInterior : ''}, Col. ${dirDenun.colonia}, ${dirDenun.municipio}, CP ${dirDenun.codigoPostal}`;
    document.getElementById('modalDireccionDenunciado').textContent = direccionDenunciado;

    // Evidencias (vacío por ahora)
    const evidenciasContainer = document.getElementById('modalEvidencias');
    if (data.evidencias && data.evidencias.length > 0) {
        evidenciasContainer.innerHTML = data.evidencias.map(ev => `
          <div class="aspect-square rounded-lg overflow-hidden border border-outline-variant/20">
            <img src="${ev}" alt="Evidencia" class="w-full h-full object-cover">
          </div>
        `).join('');
    } else {
        evidenciasContainer.innerHTML = '<div class="col-span-full text-center text-secondary text-sm py-8">No hay evidencias adjuntas</div>';
    }

    // Mostrar el modal
    document.getElementById('complaintDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Función para cerrar el modal
function closeDetailModal() {
    document.getElementById('complaintDetailModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Función para cerrar modal al hacer clic fuera
function closeDetailModalOnOverlay(event) {
    if (event.target.id === 'complaintDetailModal') {
        closeDetailModal();
    }
}

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('complaintDetailModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeDetailModal();
        }
    }
});







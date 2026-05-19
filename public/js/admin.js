// Base de datos de denuncias (simulada)
const complaintsDatabase = {
  'ARC-9821': {
    folio: 'ARC-9821',
    status: 'critical',
    statusText: 'Critical',
    denunciante: {
      tipoPersona: 'Persona Natural',
      nombre: 'María González Pérez',
      genero: 'Femenino',
      email: 'maria.gonzalez@email.com',
      telefono: '+52 555 123 4567',
      direccion: {
        asentamiento: 'Colonia Centro',
        calle: 'Avenida Juárez',
        numeroExterior: '245',
        numeroInterior: 'Depto. 3B',
        codigoPostal: '06000',
        localidad: 'Cuauhtémoc',
        municipio: 'Ciudad de México',
        estado: 'Ciudad de México'
      },
      representanteLegal: 'N/A'
    },
    denuncia: {
      tipoDenuncia: 'Contaminación de Agua',
      hechos: 'Se ha observado vertimiento de sustancias químicas no identificadas en el río local durante las últimas dos semanas. El agua ha cambiado de color a un tono turbio y presenta un olor químico fuerte. La fauna acuática ha disminuido notablemente.',
      lat: '19.4326',
      lon: '-99.1332'
    },
    denunciado: {
      nombreEntidad: 'Fábrica Textil del Norte S.A. de C.V.',
      nombreRepresentante: 'Carlos Ramírez López',
      direccion: {
        asentamiento: 'Parque Industrial Norte',
        calle: 'Calle Manufactura',
        numeroExterior: '789',
        codigoPostal: '06100',
        localidad: 'Cuauhtémoc',
        municipio: 'Ciudad de México',
        estado: 'Ciudad de México'
      }
    },
    evidencias: [
      { nombre: 'foto-rio-contaminado-1.jpg', tipo: 'imagen' },
      { nombre: 'video-vertimiento.mp4', tipo: 'video' }
    ],
    fecha: '15 Dic, 2024'
  },
  'ARC-9745': {
    folio: 'ARC-9745',
    status: 'in-review',
    statusText: 'In Review',
    denunciante: {
      tipoPersona: 'Persona Moral',
      nombre: 'Asociación de Vecinos Unidos A.C.',
      genero: 'N/A',
      email: 'contacto@vecinosunidos.org',
      telefono: '+52 555 987 6543',
      direccion: {
        asentamiento: 'Colonia Roma Norte',
        calle: 'Calle Orizaba',
        numeroExterior: '123',
        numeroInterior: '',
        codigoPostal: '06700',
        localidad: 'Cuauhtémoc',
        municipio: 'Ciudad de México',
        estado: 'Ciudad de México'
      },
      representanteLegal: 'Lic. Roberto Sánchez Torres'
    },
    denuncia: {
      tipoDenuncia: 'Emisión de Ruidos Excesivos',
      hechos: 'Establecimiento comercial ubicado en zona residencial emite ruidos de música a alto volumen después de las 10 PM, violando reglamentos locales. Esto ha afectado la calidad de vida de más de 30 familias en la zona.',
      lat: '19.4150',
      lon: '-99.1540'
    },
    denunciado: {
      nombreEntidad: 'Bar y Restaurante La Fiesta',
      nombreRepresentante: 'Ana Patricia Méndez',
      direccion: {
        asentamiento: 'Colonia Roma Norte',
        calle: 'Calle Tonalá',
        numeroExterior: '56',
        codigoPostal: '06700',
        localidad: 'Cuauhtémoc',
        municipio: 'Ciudad de México',
        estado: 'Ciudad de México'
      }
    },
    evidencias: [
      { nombre: 'medicion-decibelios.pdf', tipo: 'documento' },
      { nombre: 'audio-ruido-nocturno.mp3', tipo: 'audio' }
    ],
    fecha: '12 Dic, 2024'
  },
  'ARC-9612': {
    folio: 'ARC-9612',
    status: 'pending',
    statusText: 'Pending',
    denunciante: {
      tipoPersona: 'Persona Natural',
      nombre: 'Jorge Luis Martínez',
      genero: 'Masculino',
      email: 'jlmartinez@email.com',
      telefono: '+52 555 234 5678',
      direccion: {
        asentamiento: 'Colonia Polanco',
        calle: 'Avenida Presidente Masaryk',
        numeroExterior: '456',
        numeroInterior: 'Piso 8',
        codigoPostal: '11560',
        localidad: 'Miguel Hidalgo',
        municipio: 'Ciudad de México',
        estado: 'Ciudad de México'
      },
      representanteLegal: 'N/A'
    },
    denuncia: {
      tipoDenuncia: 'Tala Ilegal de Árboles',
      hechos: 'Se ha detectado la tala no autorizada de aproximadamente 15 árboles centenarios en zona protegida. Los árboles fueron cortados durante la noche sin permiso de las autoridades competentes.',
      lat: '19.4370',
      lon: '-99.1920'
    },
    denunciado: {
      nombreEntidad: 'Constructora Desarrollos Urbanos S.A.',
      nombreRepresentante: 'Ing. Fernando Gutiérrez',
      direccion: {
        asentamiento: 'Colonia Polanco',
        calle: 'Calle Emilio Castelar',
        numeroExterior: '234',
        codigoPostal: '11560',
        localidad: 'Miguel Hidalgo',
        municipio: 'Ciudad de México',
        estado: 'Ciudad de México'
      }
    },
    evidencias: [
      { nombre: 'fotos-arboles-talados.zip', tipo: 'archivo' },
      { nombre: 'reporte-ambiental.pdf', tipo: 'documento' }
    ],
    fecha: '10 Dic, 2024'
  },
  'ARC-9588': {
    folio: 'ARC-9588',
    status: 'solved',
    statusText: 'Solved',
    denunciante: {
      tipoPersona: 'Persona Natural',
      nombre: 'Laura Elizabeth Hernández',
      genero: 'Femenino',
      email: 'laura.hernandez@email.com',
      telefono: '+52 555 876 5432',
      direccion: {
        asentamiento: 'Colonia Condesa',
        calle: 'Avenida México',
        numeroExterior: '89',
        numeroInterior: '',
        codigoPostal: '06100',
        localidad: 'Cuauhtémoc',
        municipio: 'Ciudad de México',
        estado: 'Ciudad de México'
      },
      representanteLegal: 'N/A'
    },
    denuncia: {
      tipoDenuncia: 'Manejo Inadecuado de Residuos',
      hechos: 'Empresa de alimentos no está realizando la separación adecuada de residuos orgánicos e inorgánicos, generando acumulación de basura y focos de infección en la vía pública.',
      lat: '19.4112',
      lon: '-99.1710'
    },
    denunciado: {
      nombreEntidad: 'Comedor La Esquina',
      nombreRepresentante: 'José Luis Pérez',
      direccion: {
        asentamiento: 'Colonia Condesa',
        calle: 'Calle Tamaulipas',
        numeroExterior: '12',
        codigoPostal: '06100',
        localidad: 'Cuauhtémoc',
        municipio: 'Ciudad de México',
        estado: 'Ciudad de México'
      }
    },
    evidencias: [
      { nombre: 'foto-acumulacion-basura.jpg', tipo: 'imagen' }
    ],
    fecha: '08 Dic, 2024'
  }
};

// Función para abrir modal de detalles
function openDetailModal(folio) {
  const complaint = complaintsDatabase[folio];
  if (!complaint) {
    console.error('Denuncia no encontrada:', folio);
    return;
  }

  // Actualizar folio en el header
  document.getElementById('detailModalFolio').textContent = `Folio: #${folio}`;

  // Actualizar información del denunciante
  document.getElementById('detailTipoPersona').textContent = complaint.denunciante.tipoPersona;
  document.getElementById('detailNombre').textContent = complaint.denunciante.nombre;
  document.getElementById('detailGenero').textContent = complaint.denunciante.genero;
  document.getElementById('detailEmail').textContent = complaint.denunciante.email;
  document.getElementById('detailTelefono').textContent = complaint.denunciante.telefono;
  
  const dir = complaint.denunciante.direccion;
  document.getElementById('detailDireccion').textContent = 
    `${dir.calle} ${dir.numeroExterior}${dir.numeroInterior ? ' Int. ' + dir.numeroInterior : ''}, ${dir.asentamiento}, CP ${dir.codigoPostal}, ${dir.municipio}, ${dir.estado}`;
  
  document.getElementById('detailRepresentante').textContent = complaint.denunciante.representanteLegal;

  // Actualizar información de la denuncia
  document.getElementById('detailTipoDenuncia').textContent = complaint.denuncia.tipoDenuncia;
  document.getElementById('detailHechos').textContent = complaint.denuncia.hechos;
  document.getElementById('detailCoordenadas').textContent = `Lat: ${complaint.denuncia.lat}, Lon: ${complaint.denuncia.lon}`;

  // Actualizar información del denunciado
  document.getElementById('detailNombreEntidad').textContent = complaint.denunciado.nombreEntidad;
  document.getElementById('detailNombreRepresentante').textContent = complaint.denunciado.nombreRepresentante;
  
  const dirDenunciado = complaint.denunciado.direccion;
  document.getElementById('detailDireccionDenunciado').textContent = 
    `${dirDenunciado.calle} ${dirDenunciado.numeroExterior}, ${dirDenunciado.asentamiento}, CP ${dirDenunciado.codigoPostal}, ${dirDenunciado.municipio}, ${dirDenunciado.estado}`;

  // Actualizar lista de evidencias
  const evidenciasContainer = document.getElementById('detailEvidencias');
  evidenciasContainer.innerHTML = '';
  
  if (complaint.evidencias && complaint.evidencias.length > 0) {
    complaint.evidencias.forEach((evidencia, index) => {
      const evidenciaItem = document.createElement('div');
      evidenciaItem.className = 'flex items-center gap-2 text-sm text-on-surface';
      evidenciaItem.innerHTML = `
        <span class="material-symbols-outlined text-primary text-lg">attachment</span>
        <span>${evidencia.nombre}</span>
      `;
      evidenciasContainer.appendChild(evidenciaItem);
    });
  } else {
    evidenciasContainer.innerHTML = '<p class="text-secondary text-sm">No se adjuntaron evidencias</p>';
  }

  // Mostrar el modal
  document.getElementById('complaintDetailModal').classList.remove('hidden');
}

// Función para cerrar modal de detalles
function closeDetailModal() {
  document.getElementById('complaintDetailModal').classList.add('hidden');
}

// Cerrar modal al hacer clic en el overlay
function closeDetailModalOnOverlay(event) {
  if (event.target.id === 'complaintDetailModal') {
    closeDetailModal();
  }
}

// Variable global para almacenar el folio actual en edición
let currentEditingFolio = null;

// Función para abrir modal de cambio de estado
function openStatusModal(folio) {
  const complaint = complaintsDatabase[folio];
  if (!complaint) {
    console.error('Denuncia no encontrada:', folio);
    return;
  }

  // Almacenar el folio actual
  currentEditingFolio = folio;

  // Actualizar folio en el header
  document.getElementById('statusModalFolio').textContent = `Folio: #${folio}`;

  // Actualizar badge de estado actual
  const statusBadge = document.getElementById('currentStatusBadge');
  const statusConfig = getStatusConfig(complaint.status);
  statusBadge.textContent = statusConfig.text;
  statusBadge.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${statusConfig.class}`;

  // Resetear el formulario
  document.getElementById('changeStatusForm').reset();

  // Mostrar el modal
  document.getElementById('changeStatusModal').classList.remove('hidden');
}

// Función para cerrar modal de cambio de estado
function closeStatusModal() {
  document.getElementById('changeStatusModal').classList.add('hidden');
  document.getElementById('changeStatusForm').reset();
  currentEditingFolio = null;
}

// Cerrar modal de estado al hacer clic en el overlay
function closeStatusModalOnOverlay(event) {
  if (event.target.id === 'changeStatusModal') {
    closeStatusModal();
  }
}

// Función para guardar cambio de estado
function saveStatusChange() {
  const newStatus = document.getElementById('newStatus').value;
  const resolutionNotes = document.getElementById('resolutionNotes').value;
  const resolutionDate = document.getElementById('resolutionDate').value;

  // Validar campos requeridos
  if (!newStatus) {
    alert('Por favor, seleccione un nuevo estado.');
    return;
  }

  if (!resolutionNotes.trim()) {
    alert('Por favor, proporcione comentarios o resolución.');
    return;
  }

  if (!currentEditingFolio) {
    alert('Error: No se pudo identificar la denuncia.');
    return;
  }

  // Aquí iría la llamada a la API para guardar el cambio
  // Por ahora, solo actualizamos la base de datos local
  const complaint = complaintsDatabase[currentEditingFolio];
  complaint.status = newStatus;
  complaint.statusText = getStatusConfig(newStatus).text;

  console.log('Cambio de estado guardado:', {
    folio: currentEditingFolio,
    newStatus: newStatus,
    resolutionNotes: resolutionNotes,
    resolutionDate: resolutionDate || 'No especificada'
  });

  // Mostrar mensaje de éxito
  alert(`Estado actualizado exitosamente para la denuncia #${currentEditingFolio}\n\nNuevo estado: ${getStatusConfig(newStatus).text}\n\nSe ha enviado una notificación al denunciante.`);

  // Cerrar el modal
  closeStatusModal();

  // Aquí podrías actualizar la tabla sin recargar la página
  // updateComplaintTableRow(currentEditingFolio, newStatus);
}

// Función auxiliar para obtener configuración de estado
function getStatusConfig(status) {
  const configs = {
    'pending': { text: 'Pendiente', class: 'bg-surface-container-high text-on-surface' },
    'in-review': { text: 'En Revisión', class: 'bg-tertiary-container text-on-tertiary-container' },
    'approved': { text: 'Aprobado', class: 'bg-secondary-container text-on-secondary-container' },
    'rejected': { text: 'Rechazado', class: 'bg-error-container/50 text-error' },
    'solved': { text: 'Resuelto', class: 'bg-primary-container text-primary' },
    'critical': { text: 'Crítico', class: 'bg-error-container text-on-error-container' }
  };
  return configs[status] || configs['pending'];
}

// Cerrar modales con la tecla Escape
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeDetailModal();
    closeStatusModal();
  }
});

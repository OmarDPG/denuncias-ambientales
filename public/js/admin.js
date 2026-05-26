// Base de datos de denuncias (simulada / se extiende con datos reales desde PHP)
let complaintsDatabase = {

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
      evidenciaItem.className = 'flex items-center justify-between gap-4 p-4 bg-surface-container-low rounded-lg border border-outline-variant/20 hover:bg-surface-container transition-all';
      
      // Determinar icono según tipo de archivo
      let iconName = 'attachment';
      let canPreview = false;
      
      if (evidencia.tipo.includes('image')) {
        iconName = 'image';
        canPreview = true;
      } else if (evidencia.tipo.includes('pdf')) {
        iconName = 'picture_as_pdf';
        canPreview = true;
      } else if (evidencia.tipo.includes('video')) {
        iconName = 'video_file';
      }
      
      // Formatear tamaño de archivo
      const formatBytes = (bytes) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
      };
      
      const urlEvidencia = `${BASE_URL}admin/verEvidencia/${evidencia.id}`;
      const urlDescarga = `${BASE_URL}admin/verEvidencia/${evidencia.id}?download=1`;
      
      evidenciaItem.innerHTML = `
        <div class="flex items-center gap-3 flex-1">
          <span class="material-symbols-outlined text-primary text-2xl">${iconName}</span>
          <div class="flex-1">
            <p class="font-medium text-on-surface text-sm">${evidencia.nombre}</p>
            <p class="text-xs text-secondary mt-1">${formatBytes(evidencia.peso)} • ${evidencia.tipo}</p>
          </div>
        </div>
        <div class="flex gap-2">
          ${canPreview ? `
            <a href="${urlEvidencia}" 
               target="_blank"
               class="flex items-center gap-1 px-3 py-2 bg-surface-container text-primary border border-outline-variant/30 rounded-lg text-xs font-bold hover:bg-surface-container-high transition-all"
               title="Abrir en nueva pestaña">
              <span class="material-symbols-outlined text-sm">visibility</span>
              Ver
            </a>
          ` : ''}
          <a href="${urlDescarga}" 
             class="flex items-center gap-1 px-3 py-2 bg-primary text-white rounded-lg text-xs font-bold hover:opacity-90 transition-all"
             title="Descargar archivo">
            <span class="material-symbols-outlined text-sm">download</span>
            Descargar
          </a>
        </div>
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

  // Sincronizar campo oculto del formulario
  const hiddenId = document.getElementById('statusIdDenuncia');
  if (hiddenId) hiddenId.value = folio;

  // Actualizar folio en el header
  document.getElementById('statusModalFolio').textContent = `Folio: #${folio}`;

  // Actualizar badge de estado actual
  const statusBadge = document.getElementById('currentStatusBadge');
  const statusConfig = getStatusConfig(complaint.status);
  statusBadge.textContent = statusConfig.text;
  statusBadge.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${statusConfig.class}`;

  // Resetear el formulario y re-sincronizar el campo oculto tras el reset
  document.getElementById('changeStatusForm').reset();
  if (hiddenId) hiddenId.value = folio;
  
  // Ocultar campo de documentación por defecto
  document.getElementById('resolutionDocsField').style.display = 'none';
  
  // Agregar listener al select de estado
  const selectEstado = document.getElementById('newStatus');
  selectEstado.addEventListener('change', toggleResolutionDocsField);
  
  // Agregar listener para vista previa de archivos
  const docsInput = document.getElementById('resolutionDocs');
  docsInput.addEventListener('change', previewResolutionDocs);

  // Mostrar el modal
  document.getElementById('changeStatusModal').classList.remove('hidden');
}

// Función para mostrar vista previa de archivos seleccionados
function previewResolutionDocs(event) {
  const files = event.target.files;
  const preview = document.getElementById('resolutionDocsPreview');
  preview.innerHTML = '';
  
  if (!files || files.length === 0) return;
  
  Array.from(files).forEach((file, index) => {
    const fileItem = document.createElement('div');
    fileItem.className = 'flex items-center justify-between gap-3 p-3 bg-surface-container rounded-lg border border-outline-variant/20';
    
    let iconName = 'description';
    if (file.type.includes('pdf')) iconName = 'picture_as_pdf';
    else if (file.type.includes('image')) iconName = 'image';
    
    const formatBytes = (bytes) => {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    };
    
    fileItem.innerHTML = `
      <div class="flex items-center gap-2 flex-1">
        <span class="material-symbols-outlined text-primary text-xl">${iconName}</span>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-on-surface text-xs truncate">${file.name}</p>
          <p class="text-xs text-secondary">${formatBytes(file.size)}</p>
        </div>
      </div>
      <span class="material-symbols-outlined text-tertiary text-sm">check_circle</span>
    `;
    
    preview.appendChild(fileItem);
  });
}

// Función para mostrar/ocultar campo de documentación según estado
function toggleResolutionDocsField() {
  const selectedStatus = document.getElementById('newStatus').value;
  const docsField = document.getElementById('resolutionDocsField');
  const docsInput = document.getElementById('resolutionDocs');
  
  if (selectedStatus === 'Resuelta') {
    docsField.style.display = 'block';
    docsInput.required = true;
  } else {
    docsField.style.display = 'none';
    docsInput.required = false;
    docsInput.value = ''; // Limpiar archivos seleccionados
    document.getElementById('resolutionDocsPreview').innerHTML = '';
  }
}

// Función para cerrar modal de cambio de estado
function closeStatusModal() {
  document.getElementById('changeStatusModal').classList.add('hidden');
  document.getElementById('changeStatusForm').reset();
  document.getElementById('resolutionDocsField').style.display = 'none';
  document.getElementById('resolutionDocsPreview').innerHTML = '';
  
  // Remover listener
  const selectEstado = document.getElementById('newStatus');
  selectEstado.removeEventListener('change', toggleResolutionDocsField);
  
  currentEditingFolio = null;
}

// Cerrar modal de estado al hacer clic en el overlay
function closeStatusModalOnOverlay(event) {
  if (event.target.id === 'changeStatusModal') {
    closeStatusModal();
  }
}

// Función para guardar cambio de estado (fetch real)
async function saveStatusChange() {
  const newStatus = document.getElementById('newStatus').value;
  const resolutionNotes = document.getElementById('resolutionNotes').value;
  const resolutionDocs = document.getElementById('resolutionDocs');

  if (!newStatus) {
    alert('Por favor, seleccione un nuevo estado.');
    return;
  }

  if (!resolutionNotes.trim()) {
    alert('Por favor, proporcione comentarios o resolución.');
    return;
  }
  
  // Validar que se adjuntó documentación si el estado es Resuelta
  if (newStatus === 'Resuelta') {
    if (!resolutionDocs.files || resolutionDocs.files.length === 0) {
      alert('Debe adjuntar documentación de resolución cuando el estado es Resuelta.');
      return;
    }
  }

  if (!currentEditingFolio) {
    alert('Error: No se pudo identificar la denuncia.');
    return;
  }

  const saveBtn = document.querySelector('[onclick="saveStatusChange()"]');
  const originalHtml = saveBtn.innerHTML;
  saveBtn.disabled = true;
  saveBtn.innerHTML = '<span class="material-symbols-outlined text-sm">hourglass_top</span> Guardando...';

  try {
    const formData = new FormData(document.getElementById('changeStatusForm'));

    const response = await fetch((typeof BASE_URL !== 'undefined' ? BASE_URL : '') + 'admin/actualizarEstado', {
      method: 'POST',
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || 'Error al actualizar');
    }

    // Actualizar datos locales
    if (complaintsDatabase[currentEditingFolio]) {
      complaintsDatabase[currentEditingFolio].status     = newStatus;
      complaintsDatabase[currentEditingFolio].statusText = getStatusConfig(newStatus).text;
    }

    // Si el estado es Resuelta, eliminar la fila de la tabla
    if (newStatus === 'Resuelta') {
      const row = document.querySelector(`tr[data-denuncia-id="${currentEditingFolio}"]`);
      if (row) {
        row.remove();
      }
      // Verificar si no quedan más denuncias
      const tbody = document.querySelector('tbody');
      if (tbody && tbody.querySelectorAll('tr').length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-sm text-secondary">No se encontraron denuncias registradas.</td></tr>';
      }
    } else {
      // Actualizar badge en la fila de la tabla
      updateTableRowBadge(currentEditingFolio, newStatus);
    }

    closeStatusModal();
    showToast('Estado actualizado correctamente');

  } catch (err) {
    alert('Error al guardar: ' + err.message);
  } finally {
    saveBtn.disabled = false;
    saveBtn.innerHTML = originalHtml;
  }
}

// Función auxiliar para obtener configuración de estado
function getStatusConfig(status) {
  const configs = {
    'Pendiente':   { text: 'Pendiente',   class: 'bg-secondary-container text-on-secondary-container' },
    'En Revisión': { text: 'En Revisión', class: 'bg-tertiary-fixed text-on-tertiary-fixed-variant' },
    'Investigación':     { text: 'Investigación',    class: 'bg-error-container text-on-error-container' },
    // 'Aprobado':    { text: 'Aprobado',    class: 'bg-primary-fixed text-on-primary-fixed-variant' },
    'Desechada':   { text: 'Desechada',   class: 'bg-surface-container-high text-secondary' },
    'Resuelta':    { text: 'Resuelta',    class: 'bg-tertiary-fixed text-on-tertiary-fixed-variant' },
  };
  return configs[status] || configs['Pendiente'];
}

// Actualiza el badge de estado en la fila de la tabla sin recargar
function updateTableRowBadge(id, status) {
  const row = document.querySelector(`tr[data-denuncia-id="${id}"]`);
  if (!row) return;
  const badge = row.querySelector('td:nth-child(5) span');
  if (!badge) return;
  const config = getStatusConfig(status);
  badge.textContent = config.text;
  badge.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${config.class}`;
}

// Muestra una notificación toast temporal
function showToast(mensaje) {
  const existing = document.getElementById('toast-notification');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'toast-notification';
  toast.className = [
    'fixed bottom-6 right-6 z-[9999] flex items-center gap-3',
    'bg-primary text-white px-5 py-3 rounded-xl shadow-lg',
    'text-sm font-semibold transition-all duration-300 opacity-0 translate-y-2',
  ].join(' ');
  toast.innerHTML = `<span class="material-symbols-outlined text-base">check_circle</span>${mensaje}`;
  document.body.appendChild(toast);

  requestAnimationFrame(() => {
    toast.classList.remove('opacity-0', 'translate-y-2');
    toast.classList.add('opacity-100', 'translate-y-0');
  });

  setTimeout(() => {
    toast.classList.add('opacity-0', 'translate-y-2');
    toast.addEventListener('transitionend', () => toast.remove());
  }, 3500);
}

// Cerrar modales con la tecla Escape
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeDetailModal();
    closeStatusModal();
  }
});

// Funcionalidad de filtros
function filterByStatus(status) {
  // Obtener todas las filas de la tabla (excepto el encabezado)
  const tbody = document.querySelector('tbody');
  const rows = tbody.querySelectorAll('tr[data-denuncia-id]');
  
  let visibleCount = 0;
  
  // Filtrar filas
  rows.forEach(row => {
    const rowStatusBadge = row.querySelector('td:nth-child(5) span');
    if (!rowStatusBadge) return;
    
    const rowStatus = rowStatusBadge.textContent.trim();
    
    if (status === 'all') {
      // Mostrar todas
      row.style.display = '';
      visibleCount++;
    } else {
      // Mostrar solo las que coinciden con el filtro
      if (rowStatus === status) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    }
  });
  
  // Actualizar mensaje si no hay resultados
  const noResultsRow = tbody.querySelector('tr:not([data-denuncia-id])');
  if (visibleCount === 0 && rows.length > 0) {
    if (!noResultsRow) {
      const emptyRow = document.createElement('tr');
      emptyRow.innerHTML = '<td colspan="6" class="px-6 py-12 text-center text-sm text-secondary">No se encontraron denuncias con este estado.</td>';
      tbody.appendChild(emptyRow);
    }
  } else if (noResultsRow && visibleCount > 0) {
    noResultsRow.remove();
  }
  
  // Actualizar estilos de los botones
  updateFilterButtons(status);
  
  // Actualizar contador de registros mostrados
  updateRecordCounter(visibleCount);
}

// Función para actualizar el estilo de los botones de filtro
function updateFilterButtons(activeFilter) {
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(btn => {
    const filterValue = btn.getAttribute('data-filter');
    if (filterValue === activeFilter) {
      // Botón activo
      btn.className = 'filter-btn px-4 py-2 bg-primary text-white text-xs font-bold rounded-full transition-all';
    } else {
      // Botón inactivo
      btn.className = 'filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all';
    }
  });
}

// Función para actualizar el contador de registros
function updateRecordCounter(count) {
  const counter = document.querySelector('.flex.justify-between.items-center.px-6.py-4 p');
  if (counter) {
    counter.textContent = `Mostrando ${count} registro${count !== 1 ? 's' : ''}`;
  }
}

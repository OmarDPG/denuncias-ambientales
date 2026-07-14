// Variables globales para control de modales
let currentEditingUserId = null;
let currentAction = null;
let currentActionUserId = null;
let currentActionUserName = null;

// ========== MODAL AGREGAR USUARIO ==========
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    document.getElementById('addUserForm').reset();
}

function closeAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    document.getElementById('addUserForm').reset();
}

function saveAddUser() {
    const form = document.getElementById('addUserForm');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Obtener valores
    const nombre = document.getElementById('addNombre').value.trim();
    const apellidoP = document.getElementById('addApellidoP').value.trim();
    const apellidoM = document.getElementById('addApellidoM').value.trim();
    const cargo = document.getElementById('addCargo').value;
    const rol = document.getElementById('addRol').value;
    const curp = document.getElementById('addCurp').value.trim().toUpperCase();
    const email = document.getElementById('addEmail').value.trim();
    const password = document.getElementById('addPassword').value;
    const passwordConfirm = document.getElementById('addPasswordConfirm').value;

    // Validar CURP
    const curpPattern = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}$/;
    if (!curpPattern.test(curp)) {
        alert('El CURP no tiene un formato válido. Debe tener 18 caracteres: 4 letras, 6 números, H o M, 5 letras y 2 números.');
        return;
    }

    // Validar contraseñas
    if (password !== passwordConfirm) {
        alert('Las contraseñas no coinciden. Por favor, verifique.');
        return;
    }

    if (password.length < 8) {
        alert('La contraseña debe tener al menos 8 caracteres.');
        return;
    }

    // Crear FormData con los datos
    const formData = new FormData();
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);
    formData.append('nombre', nombre);
    formData.append('apellidoP', apellidoP);
    formData.append('apellidoM', apellidoM);
    formData.append('adm', cargo);
    formData.append('id_rol', rol);
    formData.append('expediente', curp);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('password_confirm', passwordConfirm);

    // Enviar al servidor
    fetch(BASE_URL + 'admin/crearUsuario', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Usuario creado exitosamente!\n\nNombre: ${nombre}\nCargo: ${cargo === '1' ? 'Administrador' : 'Inspector'}\nCorreo: ${email}\n\nSe ha enviado un correo de confirmación al usuario.`);
            closeAddUserModal();
            location.reload(); // Recargar página para mostrar el nuevo usuario
        } else {
            alert('Error al crear usuario: ' + (data.error || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear usuario. Por favor, intente nuevamente.');
    });
}

// ========== MODAL EDITAR USUARIO ==========
function openEditUserModal(userId) {
    currentEditingUserId = userId;

    // Cargar datos del usuario desde el servidor
    fetch(BASE_URL + 'admin/obtenerUsuario/' + userId)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.usuario) {
            const user = data.usuario;
            
            // Llenar formulario con datos actuales
            document.getElementById('editUserId').value = userId;
            document.getElementById('editNombre').value = user.nombre;
            document.getElementById('editApellidoP').value = user.apellidoP;
            document.getElementById('editApellidoM').value = user.apellidoM || '';
            document.getElementById('editCargo').value = user.adm;
            document.getElementById('editRol').value = user.id_rol;
            document.getElementById('editCurp').value = user.expediente;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editUserSubtitle').textContent = `Editando: ${user.nombre} ${user.apellidoP}`;

            document.getElementById('editUserModal').classList.remove('hidden');
        } else {
            alert('Error al cargar datos del usuario');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar datos del usuario. Por favor, intente nuevamente.');
    });
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    document.getElementById('editUserForm').reset();
    currentEditingUserId = null;
}

function saveEditUser() {
    const form = document.getElementById('editUserForm');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const userId = currentEditingUserId;
    const nombre = document.getElementById('editNombre').value.trim();
    const apellidoP = document.getElementById('editApellidoP').value.trim();
    const apellidoM = document.getElementById('editApellidoM').value.trim();
    const cargo = document.getElementById('editCargo').value;
    const rol = document.getElementById('editRol').value;
    const curp = document.getElementById('editCurp').value.trim().toUpperCase();
    const email = document.getElementById('editEmail').value.trim();

    // Validar CURP
    const curpPattern = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}$/;
    if (!curpPattern.test(curp)) {
        alert('El CURP no tiene un formato válido.');
        return;
    }

    // Crear FormData con los datos
    const formData = new FormData();
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);
    formData.append('id_adm', userId);
    formData.append('nombre', nombre);
    formData.append('apellidoP', apellidoP);
    formData.append('apellidoM', apellidoM);
    formData.append('adm', cargo);
    formData.append('id_rol', rol);
    formData.append('expediente', curp);
    formData.append('email', email);

    // Enviar al servidor
    fetch(BASE_URL + 'admin/actualizarUsuario', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Usuario actualizado exitosamente!\n\nNombre: ${nombre}\nCargo: ${cargo === '1' ? 'Administrador' : 'Inspector'}\nCorreo: ${email}`);
            closeEditUserModal();
            location.reload(); // Recargar página para mostrar cambios
        } else {
            alert('Error al actualizar usuario: ' + (data.error || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar usuario. Por favor, intente nuevamente.');
    });
}

// ========== MODAL DESACTIVAR/ACTIVAR USUARIO ==========
function openDeactivateModal(userId, userName) {
    currentAction = 'deactivate';
    currentActionUserId = userId;
    currentActionUserName = userName;

    document.getElementById('confirmActionTitle').textContent = 'Desactivar Usuario';
    document.getElementById('confirmActionSubtitle').textContent = 'Confirmar desactivación de acceso';
    document.getElementById('confirmActionIcon').textContent = 'person_off';
    document.getElementById('confirmActionMessage').textContent = 
        `¿Está seguro que desea desactivar al usuario ${userName}?`;
    document.getElementById('confirmActionDetail').textContent = 
        'El usuario no podrá acceder al sistema hasta que sea reactivado. Esta acción puede revertirse.';
    document.getElementById('confirmActionButton').className = 
        'px-6 py-2 bg-error text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2';
    document.getElementById('confirmActionButton').innerHTML = 
        '<span class="material-symbols-outlined text-sm">person_off</span> Desactivar';

    document.getElementById('confirmActionModal').classList.remove('hidden');
}

function openActivateModal(userId, userName) {
    currentAction = 'activate';
    currentActionUserId = userId;
    currentActionUserName = userName;

    document.getElementById('confirmActionTitle').textContent = 'Activar Usuario';
    document.getElementById('confirmActionSubtitle').textContent = 'Confirmar activación de acceso';
    document.getElementById('confirmActionIcon').textContent = 'person_check';
    document.getElementById('confirmActionMessage').textContent = 
        `¿Está seguro que desea activar al usuario ${userName}?`;
    document.getElementById('confirmActionDetail').textContent = 
        'El usuario podrá acceder nuevamente al sistema con sus credenciales.';
    document.getElementById('confirmActionButton').className = 
        'px-6 py-2 bg-primary text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2';
    document.getElementById('confirmActionButton').innerHTML = 
        '<span class="material-symbols-outlined text-sm">person_check</span> Activar';

    document.getElementById('confirmActionModal').classList.remove('hidden');
}

function closeConfirmActionModal() {
    document.getElementById('confirmActionModal').classList.add('hidden');
    currentAction = null;
    currentActionUserId = null;
    currentActionUserName = null;
}

function executeAction() {
    if (!currentAction || !currentActionUserId) {
        alert('Error: No se pudo identificar la acción.');
        closeConfirmActionModal();
        return;
    }

    const activo = currentAction === 'activate' ? '1' : '0';
    const formData = new FormData();
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);
    formData.append('id_adm', currentActionUserId);
    formData.append('activo', activo);

    fetch(BASE_URL + 'admin/cambiarEstadoUsuario', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const accion = currentAction === 'activate' ? 'activado' : 'desactivado';
            alert(`Usuario ${currentActionUserName} ha sido ${accion} exitosamente.\n\nSe ha enviado una notificación por correo.`);
            closeConfirmActionModal();
            location.reload(); // Recargar página para reflejar cambios
        } else {
            alert('Error al cambiar estado del usuario: ' + (data.error || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar estado del usuario. Por favor, intente nuevamente.');
    });
}

// ========== MODAL RESET PASSWORD ==========
let currentResetPasswordUserId = null;
let currentResetPasswordUserName = null;
let pendingNewPassword = null;

function openResetPasswordModal(userId, userName) {
    currentResetPasswordUserId = userId;
    currentResetPasswordUserName = userName;

    // Llenar información del usuario
    document.getElementById('resetPasswordUserId').value = userId;
    document.getElementById('resetPasswordUserName').textContent = userName;
    document.getElementById('resetPasswordSubtitle').textContent = `Cambiar contraseña de ${userName}`;

    // Resetear formulario
    document.getElementById('resetPasswordForm').reset();

    document.getElementById('resetPasswordModal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
    document.getElementById('resetPasswordForm').reset();
    currentResetPasswordUserId = null;
    currentResetPasswordUserName = null;
    pendingNewPassword = null;
}

function confirmResetPassword() {
    const form = document.getElementById('resetPasswordForm');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const newPassword = document.getElementById('resetNewPassword').value;
    const confirmPassword = document.getElementById('resetConfirmPassword').value;

    // Validar longitud mínima
    if (newPassword.length < 8) {
        alert('La contraseña debe tener al menos 8 caracteres.');
        return;
    }

    // Validar que las contraseñas coincidan
    if (newPassword !== confirmPassword) {
        alert('Las contraseñas no coinciden. Por favor, verifique.');
        return;
    }

    // Guardar la contraseña temporalmente
    pendingNewPassword = newPassword;

    // Abrir modal de confirmación
    document.getElementById('confirmResetUserName').textContent = currentResetPasswordUserName;
    document.getElementById('confirmResetPasswordModal').classList.remove('hidden');
}

function closeConfirmResetPasswordModal() {
    document.getElementById('confirmResetPasswordModal').classList.add('hidden');
    pendingNewPassword = null;
}

function executeResetPassword() {
    if (!currentResetPasswordUserId || !pendingNewPassword) {
        alert('Error: No se pudo completar el cambio de contraseña.');
        closeConfirmResetPasswordModal();
        closeResetPasswordModal();
        return;
    }

    const formData = new FormData();
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);
    formData.append('id_adm', currentResetPasswordUserId);
    formData.append('new_password', pendingNewPassword);
    formData.append('new_password_confirm', pendingNewPassword);

    fetch(BASE_URL + 'admin/restablecerPassword', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Contraseña restablecida exitosamente para ${currentResetPasswordUserName}!\n\nSe ha enviado un correo de confirmación al usuario con instrucciones para su próximo inicio de sesión.`);
            closeConfirmResetPasswordModal();
            closeResetPasswordModal();
        } else {
            alert('Error al restablecer contraseña: ' + (data.error || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al restablecer contraseña. Por favor, intente nuevamente.');
    });
}

// Función para mostrar/ocultar contraseña
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}

// ========== FUNCIONES AUXILIARES ==========
function filtrarUsuarios() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const filtroRol = document.getElementById('filtroRol').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaUsuarios tbody tr');

    filas.forEach(fila => {
        const nombre = fila.getAttribute('data-nombre') || '';
        const email = fila.getAttribute('data-email') || '';
        const rol = fila.getAttribute('data-rol') || '';

        const coincideBusqueda = nombre.includes(searchTerm) || email.includes(searchTerm);
        const coincideRol = !filtroRol || rol.toLowerCase().includes(filtroRol);

        if (coincideBusqueda && coincideRol) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

function closeModalOnOverlay(event, modalId) {
    if (event.target.id === modalId) {
        document.getElementById(modalId).classList.add('hidden');
        if (modalId === 'addUserModal') {
            document.getElementById('addUserForm').reset();
        } else if (modalId === 'editUserModal') {
            document.getElementById('editUserForm').reset();
            currentEditingUserId = null;
        } else if (modalId === 'confirmActionModal') {
            currentAction = null;
            currentActionUserId = null;
            currentActionUserName = null;
        } else if (modalId === 'resetPasswordModal') {
            closeResetPasswordModal();
        } else if (modalId === 'confirmResetPasswordModal') {
            closeConfirmResetPasswordModal();
        }
    }
}

// ========== EVENT LISTENERS ==========
// Cerrar modales con tecla Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddUserModal();
        closeEditUserModal();
        closeConfirmActionModal();
        closeResetPasswordModal();
        closeConfirmResetPasswordModal();
    }
});

// Validación en tiempo real del CURP
document.getElementById('addCurp')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

document.getElementById('editCurp')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

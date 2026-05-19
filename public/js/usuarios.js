// Base de datos simulada de usuarios
const usersDatabase = {
    '1': {
        id: '1',
        nombre: 'Julian Sterling',
        cargo: 'Administrador',
        curp: 'STJU850315HDFLRN09',
        telefono: '5551234567',
        email: 'j.sterling@livingarchive.org',
        activo: true
    },
    '2': {
        id: '2',
        nombre: 'Elena Aris',
        cargo: 'Inspector',
        curp: 'AREL900520MDFRLN03',
        telefono: '5552345678',
        email: 'e.aris@livingarchive.org',
        activo: true
    },
    '3': {
        id: '3',
        nombre: 'Marcus Halloway',
        cargo: 'Analyst',
        curp: 'HALM880712HDFLRC05',
        telefono: '5553456789',
        email: 'm.halloway@livingarchive.org',
        activo: false
    },
    '4': {
        id: '4',
        nombre: 'Sarah Kincaid',
        cargo: 'Analyst',
        curp: 'KINS920425MDFLRH08',
        telefono: '5554567890',
        email: 's.kincaid@livingarchive.org',
        activo: true
    }
};

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
    const nombre = document.getElementById('addNombreCompleto').value.trim();
    const cargo = document.getElementById('addCargo').value;
    const curp = document.getElementById('addCurp').value.trim().toUpperCase();
    const telefono = document.getElementById('addTelefono').value.trim();
    const email = document.getElementById('addEmail').value.trim();
    const password = document.getElementById('addPassword').value;
    const passwordConfirm = document.getElementById('addPasswordConfirm').value;

    // Validar CURP
    const curpPattern = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}$/;
    if (!curpPattern.test(curp)) {
        alert('El CURP no tiene un formato válido. Debe tener 18 caracteres: 4 letras, 6 números, H o M, 5 letras y 2 números.');
        return;
    }

    // Validar teléfono
    const telefonoPattern = /^[0-9]{10}$/;
    if (!telefonoPattern.test(telefono)) {
        alert('El teléfono debe contener exactamente 10 dígitos numéricos.');
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

    // Validar email único
    for (let userId in usersDatabase) {
        if (usersDatabase[userId].email.toLowerCase() === email.toLowerCase()) {
            alert('El correo electrónico ya está registrado en el sistema.');
            return;
        }
    }

    // Simular guardado
    console.log('Nuevo usuario:', { nombre, cargo, curp, telefono, email, password });

    alert(`Usuario creado exitosamente!\n\nNombre: ${nombre}\nCargo: ${cargo}\nCorreo: ${email}\n\nSe ha enviado un correo de confirmación al usuario.`);

    closeAddUserModal();
    
    // Aquí iría la llamada a la API para guardar el usuario
}

// ========== MODAL EDITAR USUARIO ==========
function openEditUserModal(userId) {
    const user = usersDatabase[userId];
    if (!user) {
        alert('Usuario no encontrado');
        return;
    }

    currentEditingUserId = userId;

    // Llenar formulario con datos actuales
    document.getElementById('editUserId').value = userId;
    document.getElementById('editNombreCompleto').value = user.nombre;
    document.getElementById('editCargo').value = user.cargo;
    document.getElementById('editCurp').value = user.curp;
    document.getElementById('editTelefono').value = user.telefono;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editUserSubtitle').textContent = `Editando: ${user.nombre}`;

    document.getElementById('editUserModal').classList.remove('hidden');
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
    const nombre = document.getElementById('editNombreCompleto').value.trim();
    const cargo = document.getElementById('editCargo').value;
    const curp = document.getElementById('editCurp').value.trim().toUpperCase();
    const telefono = document.getElementById('editTelefono').value.trim();
    const email = document.getElementById('editEmail').value.trim();

    // Validar CURP
    const curpPattern = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}$/;
    if (!curpPattern.test(curp)) {
        alert('El CURP no tiene un formato válido.');
        return;
    }

    // Validar teléfono
    const telefonoPattern = /^[0-9]{10}$/;
    if (!telefonoPattern.test(telefono)) {
        alert('El teléfono debe contener exactamente 10 dígitos numéricos.');
        return;
    }

    // Validar email único (excepto el del usuario actual)
    for (let id in usersDatabase) {
        if (id !== userId && usersDatabase[id].email.toLowerCase() === email.toLowerCase()) {
            alert('El correo electrónico ya está registrado por otro usuario.');
            return;
        }
    }

    // Simular actualización
    console.log('Usuario actualizado:', { userId, nombre, cargo, curp, telefono, email });

    alert(`Usuario actualizado exitosamente!\n\nNombre: ${nombre}\nCargo: ${cargo}\nCorreo: ${email}`);

    closeEditUserModal();
    
    // Aquí iría la llamada a la API para actualizar el usuario
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

    if (currentAction === 'deactivate') {
        console.log(`Desactivando usuario ${currentActionUserId}`);
        alert(`Usuario ${currentActionUserName} ha sido desactivado exitosamente.\n\nSe ha enviado una notificación por correo.`);
    } else if (currentAction === 'activate') {
        console.log(`Activando usuario ${currentActionUserId}`);
        alert(`Usuario ${currentActionUserName} ha sido activado exitosamente.\n\nSe ha enviado una notificación por correo.`);
    }

    closeConfirmActionModal();
    
    // Aquí iría la llamada a la API para activar/desactivar el usuario
}

// ========== MODAL RESET PASSWORD ==========
let currentResetPasswordUserId = null;
let currentResetPasswordUserName = null;
let pendingNewPassword = null;

function openResetPasswordModal(userId, userName) {
    const user = usersDatabase[userId];
    if (!user) {
        alert('Usuario no encontrado');
        return;
    }

    currentResetPasswordUserId = userId;
    currentResetPasswordUserName = userName;

    // Llenar información del usuario
    document.getElementById('resetPasswordUserId').value = userId;
    document.getElementById('resetPasswordUserName').textContent = user.nombre;
    document.getElementById('resetPasswordUserEmail').textContent = user.email;
    document.getElementById('resetPasswordSubtitle').textContent = `Cambiar contraseña de ${user.nombre}`;

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

    // Simular actualización de contraseña
    console.log(`Restableciendo contraseña para usuario ${currentResetPasswordUserId}:`, {
        userId: currentResetPasswordUserId,
        newPassword: pendingNewPassword
    });

    alert(`Contraseña restablecida exitosamente para ${currentResetPasswordUserName}!\n\nSe ha enviado un correo de confirmación al usuario con instrucciones para su próximo inicio de sesión.`);

    // Cerrar ambos modales
    closeConfirmResetPasswordModal();
    closeResetPasswordModal();

    // Aquí iría la llamada a la API para actualizar la contraseña
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
        }
    }
}

// ========== EVENT LISTENERS ==========
// Cerrar modales con tecla Escape - actualizado
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

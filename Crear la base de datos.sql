-- Crear la base de datos (Opcional, cambia el nombre si ya tienes una)
CREATE DATABASE IF NOT EXISTS sistema_denuncias
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sistema_denuncias;

-- =========================================================
-- TABLA PRINCIPAL: denuncias
-- =========================================================
CREATE TABLE denuncias (
    -- 1. Identificadores y Control Administrativo
    id_denuncia INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(30) UNIQUE NOT NULL COMMENT 'Folio único generado para el ciudadano',
    estatus ENUM('Nueva', 'En Revisión', 'Investigación', 'Resuelta', 'Desechada') DEFAULT 'Nueva' COMMENT 'Estado actual del trámite',
    fecha_captura TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que se envió el formulario',
    fecha_resolucion TIMESTAMP NULL DEFAULT NULL COMMENT 'Fecha en que se cerró el caso',
    notas_internas TEXT NULL COMMENT 'Campo para uso exclusivo de los administradores',

    -- 2. Datos del Denunciante (Paso 1)
    tipo_persona ENUM('fisica', 'moral') NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    genero ENUM('masculino', 'femenino', 'otro', 'prefiero-no-decir') NOT NULL,
    estado VARCHAR(100) NOT NULL,
    municipio VARCHAR(100) NOT NULL,
    colonia VARCHAR(150) NOT NULL,
    codigo_postal VARCHAR(5) NOT NULL,
    calle VARCHAR(150) NOT NULL,
    numero_exterior VARCHAR(50) NOT NULL,
    numero_interior VARCHAR(50) NULL,
    email VARCHAR(150) NOT NULL,
    telefono VARCHAR(10) NOT NULL,
    
    -- Representante Legal (Campos condicionales)
    es_representante BOOLEAN DEFAULT FALSE,
    razon_social VARCHAR(255) NULL,
    nombre_representante VARCHAR(255) NULL,

    -- 3. Detalles de la Denuncia y Ubicación (Paso 2)
    tipo_denuncia VARCHAR(100) NOT NULL,
    hechos_denunciados TEXT NOT NULL,
    latitud DECIMAL(10, 8) NULL COMMENT 'Latitud exacta del mapa',
    longitud DECIMAL(11, 8) NULL COMMENT 'Longitud exacta del mapa',

    -- 4. Datos del Denunciado (Paso 3)
    nombre_denunciado VARCHAR(255) NOT NULL COMMENT 'Nombre o Quien resulte responsable',
    denunciado_es_moral BOOLEAN DEFAULT FALSE,
    razon_social_denunciado VARCHAR(255) NULL,
    municipio_denunciado VARCHAR(100) NOT NULL,
    colonia_denunciado VARCHAR(150) NOT NULL,
    calle_denunciado VARCHAR(150) NOT NULL,
    codigo_postal_denunciado VARCHAR(5) NOT NULL,
    numero_exterior_denunciado VARCHAR(50) NOT NULL,
    numero_interior_denunciado VARCHAR(50) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- TABLA SECUNDARIA: evidencias_denuncia
-- =========================================================
CREATE TABLE evidencias_denuncia (
    id_evidencia INT AUTO_INCREMENT PRIMARY KEY,
    id_denuncia INT NOT NULL,
    nombre_original VARCHAR(255) NOT NULL COMMENT 'Nombre real del archivo subido',
    ruta_archivo VARCHAR(500) NOT NULL COMMENT 'Ruta física o URL donde se guardó en el servidor',
    tipo_archivo VARCHAR(50) NOT NULL COMMENT 'MIME type (ej. image/jpeg, application/pdf)',
    peso_bytes INT NOT NULL COMMENT 'Tamaño del archivo',
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Relación con la tabla principal
    CONSTRAINT fk_denuncia_evidencia 
        FOREIGN KEY (id_denuncia) 
        REFERENCES denuncias(id_denuncia) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `admin` (
    `id_adm` int(11) NOT NULL,
    `usuario` varchar(250) DEFAULT NULL,
    `password` varchar(250) DEFAULT NULL,
    `nombre` varchar(250) DEFAULT NULL,
    `apellidoP` varchar(250) DEFAULT NULL,
    `apellidoM` varchar(250) DEFAULT NULL,
    `expediente` varchar(10) DEFAULT NULL,
    `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `fecha_ultima` timestamp NULL DEFAULT NULL,
    `activo` tinyint(1) NOT NULL DEFAULT 1,
    `adm` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
INSERT INTO `admin` (`id_adm`, `usuario`, `password`, `nombre`, `apellidoP`, `apellidoM`, `expediente`, `fecha_alta`, `fecha_ultima`, `activo`, `adm`) VALUES
(1, 'administrador', '$2y$10$ofxM0DQDgEBl1V6hJtNLg.vdgARQmqy9MqbGoUSu0y/XBxWlTLNpO', 'ADMINISTRADOR', 'ADMINISTRADOR', 'ADMINISTRADOR', '141855', '2024-03-01 20:29:59', '2024-03-01 20:29:59', 1, 1);
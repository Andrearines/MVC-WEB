-- Creación de la base de datos si no existe (la base de datos se llama 'dev' según docker-compose y .env)
CREATE DATABASE IF NOT EXISTS `dev` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `dev`;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `comida`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `comida`;

CREATE TABLE `comida` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `precio` DECIMAL(10, 2) NOT NULL,
    `imagen` VARCHAR(255) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Volcado de datos para la tabla `comida`
-- --------------------------------------------------------

INSERT INTO
    `comida` (
        `id`,
        `nombre`,
        `precio`,
        `imagen`,
        `descripcion`
    )
VALUES (
        1,
        'Pizza Margherita',
        12.99,
        'img.jpg',
        'Pizza clásica con salsa de tomate natural, queso mozzarella fresco, albahaca y un toque de aceite de oliva.'
    ),
    (
        2,
        'Hamburguesa Premium',
        9.50,
        'img.jpg',
        'Hamburguesa con carne Angus de 150g, queso cheddar fundido, lechuga fresca, tomate y salsa especial de la casa en pan brioche.'
    ),
    (
        3,
        'Tacos al Pastor',
        7.20,
        'img.jpg',
        'Orden de 3 deliciosos tacos al pastor con piña caramelizada, cebolla picada, cilantro fresco y salsa picante verde/roja.'
    ),
    (
        4,
        'Ensalada César',
        8.00,
        'img.jpg',
        'Mezcla de lechuga romana crujiente, crutones de ajo, lascas de queso parmesano y aderezo César cremoso casero.'
    ),
    (
        5,
        'Sushi Roll Combo',
        15.00,
        'img.jpg',
        'Combo surtido de 12 piezas de rolls clásicos (California Roll, Philadelphia Roll y Spicy Tuna Roll).'
    ),
    (
        6,
        'Ramen de Cerdo (Tonkotsu)',
        11.50,
        'img.jpg',
        'Fideos japoneses en caldo espeso de cerdo, acompañados de huevo marinado, lomo de cerdo chashu, cebollín y alga nori.'
    );
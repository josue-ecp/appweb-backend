-- Adminer 6.0.1 MySQL 8.0.46 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `administradores`;
CREATE TABLE `administradores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `administradores` (`id`, `nombre`, `correo`, `contrasena`, `creado_en`) VALUES
(2,	'Profesor Admin',	'admin@aventurilandia.com',	'$2y$12$eHFblt4pMNOZqplxii5ad.7RLgBia53pVYkldEQLqcwJ.HZZxL2Qy',	'2026-09-21 17:05:51');

DROP TABLE IF EXISTS `materias`;
CREATE TABLE `materias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `etiqueta_superior` varchar(255) DEFAULT NULL,
  `descripcion` text,
  `total_misiones` int DEFAULT '10',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `materias` (`id`, `slug`, `titulo`, `etiqueta_superior`, `descripcion`, `total_misiones`, `creado_en`, `actualizado_en`) VALUES
(1,	'matematicas',	'Matemáticas Mágicas',	NULL,	'Suma, resta, números y lógica numérica interactiva.',	10,	'2026-09-13 21:39:25',	'2026-09-13 21:39:25'),
(2,	'ciencia',	'Aventuras de Ciencia',	NULL,	'Explora el espacio, los animales y fenómenos naturales.',	10,	'2026-09-13 21:39:25',	'2026-09-13 21:39:25'),
(3,	'espanol',	'Español Divertido',	NULL,	'Cuentos, vocabulario, letras y ortografía.',	10,	'2026-09-13 21:39:25',	'2026-09-13 21:39:25'),
(5,	'geografia_asombrosa',	'Geografía Asombrosa 🌍',	'ISLA DE LA GEOGRAFÍA',	'Explora los continentes, descubre países increíbles y aprende sobre las maravillas naturales del mundo.',	10,	'2026-09-21 18:20:13',	'2026-09-21 18:25:55');

DROP TABLE IF EXISTS `preguntas`;
CREATE TABLE `preguntas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `materia_slug` varchar(255) NOT NULL,
  `pregunta` text NOT NULL,
  `opcion_1` varchar(255) NOT NULL,
  `opcion_2` varchar(255) NOT NULL,
  `opcion_3` varchar(255) NOT NULL,
  `respuesta_correcta` varchar(255) NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `preguntas` (`id`, `materia_slug`, `pregunta`, `opcion_1`, `opcion_2`, `opcion_3`, `respuesta_correcta`, `creado_en`) VALUES
(1,	'geografia_asombrosa',	'¿Cuál es el río más largo del mundo?',	'Nilo',	'Amazonas',	'Mississippi',	'Amazonas',	'2026-09-21 18:20:13'),
(2,	'geografia_asombrosa',	'¿Cuál es el océano más grande del planeta?',	'Océano Atlántico',	'Océano Índico',	'Océano Pacífico',	'Océano Pacífico',	'2026-09-21 18:20:13'),
(3,	'geografia_asombrosa',	'¿En qué continente se encuentra Egipto?',	'África',	'Asia',	'Europa',	'África',	'2026-09-21 18:20:13'),
(4,	'geografia_asombrosa',	'¿Cuál es el país más grande del mundo por superficie?',	'Canadá',	'Rusia',	'China',	'Rusia',	'2026-09-21 18:20:13'),
(5,	'geografia_asombrosa',	'¿Qué país tiene forma de bota en el mapa?',	'España',	'Italia',	'Grecia',	'Italia',	'2026-09-21 18:20:13'),
(6,	'geografia_asombrosa',	'¿Cuál es la montaña más alta del mundo?',	'K2',	'Monte Everest',	'Kilimanjaro',	'Monte Everest',	'2026-09-21 18:20:13'),
(7,	'geografia_asombrosa',	'¿En qué continente está ubicado Japón?',	'América',	'Oceanía',	'Asia',	'Asia',	'2026-09-21 18:20:13'),
(8,	'geografia_asombrosa',	'¿Cuál es el desierto cálido más grande del mundo?',	'Desierto de Atacama',	'Desierto del Sahara',	'Desierto de Gobi',	'Desierto del Sahara',	'2026-09-21 18:20:13'),
(9,	'geografia_asombrosa',	'¿Qué gas abunda más en la atmósfera de la Tierra?',	'Oxígeno',	'Nitrógeno',	'Dióxido de carbono',	'Nitrógeno',	'2026-09-21 18:20:13'),
(10,	'geografia_asombrosa',	'¿Cuántos continentes existen oficialmente en la Tierra?',	'5',	'6',	'7',	'7',	'2026-09-21 18:20:13');

DROP TABLE IF EXISTS `progreso_usuarios`;
CREATE TABLE `progreso_usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `materia_id` int NOT NULL,
  `misiones_completadas` int DEFAULT '0',
  `porcentaje_progreso` int DEFAULT '0',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `materia_id` (`materia_id`),
  CONSTRAINT `progreso_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `progreso_usuarios_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `usuario_accesorios`;
CREATE TABLE `usuario_accesorios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `accesorio_slug` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_accesorio` (`user_id`,`accesorio_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuario_accesorios` (`id`, `user_id`, `accesorio_slug`, `created_at`, `updated_at`) VALUES
(3,	1,	'medalla_oro',	'2026-09-22 22:38:14',	'2026-09-22 22:38:14');

DROP TABLE IF EXISTS `usuario_trofeos`;
CREATE TABLE `usuario_trofeos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `trofeo_slug` varchar(50) NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `usuario_trofeos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuario_trofeos` (`id`, `usuario_id`, `trofeo_slug`, `creado_en`) VALUES
(8,	2,	'primeros_pasos',	'2026-09-15 17:12:06'),
(15,	5,	'primeros_pasos',	'2026-09-15 19:41:30'),
(16,	5,	'mente_brillante',	'2026-09-15 19:42:27'),
(17,	6,	'primeros_pasos',	'2026-09-17 22:18:07'),
(18,	2,	'explorador_estelar',	'2026-09-23 01:44:08');

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `nivel` int DEFAULT '4',
  `estrellas` int DEFAULT '128',
  `dias_racha` int DEFAULT '5',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `avatar` varchar(50) DEFAULT 'fox',
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contrasena`, `nivel`, `estrellas`, `dias_racha`, `creado_en`, `actualizado_en`, `avatar`) VALUES
(2,	'josue ceh',	'josue@gmail.com',	'$2y$12$phqelzdyuz16haL/fzjrEuWhDPJUujjMgpY7ukf3Cm3AmQsxfhDfG',	1,	1480,	3,	'2026-09-14 19:35:27',	'2026-09-23 19:39:42',	'fox'),
(3,	'andres',	'andres@gmail.com',	'$2y$12$rQkEL79jUaEMSJrlidG.YeOaYyvLWXJ9ll6pQGMViowglAgGu6KeS',	1,	10,	1,	'2026-09-15 19:09:16',	'2026-09-15 19:09:16',	'fox'),
(5,	'mario pech',	'mario@gmail.com',	'$2y$12$osxPAUnx6ZoRTGh2M1KOYuJOYjYr1obnWCpz9qNOKneDG4RJHW2nO',	1,	75,	1,	'2026-09-15 19:31:43',	'2026-09-15 19:42:27',	'fox'),
(6,	'evelyn',	'evelyn@gmail.com',	'$2y$12$Rf15N3RvOGWbWK50/tbKJeJLWFg76zHxyDp7BzrgfQatdR3PFDZzW',	1,	45,	1,	'2026-09-17 22:16:19',	'2026-09-17 22:18:07',	'unicorn');

-- 2026-09-30 03:07:31 UTC

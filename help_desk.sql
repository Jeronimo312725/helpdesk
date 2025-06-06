-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2025 a las 18:30:19
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `help_desk`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos_adjuntos`
--

CREATE TABLE `archivos_adjuntos` (
  `id_adjunto` int(11) NOT NULL,
  `id_historial` int(11) NOT NULL,
  `nombre_archivo` varchar(255) DEFAULT NULL,
  `ruta_archivo` varchar(255) DEFAULT NULL,
  `tipo_mime` varchar(50) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `archivos_adjuntos`
--

INSERT INTO `archivos_adjuntos` (`id_adjunto`, `id_historial`, `nombre_archivo`, `ruta_archivo`, `tipo_mime`, `fecha_subida`) VALUES
(1, 4, 'Captura.JPG', 'uploads/adj_683f5b09c5c11.JPG', 'image/jpeg', '2025-06-03 20:28:57'),
(2, 6, 'descargar.png', 'uploads/img_683f5e9a463fe.png', 'image/png', '2025-06-03 20:44:10'),
(3, 18, 'atras.png', 'uploads/adj_683f6369a33ad.png', 'image/png', '2025-06-03 21:04:41'),
(4, 19, 'descargar.png', 'uploads/adj_683f6380b236d.png', 'image/png', '2025-06-03 21:05:04'),
(5, 19, 'adjunto-archivo.png', 'uploads/adj_683f6380b2973.png', 'image/png', '2025-06-03 21:05:04'),
(6, 19, 'icons8-billete-combinado-30-removebg-preview.png', 'uploads/adj_683f6380b3d91.png', 'image/png', '2025-06-03 21:05:04'),
(7, 19, 'icons8-billete-combinado-30.png', 'uploads/adj_683f6380b4214.png', 'image/png', '2025-06-03 21:05:04'),
(8, 19, 'agregar.png', 'uploads/adj_683f6380b4794.png', 'image/png', '2025-06-03 21:05:04'),
(9, 19, 'asignar.png', 'uploads/adj_683f6380b4ce9.png', 'image/png', '2025-06-03 21:05:04'),
(10, 19, 'ver-usuario.png', 'uploads/adj_683f6380b5056.png', 'image/png', '2025-06-03 21:05:04'),
(11, 19, 'icons8-usuarios-32-removebg-preview.png', 'uploads/adj_683f6380b540e.png', 'image/png', '2025-06-03 21:05:04'),
(12, 25, 'adjunto-archivo.png', 'uploads/img_683f63c4b57ee.png', 'image/png', '2025-06-03 21:06:12'),
(13, 28, 'atras.png', 'uploads/adj_683f65b37aeb2.png', 'image/png', '2025-06-03 21:14:27'),
(14, 37, 'iconosena.png', 'uploads/adj_683f6b729c07a.png', 'image/png', '2025-06-03 21:38:58'),
(15, 38, 'Logo-Firma.png', 'uploads/adj_683f6ba4b9956.png', 'image/png', '2025-06-03 21:39:48'),
(16, 39, 'descargar-removebg-preview.png', 'uploads/img_683f6bc059bb5.png', 'image/png', '2025-06-03 21:40:16'),
(17, 48, 'adjunto-archivo.png', 'uploads/img_683f6e1ea4260.png', 'image/png', '2025-06-03 21:50:22'),
(18, 59, 'descargar-removebg-preview.png', 'uploads/img_683f71619866d.png', 'image/png', '2025-06-03 22:04:17'),
(19, 59, 'descargar.png', 'uploads/img_683f716198fb4.png', 'image/png', '2025-06-03 22:04:17'),
(20, 59, 'adjunto-archivo.png', 'uploads/img_683f7161992f5.png', 'image/png', '2025-06-03 22:04:17'),
(22, 61, 'WhatsApp Image 2025-05-23 at 12.13.19 PM.jpeg', 'uploads/adj_683f72321467c.jpeg', 'image/jpeg', '2025-06-03 22:07:46'),
(23, 62, 'WhatsApp Image 2025-05-23 at 12.13.19 PM.jpeg', 'uploads/adj_683f73fe7c341.jpeg', 'image/jpeg', '2025-06-03 22:15:26'),
(24, 64, 'atras.png', 'uploads/adj_683f764baa752.png', 'image/png', '2025-06-03 22:25:15'),
(25, 64, 'descargas.png', 'uploads/adj_683f764baabbd.png', 'image/png', '2025-06-03 22:25:15'),
(26, 64, 'descargar-removebg-preview.png', 'uploads/adj_683f764bab895.png', 'image/png', '2025-06-03 22:25:15'),
(27, 64, 'descargar.png', 'uploads/adj_683f764babc38.png', 'image/png', '2025-06-03 22:25:15'),
(28, 65, 'exit.svg', 'uploads/adj_683f7a195f5b9.svg', 'image/svg+xml', '2025-06-03 22:41:29'),
(29, 67, 'Captura.JPG', 'uploads/img_683f7a3f32fbe.jpg', 'image/jpeg', '2025-06-03 22:42:07'),
(30, 68, 'atras.png', 'uploads/adj_68404ea8cf549.png', 'image/png', '2025-06-04 13:48:24'),
(31, 70, 'Captura.JPG', 'uploads/img_6840505c9825b.jpg', 'image/jpeg', '2025-06-04 13:55:40'),
(32, 70, 'atras.png', 'uploads/img_6840505c9994c.png', 'image/png', '2025-06-04 13:55:40'),
(33, 70, 'descargas.png', 'uploads/img_6840505c99c7d.png', 'image/png', '2025-06-04 13:55:40'),
(34, 70, 'descargar-removebg-preview.png', 'uploads/img_6840505c99f66.png', 'image/png', '2025-06-04 13:55:40'),
(35, 70, 'descargar.png', 'uploads/img_6840505c9a22c.png', 'image/png', '2025-06-04 13:55:40'),
(36, 70, 'icons8-usuarios-32.png', 'uploads/img_6840505c9b3dc.png', 'image/png', '2025-06-04 13:55:40'),
(37, 70, 'WhatsApp Image 2025-04-07 at 10.57.31 AM.jpeg', 'uploads/img_6840505c9b752.jpeg', 'image/jpeg', '2025-06-04 13:55:40'),
(38, 70, 'WhatsApp_Image_2025-03-07_at_8.46.15_AM-removebg-preview.png', 'uploads/img_6840505c9ba51.png', 'image/png', '2025-06-04 13:55:40'),
(39, 70, 'WhatsApp Image 2025-03-07 at 8.46.15 AM.jpeg', 'uploads/img_6840505c9bdd5.jpeg', 'image/jpeg', '2025-06-04 13:55:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casos`
--

CREATE TABLE `casos` (
  `id_caso` int(11) NOT NULL,
  `tipo_caso` varchar(100) NOT NULL,
  `caso` varchar(100) NOT NULL,
  `prioridad_caso` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `casos`
--

INSERT INTO `casos` (`id_caso`, `tipo_caso`, `caso`, `prioridad_caso`) VALUES
(1, 'Conectividad', 'Conexión a internet', 'Alta'),
(2, 'Conectividad', 'Activación punto de red cableado', 'Media'),
(3, 'Conectividad', 'Fallo conexión a internet', 'Media'),
(4, 'Ofimática', 'Office', 'Alta'),
(5, 'Ofimática', 'Adobe Acrobat pro', 'Media'),
(6, 'Ofimática', 'Excel en general', 'Baja'),
(7, 'Aplicativos especiales', 'Siif nación', 'Alta'),
(8, 'Aplicativos especiales', 'Token virtual', 'Alta'),
(9, 'Aplicativos especiales', 'Dsginer', 'Alta'),
(10, 'Aplicativos especiales', 'VPN', 'Media'),
(11, 'Aplicativos especiales', 'Elogic', 'Alta'),
(12, 'Aplicativos especiales', 'Otros', 'Media'),
(13, 'Hardware', 'Mantenimiento', 'Baja'),
(14, 'Hardware', 'Impresora: falla - instalación – drivers', 'Media'),
(15, 'Hardware', 'Instalación equipo – especifique cual', 'Baja'),
(16, 'Hardware', 'Traslado de equipo', 'Baja'),
(17, 'Software', 'Mantenimiento lógico', 'Media'),
(18, 'Software', 'Instalación de software-drivers', 'Media'),
(19, 'Requerimiento de actividad', 'Cual (escriba su requerimiento)', 'Baja');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id_funcionario` int(11) NOT NULL,
  `numero_documento` varchar(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(11) NOT NULL,
  `cargo` varchar(12) NOT NULL,
  `fecha_ultima_sesion` datetime NOT NULL,
  `estado` varchar(9) NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `funcionarios`
--

INSERT INTO `funcionarios` (`id_funcionario`, `numero_documento`, `nombre`, `correo`, `telefono`, `cargo`, `fecha_ultima_sesion`, `estado`) VALUES
(4, '1114149791134', 'JERONIMO LO MAS BELLO', 'pruebacorreo@gmail.com', '3154598499', 'VIGILANTE', '2025-05-05 15:36:12', 'ACTIVO'),
(5, '11141497911324', 'JERONIMO PIZARRO PRUEBALO', 'pruebacorreo@sena.edu.co', '3154598499', 'VIGILANTE', '2025-05-05 15:37:43', 'ACTIVO'),
(6, '222222', 'PRUEBA DE JERO', 'pruebacorreo@gmail.com', '3154598499', 'VIGILANTE', '2025-05-06 08:40:01', 'ACTIVO'),
(7, '44444444444', 'JERONIMO ALEXANDER PIZARRO MARIN', 'marinjeronimo44@gmail.com', '3154598499', 'VIGILANTE', '2025-05-06 09:28:04', 'ACTIVO'),
(8, '121212121', 'JERONIMO ALEXANDER PIZARRO MARIN', 'marinjeronimo44@gmail.com', '3154598499', 'VIGILANTE', '2025-05-06 09:30:18', 'ACTIVO'),
(9, '23232323', 'JERONIMO PIZARRO', 'marinjeronimo44@gmail.com', '3154598499', 'VIGILANTE', '2025-05-06 09:31:44', 'ACTIVO'),
(11, '1114149720', 'JER GFD', 'juanmontolla531@sena.edu.co', '3154598499', 'VIGILANTE', '2025-05-06 09:43:32', 'ACTIVO'),
(12, '222223444', 'JERONIMO PIZARRO', 'marinjeronimo44@gmail.com', '3154598499', 'VIGILANTE', '2025-05-08 09:02:44', 'ACTIVO'),
(13, '1114149721', 'JERONIMO ALEXANDER PIZARRO MARIN', 'marinjeronimo44@gmail.com', '3154598499', 'VIGILANTE', '2025-05-08 09:21:59', 'ACTIVO'),
(14, '22222777', 'ñero ñero ', 'marinjero@sena.edu.co', '3154598499', 'VIGILANTE', '2025-05-14 08:47:03', 'ACTIVO'),
(15, '666464', 'JUAN DAVID TILMAS', 'titi@sena.edu.co', '3154598499', 'VIGILANTE', '2025-05-21 09:22:06', 'ACTIVO'),
(16, '4671445', 'PAPIJERO', 'juandavidtilmans@sena.edu.co', '311457555', 'VIGILANTE', '2025-05-21 09:32:54', 'ACTIVO'),
(18, '6664421', 'PERUEBA PEPE', 'probando@sena.edu.co', '3154598499', 'VIGILANTE', '2025-05-21 10:38:52', 'ACTIVO'),
(20, '1114149791', 'JERONIMO PIZARRO MAKASXZQQ', 'marinjeronimo444@sena.edu.co', '3154598499', 'VIGILANTE', '2025-05-26 11:12:20', 'ACTIVO'),
(21, '12312312311', 'DFFDASDASD', 'dsf@sena.edu.co', '1231231231', 'VIGILANTE', '0000-00-00 00:00:00', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_ticket`
--

CREATE TABLE `historial_ticket` (
  `id_historial` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `tipo_evento` enum('comentario_inicial','comentario','edicion_comentario','cambio_estado','reasignacion','adjunto','otro') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `usuario` varchar(100) NOT NULL,
  `fecha_evento` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) DEFAULT NULL,
  `usuario_anterior` varchar(100) DEFAULT NULL,
  `usuario_nuevo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `historial_ticket`
--

INSERT INTO `historial_ticket` (`id_historial`, `id_ticket`, `tipo_evento`, `descripcion`, `usuario`, `fecha_evento`, `estado_anterior`, `estado_nuevo`, `usuario_anterior`, `usuario_nuevo`) VALUES
(1, 59, 'comentario_inicial', 'jeronimo el mejor', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:17:50', NULL, NULL, NULL, NULL),
(2, 60, 'comentario_inicial', 'epa', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:20:51', NULL, NULL, NULL, NULL),
(3, 61, 'comentario_inicial', '', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:24:17', NULL, NULL, NULL, NULL),
(4, 62, 'comentario_inicial', '', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:28:57', NULL, NULL, NULL, NULL),
(5, 58, 'comentario', 'oe', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:43:53', NULL, NULL, NULL, NULL),
(6, 58, 'comentario', 'dsf', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:44:10', NULL, NULL, NULL, NULL),
(7, 63, 'comentario_inicial', ' la verdad no sirve', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:45:01', NULL, NULL, NULL, NULL),
(8, 63, 'comentario', 'si servia solo era que no tenia conectado la cosa esa', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:45:45', NULL, NULL, NULL, NULL),
(9, 63, 'cambio_estado', 'Cambio de estado: ABIERTO ➔ EN PAUSA', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:45:50', NULL, NULL, NULL, NULL),
(10, 63, 'cambio_estado', 'Cambio de estado: EN PAUSA ➔ ABIERTO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:46:15', NULL, NULL, NULL, NULL),
(11, 63, 'comentario', 'lo volvi a abir', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:46:15', NULL, NULL, NULL, NULL),
(12, 63, 'cambio_estado', 'Cambio de estado: ABIERTO ➔ EN PROCESO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:46:44', NULL, NULL, NULL, NULL),
(13, 63, 'cambio_estado', 'Cambio de estado: EN PROCESO ➔ ABIERTO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:47:15', NULL, NULL, NULL, NULL),
(14, 35, 'cambio_estado', 'Cambio de estado: EN PROCESO ➔ ABIERTO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:47:30', NULL, NULL, NULL, NULL),
(15, 63, 'cambio_estado', 'Cambio de estado: EN PROCESO ➔ ABIERTO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 20:50:10', NULL, NULL, NULL, NULL),
(16, 63, 'comentario', 'ere', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:02:57', NULL, NULL, NULL, NULL),
(17, 63, 'cambio_estado', 'Cambio de estado: ABIERTO ➔ CERRADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:04:08', NULL, NULL, NULL, NULL),
(18, 64, 'comentario_inicial', 'oe mire que la fotro no se ve', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:04:41', NULL, NULL, NULL, NULL),
(19, 65, 'comentario_inicial', 'oeeeee', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:05:04', NULL, NULL, NULL, NULL),
(20, 66, 'comentario_inicial', '', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:05:52', NULL, NULL, NULL, NULL),
(21, 66, 'comentario', 'wer', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:06:02', NULL, NULL, NULL, NULL),
(22, 66, 'comentario', 'ewrew', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:06:04', NULL, NULL, NULL, NULL),
(23, 66, 'comentario', 'ewr', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:06:05', NULL, NULL, NULL, NULL),
(24, 66, 'comentario', 'wer', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:06:07', NULL, NULL, NULL, NULL),
(25, 66, 'comentario', 'erwer', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:06:12', NULL, NULL, NULL, NULL),
(26, 66, 'cambio_estado', 'Cambio de estado: ABIERTO ➔ CERRADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:07:34', NULL, NULL, NULL, NULL),
(27, 67, 'comentario_inicial', '', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:11:23', NULL, NULL, NULL, NULL),
(28, 68, 'comentario_inicial', '', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:14:27', NULL, NULL, NULL, NULL),
(29, 69, 'comentario_inicial', 'Sin observaciones', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:18:51', NULL, NULL, NULL, NULL),
(30, 69, 'comentario', 'rg', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:26:30', NULL, NULL, NULL, NULL),
(31, 69, 'comentario', 'rg', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:26:32', NULL, NULL, NULL, NULL),
(32, 69, 'comentario', 'rg', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:26:33', NULL, NULL, NULL, NULL),
(33, 69, 'comentario', 'rr\r\nr\r\nr\r\nr\r\n\r\nr\r\nr\r\nr\r\n\r\nr\r\nr\r\nr\r\n\r\nr\r\nr\r\n\r\nr', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:26:57', NULL, NULL, NULL, NULL),
(34, 69, 'comentario', 'd', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:28:57', NULL, NULL, NULL, NULL),
(35, 69, 'comentario', 'd', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:28:59', NULL, NULL, NULL, NULL),
(36, 69, 'comentario', 'd', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:38:34', NULL, NULL, NULL, NULL),
(37, 70, 'comentario_inicial', 'wer', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:38:58', NULL, NULL, NULL, NULL),
(38, 71, 'comentario_inicial', 'Sin observaciones', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:39:48', NULL, NULL, NULL, NULL),
(39, 71, 'comentario', '.', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:40:16', NULL, NULL, NULL, NULL),
(40, 71, 'comentario', 's', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:44:00', NULL, NULL, NULL, NULL),
(41, 71, 'cambio_estado', 'Cambio de estado: ABIERTO ➔ CERRADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:46:32', NULL, NULL, NULL, NULL),
(42, 71, 'cambio_estado', 'Cambio de estado:  ➔ CERRADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:46:51', NULL, NULL, NULL, NULL),
(43, 71, 'cambio_estado', 'Cambio de estado:  ➔ CERRADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:46:54', NULL, NULL, NULL, NULL),
(44, 71, 'cambio_estado', 'Cambio de estado:  ➔ ABIERTO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:46:59', NULL, NULL, NULL, NULL),
(45, 71, 'comentario', 'jhk', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:46:59', NULL, NULL, NULL, NULL),
(46, 71, 'comentario', 'jhk', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:47:02', NULL, NULL, NULL, NULL),
(47, 71, 'comentario', 'w', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:50:09', NULL, NULL, NULL, NULL),
(48, 71, 'comentario', 'w', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:50:22', NULL, NULL, NULL, NULL),
(49, 71, 'cambio_estado', 'Cambio de estado: ABIERTO ➔ CERRADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:50:32', NULL, NULL, NULL, NULL),
(50, 67, 'cambio_estado', 'Cambio de estado: EN PROCESO ➔ ABIERTO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:51:01', NULL, NULL, NULL, NULL),
(51, 67, 'comentario', 'e', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:51:05', NULL, NULL, NULL, NULL),
(52, 67, 'cambio_estado', 'Cambio de estado: ABIERTO ➔ EN PAUSA', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:51:10', NULL, NULL, NULL, NULL),
(53, 67, 'cambio_estado', 'Cambio de estado: EN PAUSA ➔ CERRADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:51:14', NULL, NULL, NULL, NULL),
(54, 57, 'comentario', 's', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:57:48', NULL, NULL, NULL, NULL),
(55, 57, 'cambio_estado', 'Cambio de estado: EN PROCESO ➔ ABIERTO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 21:57:58', NULL, NULL, NULL, NULL),
(56, 56, 'cambio_estado', 'Cambio de estado: EN PROCESO ➔ CERRADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:02:24', NULL, NULL, NULL, NULL),
(57, 37, 'cambio_estado', 'Cambio de estado: EN PROCESO ➔ SOLUCIONADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:03:51', NULL, NULL, NULL, NULL),
(58, 32, 'cambio_estado', 'Cambio de estado: EN PAUSA ➔ SOLUCIONADO', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:04:03', NULL, NULL, NULL, NULL),
(59, 32, 'comentario', 'A', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:04:17', NULL, NULL, NULL, NULL),
(60, 32, 'comentario', 'S', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:05:00', NULL, NULL, NULL, NULL),
(61, 72, 'comentario_inicial', 'Sin observaciones', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:07:46', NULL, NULL, NULL, NULL),
(62, 73, 'comentario_inicial', 'q', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:15:26', NULL, NULL, NULL, NULL),
(63, 74, 'comentario_inicial', 'qa', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:16:31', NULL, NULL, NULL, NULL),
(64, 75, 'comentario_inicial', 'o3', 'JERONIMO ALEXANDER PIZARRO', '2025-06-03 22:25:15', NULL, NULL, NULL, NULL),
(65, 76, 'comentario_inicial', 'Sin observaciones', 'JERONIMO EL MEJOR', '2025-06-03 22:41:29', NULL, NULL, NULL, NULL),
(66, 76, 'comentario', 'ññp{{ño{', 'JERONIMO EL MEJOR', '2025-06-03 22:41:52', NULL, NULL, NULL, NULL),
(67, 76, 'comentario', '-', 'JERONIMO EL MEJOR', '2025-06-03 22:42:07', NULL, NULL, NULL, NULL),
(68, 77, 'comentario_inicial', 'O3', 'ñero ñero ', '2025-06-04 13:48:24', NULL, NULL, NULL, NULL),
(69, 77, 'comentario', 'ewrqwer\r\nw\r\nw\r\nw\r\n\r\nw\r\nw\r\nw\r\nw\r\n\r\nw', 'JERONIMO EL MEJOR', '2025-06-04 13:55:11', NULL, NULL, NULL, NULL),
(70, 77, 'comentario', 'la verdad lo qeu pasa es que eso no carga', 'JERONIMO EL MEJOR', '2025-06-04 13:55:40', NULL, NULL, NULL, NULL),
(71, 77, 'cambio_estado', 'Cambio de estado: EN PROCESO ➔ ABIERTO', 'JERONIMO EL MEJOR', '2025-06-04 13:56:02', NULL, NULL, NULL, NULL),
(72, 77, 'cambio_estado', 'Cambio de estado: ABIERTO ➔ EN PROCESO', 'JERONIMO EL MEJOR', '2025-06-04 13:56:07', NULL, NULL, NULL, NULL),
(73, 76, 'comentario', 'oe', 'JERONIMO ALEXANDER PIZARRO MARIN MARIN', '2025-06-04 16:15:38', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id_ticket` int(11) NOT NULL,
  `codigo_ticket` varchar(15) NOT NULL,
  `tipo_caso` varchar(100) NOT NULL,
  `prioridad` varchar(10) NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `doc_usuario_creador` varchar(16) NOT NULL,
  `estado` enum('Abierto','En proceso','Solucionado','En pausa') DEFAULT 'Abierto',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `doc_usuario_atencion` varchar(16) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id_ticket`, `codigo_ticket`, `tipo_caso`, `prioridad`, `ubicacion`, `doc_usuario_creador`, `estado`, `fecha_creacion`, `doc_usuario_atencion`, `fecha_actualizacion`) VALUES
(1, 'CON202500001', 'Conectividad - Conexión a internet', 'Alta', 'Auditorio', 'JERONIMO ALEXAND', 'En proceso', '2025-05-04 18:43:49', 'JERONIMO ALEXAND', '2025-05-19 21:25:30'),
(2, 'CON202500002', 'Conectividad - Conexión a internet', 'Alta', 'Auditorio', '', 'En proceso', '2025-05-07 18:47:14', '111414979192', '2025-05-22 18:52:03'),
(3, 'CON202500003', 'Conectividad - Conexión a internet', 'Alta', 'Auditorio', '', 'En proceso', '2025-05-06 18:47:15', '', '2025-05-13 15:55:09'),
(4, 'CON202500004', 'Conectividad - Conexión a internet', 'Alta', 'Auditorio', '', 'En proceso', '2025-05-06 18:47:26', '', '2025-05-13 13:50:49'),
(5, 'APL202500001', 'Aplicativos especiales - Elogic', 'Alta', 'Sala de Instructores', '', 'En pausa', '2025-05-06 18:51:11', '', '2025-05-14 15:55:09'),
(6, 'APL202500002', 'Aplicativos especiales - Elogic', 'Alta', 'Sala de Instructores', '', 'Abierto', '2025-05-06 18:51:13', '', '2025-05-13 23:25:09'),
(7, 'APL202500003', 'Aplicativos especiales - Elogic', 'Alta', 'Sala de Instructores', 'PROBANDO MNNBV', 'En proceso', '2025-05-06 18:51:14', 'JERONIMO ALEXAND', '2025-05-13 19:19:42'),
(8, 'APL202500004', 'Aplicativos especiales - Otros', 'Media', 'Talento Humano', '', 'En pausa', '2025-05-06 19:03:35', '', '2025-05-14 15:55:09'),
(9, 'SOF202500001', 'Software - Instalación de software-drivers', 'Media', 'Campesena', '', 'En pausa', '2025-05-06 19:05:38', '', '2025-05-14 15:55:09'),
(10, 'OFI202500001', 'Ofimática - Office', 'Alta', 'Sala de Instructores', '', 'Abierto', '2025-05-06 19:06:37', '', '2025-05-13 13:59:09'),
(11, 'OFI202500002', 'Ofimática - Office', 'Alta', 'Sala de Instructores', '', 'En proceso', '2025-05-13 21:33:01', '', '2025-05-15 21:33:01'),
(12, 'OFI202500003', 'Ofimática - Office', 'Alta', 'Sala de Instructores', '', 'Abierto', '2025-05-06 19:06:40', '', '2025-05-13 15:55:09'),
(13, 'CON202500005', 'Conectividad - Fallo conexión a internet', 'Media', 'Biblioteca', '', 'En proceso', '2025-05-06 19:07:24', '', '2025-05-13 21:37:09'),
(14, 'OFI202500004', 'Ofimática - Office', 'Alta', 'Cuarto Vigilancia', '', 'En pausa', '2025-05-06 19:07:53', '', '2025-05-14 15:55:09'),
(15, 'CON20250006', 'Conectividad - Fallo conexión a internet', 'Media', 'Cuarto Vigilancia', '', 'En pausa', '2025-05-06 19:10:08', '', '2025-05-14 15:55:09'),
(16, 'CON20250007', 'Conectividad - Fallo conexión a internet', 'Media', 'Cuarto Vigilancia', '', 'En pausa', '2025-05-06 19:11:21', '', '2025-05-12 15:55:09'),
(17, 'CON20250008', 'Conectividad - Fallo conexión a internet', 'Media', 'Cuarto Vigilancia', '', 'En pausa', '2025-05-06 19:12:03', '', '2025-05-14 15:55:09'),
(18, 'SOF20250009', 'Software - Mantenimiento lógico', 'Media', 'Biblioteca', '', 'En proceso', '2025-05-06 19:12:30', '', '2025-05-13 21:40:09'),
(19, 'SOF20250010', 'Software - Mantenimiento lógico', 'Media', 'Biblioteca', '', 'En pausa', '2025-05-06 19:15:43', '', '2025-05-14 15:55:09'),
(20, 'SOF20250011', 'Software - Mantenimiento lógico', 'Media', 'Biblioteca', '', 'En pausa', '2025-05-06 19:24:16', '', '2025-05-14 15:55:09'),
(21, 'CON20250012', 'Conectividad - Activación punto de red cableado', 'Media', 'Biblioteca', '', 'En pausa', '2025-05-06 19:24:29', '', '2025-05-14 15:55:09'),
(22, 'REQ20250013', 'Requerimiento de actividad - Cual (escriba su requerimiento)', 'Baja', 'Biblioteca', '', 'En pausa', '2025-05-06 19:26:10', '', '2025-05-14 15:55:09'),
(23, 'CON20250014', 'Conectividad - Activación punto de red cableado', 'Media', 'Cuarto Vigilancia', '', 'En proceso', '2025-05-13 21:32:01', '4478521', '2025-05-28 13:38:40'),
(24, 'SOF20250015', 'Software - Instalación de software-drivers', 'Media', 'Edificio nuevo (oficina de contratacion)', '', 'En proceso', '2025-05-06 20:57:16', '', '2025-05-13 21:38:09'),
(25, 'OFI20250016', 'Ofimática - Office', 'Alta', 'Sennova', '', 'En pausa', '2025-05-07 15:04:57', '', '2025-05-14 15:55:09'),
(26, 'REQ20250017', 'Requerimiento de actividad - Cual (escriba su requerimiento)', 'Baja', 'Porteria', '', 'En pausa', '2025-05-07 17:02:40', '', '2025-05-14 15:55:09'),
(27, 'OFI20250018', 'Ofimática - Office', 'Alta', 'Edificio nuevo (oficina ludwig mauricio)', '', 'En pausa', '2025-05-07 19:32:19', '', '2025-05-14 15:55:09'),
(28, 'OFI20250019', 'Ofimática - Office', 'Alta', 'Biblioteca', '', 'En pausa', '2025-05-08 13:59:34', '', '2025-05-14 15:55:09'),
(29, 'REQ20250020', 'Requerimiento de actividad - Cual (escriba su requerimiento)', 'Baja', 'Cuarto Vigilancia', '', 'En pausa', '2025-05-08 14:03:22', '', '2025-05-14 15:55:09'),
(30, 'SOF20250021', 'Software - Mantenimiento lógico', 'Media', 'Certificacion Academica', '', 'En pausa', '2025-05-08 14:17:11', '', '2025-05-14 15:55:09'),
(31, 'CON20250022', 'Conectividad - Fallo conexión a internet', 'Media', 'Cuarto Vigilancia', '', 'En proceso', '2025-05-09 14:22:15', '', '2025-05-13 21:29:28'),
(32, 'CON20250023', 'Conectividad - Fallo conexión a internet', 'Media', 'Cuarto Vigilancia', '', 'Solucionado', '2025-05-14 15:46:11', '', '2025-06-03 22:04:03'),
(33, 'CON20250024', 'Conectividad - Activación punto de red cableado', 'Media', 'Cuarto Vigilancia', '', 'Abierto', '2025-05-12 16:03:58', '', '2025-05-14 21:28:10'),
(34, 'OFI20250025', 'Ofimática - Office', 'Alta', 'Archivo Gestion Documental', '', 'En proceso', '2025-05-13 19:26:49', '', '2025-05-14 21:24:57'),
(35, 'CON20250026', 'Conectividad - Fallo conexión a internet', 'Media', 'Auditorio', '', 'Abierto', '2025-05-14 21:46:46', '1114149792', '2025-06-03 20:47:30'),
(36, 'OFI20250027', 'Ofimática - Office', 'Alta', 'Biblioteca', '', 'En proceso', '2025-05-16 14:04:02', '1114149792', '2025-05-28 15:31:46'),
(37, 'OFI20250028', 'Ofimática - Adobe Acrobat pro', 'Media', 'Almacen', '', 'Solucionado', '2025-05-16 14:04:05', '33215456', '2025-06-03 22:03:51'),
(38, 'OFI20250029', 'Ofimática - Office', 'Alta', 'Porteria', '', 'En proceso', '2025-05-16 19:50:52', '1114149792', '2025-05-26 14:21:40'),
(39, 'CON20250030', 'Conectividad - Fallo conexión a internet', 'Media', 'Almacen', '', 'En proceso', '2025-05-16 20:01:43', '1114149792', '2025-05-28 15:17:50'),
(40, 'OFI20250031', 'Ofimática - Adobe Acrobat pro', 'Media', 'Sennova', '', 'En proceso', '2025-05-16 20:03:06', '11141497923', '2025-05-21 19:07:17'),
(41, 'OFI20250032', 'Ofimática - Office', 'Alta', 'Sala de Instructores', 'Jeronimo', 'En proceso', '2025-05-16 20:17:32', '111414979192', '2025-05-22 13:34:58'),
(42, 'CON20250033', 'Conectividad - Conexión a internet', 'Alta', 'Biblioteca', '22222777', 'En proceso', '2025-05-21 13:49:08', '1114149792', '2025-05-22 16:16:57'),
(43, 'CON20250034', 'Conectividad - Activación punto de red cableado', 'Media', 'Auditorio', '1114149791', 'En proceso', '2025-05-21 13:56:42', '11141497923', '2025-05-21 19:06:53'),
(44, 'APL20250035', 'Aplicativos especiales - Dsginer', 'Alta', 'Edificio nuevo (coordinacion administrativa)', '1114149791', 'En proceso', '2025-05-21 14:00:02', '1114149792', '2025-05-21 22:19:34'),
(45, 'OFI20250036', 'Ofimática - Office', 'Alta', 'Almacen', '4671445', 'En proceso', '2025-05-21 14:46:05', '111414979192', '2025-05-21 19:26:34'),
(46, 'CON20250037', 'Conectividad - Fallo conexión a internet', 'Media', 'Cuarto Vigilancia', '22222777', 'En proceso', '2025-05-21 15:21:50', '11141497910', '2025-05-21 19:20:08'),
(47, 'OFI20250038', 'Ofimática - Adobe Acrobat pro', 'Media', 'Sennova', '1114149791', 'En proceso', '2025-05-21 15:28:59', '66666666666', '2025-05-21 19:01:49'),
(48, 'CON20250039', 'Conectividad - Activación punto de red cableado', 'Media', 'Biblioteca', '22222777', 'En proceso', '2025-05-21 15:53:20', '111414444', '2025-05-21 19:14:02'),
(49, 'OFI20250040', 'Ofimática - Adobe Acrobat pro', 'Media', 'Bienestar para el aprendiz', '22222777', 'En proceso', '2025-05-21 16:33:24', '1114149754', '2025-05-21 19:01:19'),
(50, 'REQ20250041', 'Requerimiento de actividad - Cual (escriba su requerimiento)', 'Baja', 'Edificio nuevo (coordinacion formacion profesional)', '1114149791', 'En proceso', '2025-05-22 16:39:10', '33215456', '2025-05-23 17:03:00'),
(51, 'CON20250042', 'Conectividad - Conexión a internet', 'Alta', 'Biblioteca', '1114149791', 'En proceso', '2025-05-23 14:10:16', '33215456', '2025-05-23 16:59:20'),
(52, 'APL20250043', 'Aplicativos especiales - Token virtual', 'Alta', 'Sennova', '1114149791', 'En proceso', '2025-05-26 14:31:41', '444123123', '2025-05-28 13:38:57'),
(53, 'APL20250044', 'Aplicativos especiales - VPN', 'Media', 'Biblioteca', '1114149791', 'En proceso', '2025-05-26 15:17:57', '1114149792', '2025-05-28 15:16:46'),
(54, 'SOF20250045', 'Software - Instalación de software-drivers', 'Media', 'Campesena', '1114149791', 'En proceso', '2025-05-26 15:21:13', '33215456', '2025-05-28 14:44:35'),
(55, 'OFI20250046', 'Ofimática - Adobe Acrobat pro', 'Media', 'Cuarto Vigilancia', '1114149792', 'En proceso', '2025-05-28 15:18:08', '1114149792', '2025-05-28 15:18:59'),
(56, 'OFI20250047', 'Ofimática - Excel en general', 'Baja', 'Certificacion Academica', '22222777', '', '2025-05-28 17:50:46', '1114149792', '2025-06-03 22:02:24'),
(57, 'OFI20250048', 'Ofimática - Office', 'Alta', 'Cuarto Vigilancia', '1114149791', 'Abierto', '2025-05-28 19:56:50', '4478521', '2025-06-03 21:57:58'),
(58, 'SOF20250049', 'Software - Instalación de software-drivers', 'Media', 'Porteria', '1114149791', 'Abierto', '2025-05-30 14:18:10', '', '2025-05-30 14:18:10'),
(59, 'OFI20250050', 'Ofimática - Office', 'Alta', 'Edificio nuevo', '1114149791', '', '2025-06-03 20:17:50', '', '2025-06-03 21:55:11'),
(60, 'CON20250051', 'Conectividad - Fallo conexión a internet', 'Media', 'Bizerta', '1114149791', 'Abierto', '2025-06-03 20:20:51', '', '2025-06-03 20:20:51'),
(61, 'CON20250052', 'Conectividad - Activación punto de red cableado', 'Media', 'Cuarto Vigilancia', '1114149791', 'Abierto', '2025-06-03 20:24:17', '', '2025-06-03 20:24:17'),
(62, 'OFI20250053', 'Ofimática - Office', 'Alta', 'Ambientes', '1114149791', 'Abierto', '2025-06-03 20:28:57', '', '2025-06-03 20:28:57'),
(63, 'OFI20250054', 'Ofimática - Office', 'Alta', 'Diseño curricular', '1114149791', '', '2025-06-03 20:45:01', '4478521', '2025-06-03 21:52:47'),
(64, 'CON20250055', 'Conectividad - Conexión a internet', 'Alta', 'Bizerta', '1114149791', 'Abierto', '2025-06-03 21:04:41', '', '2025-06-03 21:04:41'),
(65, 'OFI20250056', 'Ofimática - Adobe Acrobat pro', 'Media', 'Almacen', '1114149791', 'Abierto', '2025-06-03 21:05:04', '', '2025-06-03 21:05:04'),
(66, 'CON20250057', 'Conectividad - Activación punto de red cableado', 'Media', 'Archivo Gestion Documental', '1114149791', '', '2025-06-03 21:05:52', '', '2025-06-03 21:07:34'),
(67, 'SOF20250058', 'Software - Instalación de software-drivers', 'Media', 'Ambientes', '1114149791', '', '2025-06-03 21:11:23', '1114149792', '2025-06-03 21:51:14'),
(68, 'OFI20250059', 'Ofimática - Office', 'Alta', 'Ambientes', '1114149791', 'Abierto', '2025-06-03 21:14:27', '', '2025-06-03 21:14:27'),
(69, 'CON20250060', 'Conectividad - Conexión a internet', 'Alta', 'Almacen', '1114149791', 'Abierto', '2025-06-03 21:18:51', '', '2025-06-03 21:18:51'),
(70, 'REQ20250061', 'Requerimiento de actividad - Cual (escriba su requerimiento)', 'Baja', 'Diseño curricular', '1114149791', 'Abierto', '2025-06-03 21:38:58', '', '2025-06-03 21:38:58'),
(71, 'REQ20250062', 'Requerimiento de actividad - Cual (escriba su requerimiento)', 'Baja', 'Cuarto Vigilancia', '1114149791', '', '2025-06-03 21:39:48', '', '2025-06-03 21:50:32'),
(72, 'HAR20250063', 'Hardware - Traslado de equipo', 'Baja', 'Ambientes', '1114149791', 'Abierto', '2025-06-03 22:07:46', '', '2025-06-03 22:07:46'),
(73, 'CON20250064', 'Conectividad - Activación punto de red cableado', 'Media', 'Administracion Educativa', '1114149791', 'Abierto', '2025-06-03 22:15:26', '', '2025-06-03 22:15:26'),
(74, 'CON20250065', 'Conectividad - Conexión a internet', 'Alta', 'Ambientes', '1114149791', 'Abierto', '2025-06-03 22:16:31', '', '2025-06-03 22:16:31'),
(75, 'CON20250066', 'Conectividad - Fallo conexión a internet', 'Media', 'Ambientes', '1114149791', 'En proceso', '2025-06-03 22:25:15', '1114149792', '2025-06-03 22:37:58'),
(76, 'CON20250067', 'Conectividad - Activación punto de red cableado', 'Media', 'Archivo Gestion Documental', '1114149792', 'Abierto', '2025-06-03 22:41:29', '', '2025-06-03 22:41:29'),
(77, 'APL20250068', 'Aplicativos especiales - Token virtual', 'Alta', 'Ambientes', '22222777', 'En proceso', '2025-06-04 13:48:24', '1114149792', '2025-06-04 13:56:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `documento` varchar(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `clave` varchar(16) NOT NULL,
  `cargo` enum('ANALISTA','DINAMIZADOR') NOT NULL,
  `fecha_ultima_sesion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(9) NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `documento`, `nombre`, `correo`, `telefono`, `clave`, `cargo`, `fecha_ultima_sesion`, `estado`) VALUES
(1, '1114149792', 'JERONIMO EL MEJOR', 'pruebacorreo@sena.edu.co', '3154598499', 'Jero312.sd', 'ANALISTA', '2025-05-05 18:52:31', 'ACTIVO'),
(2, '1114149791', 'JERONIMO ALEXANDER PIZARRO MARIN MARIN', 'marinjeronimo44@sena.edu.co', '3154598499', 'Jero312.sd', 'DINAMIZADOR', '2025-05-05 18:52:50', 'ACTIVO'),
(3, '111414979192', 'PAPITO JERO ABUELITO', 'juanmontolla531@sena.edu.co', '3154598499', 'ADGG214.a', 'DINAMIZADOR', '2025-05-05 19:08:28', 'ACTIVO'),
(6, '11141497910', 'PROBANDO EDICION CC', 'tilmaselmejore@sena.edu.co', '3154598499', '3eee121a97a0af9d', 'ANALISTA', '2025-05-08 14:19:05', 'ACTIVO'),
(10, '111421231', 'MILENA PEREA VV', 'pera@sena.edu.co', '3154598499', '69cdcb122b4575c4', 'ANALISTA', '2025-05-23 13:46:38', 'ACTIVO'),
(11, '33215456', 'KOKE JERO AA', 'marinjeronimo44@gmail.com', '3154598499', 'c47cc03d5164ee98', 'ANALISTA', '2025-05-23 14:02:52', 'ACTIVO'),
(13, '444123123', 'KK RARO MARIN', 'lilian@sena.edu.co', '3154987851', 'fa1caeca340af0a0', 'ANALISTA', '2025-05-23 14:14:04', 'ACTIVO'),
(14, '4478521', 'LOCO RIELME', 'lamin@sena.edu.co', '3125146874', '6e3bdf40b1199b2e', 'ANALISTA', '2025-05-23 14:17:42', 'ACTIVO'),
(15, '3364454', 'ASDSAD SFASDF', 'jero@sena.edu.co', '315424622', '329d58515242cf70', 'ANALISTA', '2025-05-23 14:26:27', 'ACTIVO'),
(16, '7889785', 'PRUEBA NUMERO DOSW', 'kk@sena.edu.co', '3124568788', '9f9c8f60e90cb942', 'ANALISTA', '2025-05-23 14:28:31', 'ACTIVO'),
(17, '4457149781', 'PRUBANDO REGISTRO JJ', 'registroprueba@sena.edu.co', '3154756433', 'fed9a05990164591', 'DINAMIZADOR', '2025-06-04 14:51:15', 'ACTIVO'),
(18, '7789749854', 'PRUEBA DE ENVIO', 'jeroenvia@sena.edu.co', '3124698466', '74e18855cb75e6d9', 'ANALISTA', '2025-06-04 15:33:09', 'ACTIVO');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  ADD PRIMARY KEY (`id_adjunto`),
  ADD KEY `id_historial` (`id_historial`);

--
-- Indices de la tabla `casos`
--
ALTER TABLE `casos`
  ADD PRIMARY KEY (`id_caso`);

--
-- Indices de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id_funcionario`);

--
-- Indices de la tabla `historial_ticket`
--
ALTER TABLE `historial_ticket`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_ticket` (`id_ticket`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id_ticket`),
  ADD UNIQUE KEY `codigo_ticket` (`codigo_ticket`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  MODIFY `id_adjunto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `casos`
--
ALTER TABLE `casos`
  MODIFY `id_caso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id_funcionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `historial_ticket`
--
ALTER TABLE `historial_ticket`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id_ticket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  ADD CONSTRAINT `archivos_adjuntos_ibfk_1` FOREIGN KEY (`id_historial`) REFERENCES `historial_ticket` (`id_historial`);

--
-- Filtros para la tabla `historial_ticket`
--
ALTER TABLE `historial_ticket`
  ADD CONSTRAINT `historial_ticket_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

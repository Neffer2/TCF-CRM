-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-12-2022 a las 00:16:55
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tfc_crm`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anos`
--

CREATE TABLE `anos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `anos`
--

INSERT INTO `anos` (`id`, `description`, `created_at`, `updated_at`) VALUES
(1, '2022', '2022-12-28 18:48:13', '2022-12-28 18:48:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `base_comerciales`
--

CREATE TABLE `base_comerciales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `nom_cliente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_proyecto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cod_cc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_proyecto` decimal(10,2) NOT NULL,
  `com_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `com_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `com_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_estado` bigint(20) UNSIGNED NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `dura_mes` date DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `base_comerciales`
--

INSERT INTO `base_comerciales` (`id`, `fecha`, `nom_cliente`, `nom_proyecto`, `cod_cc`, `valor_proyecto`, `com_1`, `com_2`, `com_3`, `id_estado`, `fecha_inicio`, `dura_mes`, `id_user`, `created_at`, `updated_at`) VALUES
(1, '2022-12-05', 'COMUNICACION CELULAR S A COMCEL S A', 'INSTALACION MATERIAL POP CALL- SEPT', '38211006', '12256356.00', 'LIZETH;30', 'JONY;70', NULL, 2, '2022-12-05', '2022-12-05', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(2, '2022-12-06', 'COMUNICACION CELULAR S A COMCEL S A', 'DÍA SIN CARRO FLORENCIA', '38211013', '768182.00', 'LIZETH;30', 'JONY;70', NULL, 2, '2022-12-06', '2022-12-06', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(3, '2022-12-07', 'COMUNICACION CELULAR S A COMCEL S A', 'ACTIVACION MINI FERIA KIT ADICIONALES', '38210905', '22660908.00', 'LIZETH;30', 'JONY;70', NULL, 6, '2022-12-07', '2022-12-07', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(4, '2022-12-08', 'COMUNICACION CELULAR S A COMCEL S A', 'TROPA RETAIL MEGA TOMAS', '38210809', '6944060.00', 'LIZETH;30', 'JONY;70', NULL, 6, '2022-12-08', '2022-12-08', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(5, '2022-12-09', 'COMUNICACION CELULAR S A COMCEL S A', 'SMARTPHONE LISTO PARA USAR - CARTAGENA', '38211007', '1950718.00', 'LIZETH;30', 'JONY;70', NULL, 7, '2022-12-09', '2022-12-09', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(6, '2022-12-10', 'COMUNICACION CELULAR S A COMCEL S A', 'SABANA  EVENTO ADMINISTRADORES', '38211204', '4890628.00', 'LIZETH;30', 'JONY;70', NULL, 2, '2022-12-10', '2022-12-10', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(7, '2022-12-11', 'COMUNICACION CELULAR S A COMCEL S A', 'SABANA ACTIVACIÓN CAV IBAGUE', '38211017', '1119042.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-11', '2022-12-11', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(8, '2022-12-12', 'COMUNICACION CELULAR S A COMCEL S A', 'OPERACION RELACIONAMIENTO-PARTIDO COLOM', '38210904', '14180998.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-12', '2022-12-12', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(9, '2022-12-13', 'COMUNICACION CELULAR S A COMCEL S A', 'PLAZA CLARO HBO MAX 16 SEP Y 23 OCT', '38210907', '12570964.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-13', '2022-12-13', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(10, '2022-12-14', 'COMUNICACION CELULAR S A COMCEL S A', ' COSTO VEHICULOS CLARO 2021', '38210407', '38600000.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-14', '2022-12-14', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(11, '2022-12-15', 'COMUNICACION CELULAR S A COMCEL S A', 'SABANA  MEGA TOMAS NOVIEMBRE FLORENCIA', '38211110', '2120000.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-15', '2022-12-15', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(12, '2022-12-16', 'COMUNICACION CELULAR S A COMCEL S A', 'DJ GUARACHA PARTE 2', '38210608', '52337791.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-16', '2022-12-16', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(13, '2022-12-17', 'COMUNICACION CELULAR S A COMCEL S A', 'PAGO PRESENTACION PAOLA JARA', '38210910', '46796591.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-17', '2022-12-17', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(14, '2022-12-18', 'COMUNICACION CELULAR S A COMCEL S A', 'TORTA ANICERSARIO GSS BOGOTA', '38210611', '1480820.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-18', '2022-12-18', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(15, '2022-12-19', 'COMUNICACION CELULAR S A COMCEL S A', 'R3 ACTIVACIONES JULIO', '38210707', '14886004.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-19', '2022-12-19', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25'),
(16, '2022-12-20', 'COMUNICACION CELULAR S A COMCEL S A', 'SABANA OCTUBRE CAVS', '38211008', '1914572.00', 'LIZETH;30', 'JONY;70', NULL, 3, '2022-12-20', '2022-12-20', 10, '2022-12-28 02:29:25', '2022-12-28 02:29:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_cuenta`
--

CREATE TABLE `estados_cuenta` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estados_cuenta`
--

INSERT INTO `estados_cuenta` (`id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'CERRADO', NULL, NULL),
(2, 'COTIZACION', NULL, NULL),
(3, 'EJECUCIONXFACTURAR', NULL, NULL),
(4, 'PERDIDO', NULL, NULL),
(5, 'PROPUESTA', NULL, NULL),
(6, 'VENTA', NULL, NULL),
(7, 'VENTAEJECUCIÓN', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `helisa`
--

CREATE TABLE `helisa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `tipo_doc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_doc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_tercero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `centro` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_centro_costo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debito` decimal(12,2) DEFAULT NULL,
  `credito` decimal(12,2) DEFAULT NULL,
  `comercial` bigint(20) UNSIGNED NOT NULL,
  `participacion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_factura` decimal(12,2) NOT NULL,
  `mes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `año` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comision` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `helisa`
--

INSERT INTO `helisa` (`id`, `fecha`, `tipo_doc`, `num_doc`, `concepto`, `identidad`, `nom_tercero`, `centro`, `nom_centro_costo`, `debito`, `credito`, `comercial`, `participacion`, `base_factura`, `mes`, `año`, `comision`, `created_at`, `updated_at`) VALUES
(13, '2022-01-01', 'NCEO', 'NCEO78', 'Devolución según: NCEO78', '901215899', 'FUN TO FUNDRAISING SAS', 'B7211201', 'FACER UNICEF 2021', '-93433397.00', NULL, 9, '1', '1.00', 'Enero ', '2022', '-1868667.94', '2022-12-28 03:25:50', '2022-12-28 03:25:50'),
(14, '2022-02-02', 'NCEO', 'NCEO73', 'Devolución según: NCEO73', '890903939', 'GASEOSAS POSADA TOBON S A', '44210901', 'PLAN VISIBILIDAD FLANGER', '-233912045.00', '0.00', 12, '0.5', '1.00', 'Febrero', '2022', '-2339120.45', '2022-12-28 03:25:50', '2022-12-28 03:25:50'),
(15, '2022-05-03', 'NCEO', 'NCEO73', 'Devolución según: NCEO73', '890903939', 'GASEOSAS POSADA TOBON S A', '44210901', 'PLAN VISIBILIDAD FLANGER', '-233912045.00', '0.00', 13, '0.5', '1.00', 'Marzo', '2022', '-2339120.45', '2022-12-28 03:25:50', '2022-12-28 03:25:50'),
(16, '2022-05-04', 'FEVT', 'FEVT644', 'Venta Según: FEVT644', '890920304', 'PEPSICO ALIMENTOS COLOMBIA LTDA', '11210201', 'LOGISTICA AMARRES FEBRERO', '0.00', '6596571.00', 8, '0.5', '1.00', 'Abril', '2022', '65965.71', '2022-12-28 03:25:50', '2022-12-28 03:25:50'),
(17, '2022-05-05', 'FEVT', 'FEVT644', 'Venta Según: FEVT644', '890920304', 'PEPSICO ALIMENTOS COLOMBIA LTDA', '11210201', 'LOGISTICA AMARRES FEBRERO', '0.00', '6596571.00', 11, '0.5', '1.00', 'Mayo', '2022', '65965.71', '2022-12-28 03:25:50', '2022-12-28 03:25:50'),
(18, '2022-05-06', 'FEVT', 'FEVT645', 'Venta Según: FEVT645', '890903939', 'GASEOSAS POSADA TOBON S A', '44210901', 'PLAN VISIBILIDAD FLANGER', '0.00', '206400717.00', 8, '0.5', '1.00', 'Mayo', '2022', '2064007.17', '2022-12-28 03:25:50', '2022-12-28 03:25:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `meses`
--

CREATE TABLE `meses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ano_id` bigint(20) UNSIGNED NOT NULL,
  `f_inicio` date DEFAULT NULL,
  `f_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `meses`
--

INSERT INTO `meses` (`id`, `description`, `identifier`, `ano_id`, `f_inicio`, `f_fin`, `created_at`, `updated_at`) VALUES
(1, 'Enero', '1', 1, '2022-01-01', '2022-01-31', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(2, 'Febrero', '2', 1, '2022-02-01', '2022-02-28', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(3, 'Marzo', '3', 1, '2022-03-01', '2022-03-31', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(4, 'Abril', '4', 1, '2022-04-01', '2022-04-30', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(5, 'Mayo', '5', 1, '2022-05-01', '2022-05-31', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(6, 'Junio', '6', 1, '2022-06-01', '2022-06-30', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(7, 'Julio', '7', 1, '2022-07-01', '2022-07-31', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(8, 'Agosto', '8', 1, '2022-08-01', '2022-08-31', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(9, 'Septiembre', '9', 1, '2022-09-01', '2022-09-30', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(10, 'Octubre', '10', 1, '2022-10-01', '2022-10-31', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(11, 'Noviembre', '11', 1, '2022-11-01', '2022-11-30', '2022-12-28 18:48:13', '2022-12-28 18:51:20'),
(12, 'Diciembre', '12', 1, '2022-12-01', '2022-12-31', '2022-12-28 18:48:13', '2022-12-28 18:51:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2022_11_29_214201_create_roles_user_table', 1),
(8, '2022_11_28_141640_create_estados_cuenta_table', 2),
(30, '2014_10_12_000000_create_users_table', 3),
(31, '2014_10_12_100000_create_password_resets_table', 3),
(32, '2019_08_19_000000_create_failed_jobs_table', 3),
(33, '2022_11_29_214200_create_base_comerciales_table', 3),
(40, '2022_12_16_234442_create_anos_table', 4),
(41, '2022_12_17_232130_create_meses_table', 4),
(42, '2022_12_17_232135_create_presupuestos_table', 4),
(47, '2022_12_27_152055_create_helisa_table', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuestos`
--

CREATE TABLE `presupuestos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ano_id` bigint(20) UNSIGNED NOT NULL,
  `mes_id` bigint(20) UNSIGNED NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `presupuestos`
--

INSERT INTO `presupuestos` (`id`, `ano_id`, `mes_id`, `valor`, `id_user`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(2, 1, 2, '2000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(3, 1, 3, '3000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(4, 1, 4, '4000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(5, 1, 5, '5000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(6, 1, 6, '6000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(7, 1, 7, '7000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(8, 1, 8, '8000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(9, 1, 9, '9000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(10, 1, 10, '10000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(11, 1, 11, '11000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24'),
(12, 1, 12, '12000.00', 9, '2022-12-29 01:45:24', '2022-12-29 01:45:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_user`
--

CREATE TABLE `roles_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles_user`
--

INSERT INTO `roles_user` (`id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, NULL),
(2, 'comercial', NULL, NULL),
(3, 'contable', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` bigint(20) UNSIGNED NOT NULL DEFAULT 2,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'public/photos/avatar.jpg',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `telefono`, `email_verified_at`, `password`, `rol`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Neffer', 'n3f73r@gmail.com', '3134085483', NULL, '$2y$10$QzG7L9AIA.34jN60V0kSqedCWFlc5HtCzCxTGM3.wxJOSqr4cwqFm', 1, 'public/photos/avatar.jpg', NULL, '2022-12-13 01:47:18', '2022-12-28 01:29:12'),
(8, 'ALEJANDRO RODRIGUEZ', 'alejandro.rodriguez@bullmarketing.com.co', '3174004506', NULL, '$2y$10$B5U7I5MCHa3hOQA1UDQdwOh6X3hKrFIu3rdE5ZvjYW8f0rezOnVU2', 2, 'public/photos/avatar.jpg', NULL, '2022-12-28 01:58:07', '2022-12-28 01:58:07'),
(9, 'LADY ORTIZ', 'lady.ortiz@bullmarketing.com.co', '54321', NULL, '$2y$10$5KJlhJHsS3x5akwkiNDqKeT9fdy0NE3kaOkl7YShOzFroftFr0zRm', 2, 'public/photos/avatar.jpg', NULL, '2022-12-28 02:01:21', '2022-12-28 02:01:21'),
(10, 'JONY ARIZA', 'j.ariza@bullmarketing.com.co', '56789', NULL, '$2y$10$WIs3wKvuPxQRKmhLqIsP9.PvNXJVSIOXgXI1Efl69vlQSQrAJbt4S', 2, 'public/photos/avatar.jpg', NULL, '2022-12-28 02:03:31', '2022-12-28 02:03:31'),
(11, 'ALEXANDRA NIÑO', 'alexandra.nino@bullmarketing.com.co', '98765', NULL, '$2y$10$3a2VLsUMLTTT99zqSaXum.Dif3Rv8ugoO.zp9gIRryxl9RcVzuAI2', 2, 'public/photos/avatar.jpg', NULL, '2022-12-28 02:04:23', '2022-12-28 02:04:23'),
(12, 'PAULA GARNICA', 'paula.garnica@bullmarketing.com.co', '246810', NULL, '$2y$10$D35WNAdQjHHmHmBi.QrlH.SVv9KyCrDt.onlTeisGCEbCztAkdu/q', 2, 'public/photos/avatar.jpg', NULL, '2022-12-28 02:06:07', '2022-12-28 02:06:07'),
(13, 'LILIANA VANEGAS', 'liliana.vanegas@bullmarketing.com.co', '3691215', NULL, '$2y$10$wyBcs3wZm8CIJSgiq.8k/edgVzQpqoPw/5PjF/x/UFGHo47Q/sZXm', 2, 'public/photos/avatar.jpg', NULL, '2022-12-28 02:07:12', '2022-12-28 02:07:12'),
(14, 'Paola Jimenez', 'paola.jimenez@bullmarketing.com.co', '481216', NULL, '$2y$10$ZDSuVc05B/xnBgvrktVT6.s.g/gCrfe3W9BKdQclwQO5QNhceEpIy', 3, 'public/photos/avatar.jpg', NULL, '2022-12-28 03:05:11', '2022-12-28 03:05:11');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anos`
--
ALTER TABLE `anos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `anos_description_unique` (`description`);

--
-- Indices de la tabla `base_comerciales`
--
ALTER TABLE `base_comerciales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `base_comerciales_id_estado_foreign` (`id_estado`),
  ADD KEY `base_comerciales_id_user_foreign` (`id_user`);

--
-- Indices de la tabla `estados_cuenta`
--
ALTER TABLE `estados_cuenta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `helisa`
--
ALTER TABLE `helisa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `helisa_comercial_foreign` (`comercial`);

--
-- Indices de la tabla `meses`
--
ALTER TABLE `meses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meses_ano_id_foreign` (`ano_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `presupuestos_ano_id_foreign` (`ano_id`),
  ADD KEY `presupuestos_mes_id_foreign` (`mes_id`),
  ADD KEY `presupuestos_id_user_foreign` (`id_user`);

--
-- Indices de la tabla `roles_user`
--
ALTER TABLE `roles_user`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_rol_foreign` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anos`
--
ALTER TABLE `anos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `base_comerciales`
--
ALTER TABLE `base_comerciales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `estados_cuenta`
--
ALTER TABLE `estados_cuenta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `helisa`
--
ALTER TABLE `helisa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `meses`
--
ALTER TABLE `meses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `roles_user`
--
ALTER TABLE `roles_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `base_comerciales`
--
ALTER TABLE `base_comerciales`
  ADD CONSTRAINT `base_comerciales_id_estado_foreign` FOREIGN KEY (`id_estado`) REFERENCES `estados_cuenta` (`id`),
  ADD CONSTRAINT `base_comerciales_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `helisa`
--
ALTER TABLE `helisa`
  ADD CONSTRAINT `helisa_comercial_foreign` FOREIGN KEY (`comercial`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `meses`
--
ALTER TABLE `meses`
  ADD CONSTRAINT `meses_ano_id_foreign` FOREIGN KEY (`ano_id`) REFERENCES `anos` (`id`);

--
-- Filtros para la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  ADD CONSTRAINT `presupuestos_ano_id_foreign` FOREIGN KEY (`ano_id`) REFERENCES `anos` (`id`),
  ADD CONSTRAINT `presupuestos_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `presupuestos_mes_id_foreign` FOREIGN KEY (`mes_id`) REFERENCES `meses` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_rol_foreign` FOREIGN KEY (`rol`) REFERENCES `roles_user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

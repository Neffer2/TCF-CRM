-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-09-2023 a las 17:31:03
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

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
(1, '2023', '2023-02-21 00:56:08', '2023-02-21 00:56:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistentes`
--

CREATE TABLE `asistentes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asistente_id` bigint(20) UNSIGNED NOT NULL,
  `comercial_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `base_comerciales`
--

CREATE TABLE `base_comerciales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `nom_cliente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_proyecto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cod_cc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_original` decimal(15,2) DEFAULT 0.00,
  `cotizacion_file_actualizacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `porcentaje` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_proyecto` decimal(15,2) DEFAULT 0.00,
  `com_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `com_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `com_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_estado` bigint(20) UNSIGNED NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `dura_mes` date DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `id_asistente` bigint(20) UNSIGNED DEFAULT NULL,
  `id_cuenta` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `id_gestion` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pbx` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_gestion_comercial`
--

CREATE TABLE `estados_gestion_comercial` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_presupuesto`
--

CREATE TABLE `estados_presupuesto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estructura de tabla para la tabla `gestion_comercial`
--

CREATE TABLE `gestion_comercial` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_contacto` bigint(20) UNSIGNED NOT NULL,
  `tipo_contacto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc_contacto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presto_cot` decimal(15,2) NOT NULL DEFAULT 0.00,
  `participaciones` decimal(8,2) NOT NULL DEFAULT 1.00,
  `porcentaje` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `porcentaje_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `porcentaje_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `porcentaje_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comercial_2` bigint(20) UNSIGNED DEFAULT NULL,
  `comercial_3` bigint(20) UNSIGNED DEFAULT NULL,
  `comercial_4` bigint(20) UNSIGNED DEFAULT NULL,
  `nom_proyecto_cot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_estimada_cot` date DEFAULT NULL,
  `cotizacion_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presto_prop` decimal(15,2) NOT NULL DEFAULT 0.00,
  `nom_proyecto_prop` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_estimada_prop` date DEFAULT NULL,
  `propuesta_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_estado` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_tercero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `centro` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_centro_costo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debito` decimal(12,2) DEFAULT NULL,
  `credito` decimal(12,2) DEFAULT NULL,
  `porcentaje` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '100',
  `comercial` bigint(20) UNSIGNED NOT NULL,
  `id_cuenta` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `participacion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_factura` decimal(12,2) NOT NULL,
  `mes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `año` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comision` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items_presupuesto`
--

CREATE TABLE `items_presupuesto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `presupuesto_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cod` int(11) NOT NULL,
  `evento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `cantidad` int(11) NOT NULL,
  `dia` int(11) NOT NULL,
  `otros` int(11) NOT NULL,
  `descripcion` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `v_unitario` decimal(15,2) NOT NULL DEFAULT 0.00,
  `v_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `proveedor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `margen_utilidad` double(15,10) NOT NULL DEFAULT 0.0000000000,
  `mes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dias` int(11) NOT NULL,
  `ciudad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `v_unitario_cot` decimal(15,2) NOT NULL DEFAULT 0.00,
  `v_total_cot` decimal(15,2) NOT NULL DEFAULT 0.00,
  `rentabilidad` decimal(15,2) NOT NULL DEFAULT 0.00,
  `actualizado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2022_11_29_214201_create_roles_user_table', 1),
(3, '2014_10_12_000000_create_users_table', 2),
(4, '2014_10_12_100000_create_password_resets_table', 3),
(5, '2019_08_19_000000_create_failed_jobs_table', 3),
(6, '2022_11_28_141640_create_estados_cuenta_table', 4),
(7, '2023_03_24_151700_create_cuentas_table', 5),
(8, '2023_03_27_202002_create_estados_gestion_comercial', 5),
(9, '2022_12_16_234442_create_anos_table', 6),
(10, '2023_04_03_214843_create_contactos_table', 7),
(11, '2023_05_08_154437_create_estados_presupuesto_table', 7),
(12, '2023_05_16_144919_create_tarifario_table', 7),
(13, '2023_03_22_143801_create_asistentes_table', 8),
(14, '2023_03_28_143631_create_gestion_comercial_table', 9),
(15, '2022_11_29_214200_create_base_comerciales_table', 10),
(16, '2022_12_27_152055_create_helisa_table', 11),
(17, '2022_12_17_232130_create_meses_table', 12),
(18, '2022_12_17_232135_create_presupuestos_table', 12),
(19, '2023_05_09_154216_create_presupuesto_proyecto_table', 13),
(20, '2023_05_09_160041_items_presupuesto_table', 14);

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
  `valor` decimal(15,2) NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto_proyecto`
--

CREATE TABLE `presupuesto_proyecto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_gestion` bigint(20) UNSIGNED NOT NULL,
  `estado_id` bigint(20) UNSIGNED NOT NULL DEFAULT 3,
  `margen_general` double(15,10) NOT NULL DEFAULT 0.0000000000,
  `venta_proy` decimal(15,2) NOT NULL DEFAULT 0.00,
  `costos_proy` decimal(15,2) NOT NULL DEFAULT 0.00,
  `margen_proy` decimal(15,2) NOT NULL DEFAULT 0.00,
  `margen_bruto` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cod_cot` int(11) NOT NULL,
  `cod_cc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_cc` date DEFAULT NULL,
  `imprevistos` decimal(15,2) NOT NULL DEFAULT 0.00,
  `administracion` decimal(15,2) NOT NULL DEFAULT 0.00,
  `fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tiempo_factura` int(11) NOT NULL DEFAULT 30,
  `notas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `justificacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `productor` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifario`
--

CREATE TABLE `tarifario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caso` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caracteristicas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `v_unidad` decimal(15,2) NOT NULL DEFAULT 0.00,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anos`
--
ALTER TABLE `anos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `anos_description_unique` (`description`);

--
-- Indices de la tabla `asistentes`
--
ALTER TABLE `asistentes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asistentes_asistente_id_foreign` (`asistente_id`),
  ADD KEY `asistentes_comercial_id_foreign` (`comercial_id`);

--
-- Indices de la tabla `base_comerciales`
--
ALTER TABLE `base_comerciales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `base_comerciales_id_estado_foreign` (`id_estado`),
  ADD KEY `base_comerciales_id_user_foreign` (`id_user`),
  ADD KEY `base_comerciales_id_asistente_foreign` (`id_asistente`),
  ADD KEY `base_comerciales_id_cuenta_foreign` (`id_cuenta`),
  ADD KEY `base_comerciales_id_gestion_foreign` (`id_gestion`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contactos_id_user_foreign` (`id_user`);

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados_cuenta`
--
ALTER TABLE `estados_cuenta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados_gestion_comercial`
--
ALTER TABLE `estados_gestion_comercial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados_presupuesto`
--
ALTER TABLE `estados_presupuesto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `gestion_comercial`
--
ALTER TABLE `gestion_comercial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gestion_comercial_id_contacto_foreign` (`id_contacto`),
  ADD KEY `gestion_comercial_comercial_2_foreign` (`comercial_2`),
  ADD KEY `gestion_comercial_comercial_3_foreign` (`comercial_3`),
  ADD KEY `gestion_comercial_comercial_4_foreign` (`comercial_4`),
  ADD KEY `gestion_comercial_id_estado_foreign` (`id_estado`),
  ADD KEY `gestion_comercial_id_user_foreign` (`id_user`);

--
-- Indices de la tabla `helisa`
--
ALTER TABLE `helisa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `helisa_comercial_foreign` (`comercial`),
  ADD KEY `helisa_id_cuenta_foreign` (`id_cuenta`);

--
-- Indices de la tabla `items_presupuesto`
--
ALTER TABLE `items_presupuesto`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `presupuesto_proyecto`
--
ALTER TABLE `presupuesto_proyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `presupuesto_proyecto_id_gestion_foreign` (`id_gestion`),
  ADD KEY `presupuesto_proyecto_estado_id_foreign` (`estado_id`),
  ADD KEY `presupuesto_proyecto_productor_foreign` (`productor`);

--
-- Indices de la tabla `roles_user`
--
ALTER TABLE `roles_user`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarifario`
--
ALTER TABLE `tarifario`
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
-- AUTO_INCREMENT de la tabla `asistentes`
--
ALTER TABLE `asistentes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `base_comerciales`
--
ALTER TABLE `base_comerciales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados_cuenta`
--
ALTER TABLE `estados_cuenta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados_gestion_comercial`
--
ALTER TABLE `estados_gestion_comercial`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados_presupuesto`
--
ALTER TABLE `estados_presupuesto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gestion_comercial`
--
ALTER TABLE `gestion_comercial`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `helisa`
--
ALTER TABLE `helisa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `items_presupuesto`
--
ALTER TABLE `items_presupuesto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `meses`
--
ALTER TABLE `meses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuesto_proyecto`
--
ALTER TABLE `presupuesto_proyecto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles_user`
--
ALTER TABLE `roles_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarifario`
--
ALTER TABLE `tarifario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistentes`
--
ALTER TABLE `asistentes`
  ADD CONSTRAINT `asistentes_asistente_id_foreign` FOREIGN KEY (`asistente_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `asistentes_comercial_id_foreign` FOREIGN KEY (`comercial_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `base_comerciales`
--
ALTER TABLE `base_comerciales`
  ADD CONSTRAINT `base_comerciales_id_asistente_foreign` FOREIGN KEY (`id_asistente`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `base_comerciales_id_cuenta_foreign` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id`),
  ADD CONSTRAINT `base_comerciales_id_estado_foreign` FOREIGN KEY (`id_estado`) REFERENCES `estados_cuenta` (`id`),
  ADD CONSTRAINT `base_comerciales_id_gestion_foreign` FOREIGN KEY (`id_gestion`) REFERENCES `gestion_comercial` (`id`),
  ADD CONSTRAINT `base_comerciales_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `contactos_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `gestion_comercial`
--
ALTER TABLE `gestion_comercial`
  ADD CONSTRAINT `gestion_comercial_comercial_2_foreign` FOREIGN KEY (`comercial_2`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `gestion_comercial_comercial_3_foreign` FOREIGN KEY (`comercial_3`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `gestion_comercial_comercial_4_foreign` FOREIGN KEY (`comercial_4`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `gestion_comercial_id_contacto_foreign` FOREIGN KEY (`id_contacto`) REFERENCES `contactos` (`id`),
  ADD CONSTRAINT `gestion_comercial_id_estado_foreign` FOREIGN KEY (`id_estado`) REFERENCES `estados_gestion_comercial` (`id`),
  ADD CONSTRAINT `gestion_comercial_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `helisa`
--
ALTER TABLE `helisa`
  ADD CONSTRAINT `helisa_comercial_foreign` FOREIGN KEY (`comercial`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `helisa_id_cuenta_foreign` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id`);

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
-- Filtros para la tabla `presupuesto_proyecto`
--
ALTER TABLE `presupuesto_proyecto`
  ADD CONSTRAINT `presupuesto_proyecto_estado_id_foreign` FOREIGN KEY (`estado_id`) REFERENCES `estados_presupuesto` (`id`),
  ADD CONSTRAINT `presupuesto_proyecto_id_gestion_foreign` FOREIGN KEY (`id_gestion`) REFERENCES `gestion_comercial` (`id`),
  ADD CONSTRAINT `presupuesto_proyecto_productor_foreign` FOREIGN KEY (`productor`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_rol_foreign` FOREIGN KEY (`rol`) REFERENCES `roles_user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

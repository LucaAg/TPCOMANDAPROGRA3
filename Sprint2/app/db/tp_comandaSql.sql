-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-11-2022 a las 02:06:53
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
-- Base de datos: `tp_comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `id` int(11) NOT NULL,
  `nombreArticulo` varchar(60) COLLATE utf8mb4_spanish_ci NOT NULL,
  `precio` int(11) NOT NULL,
  `cargoEmpleado` varchar(40) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comandas`
--

CREATE TABLE `comandas` (
  `codigo` varchar(5) COLLATE utf8mb4_spanish_ci NOT NULL,
  `codigoMesa` int(5) NOT NULL,
  `estadoComanda` varchar(21) COLLATE utf8mb4_spanish_ci NOT NULL,
  `nombreCliente` varchar(60) COLLATE utf8mb4_spanish_ci NOT NULL,
  `imagenComanda` varchar(90) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `precioFinal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `tipoEmpleado` varchar(60) COLLATE utf8mb4_spanish_ci NOT NULL,
  `clave` varchar(90) COLLATE utf8mb4_spanish_ci NOT NULL,
  `nombreCompleto` varchar(90) COLLATE utf8mb4_spanish_ci NOT NULL,
  `esSocio` varchar(3) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaBaja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `tipoEmpleado`, `clave`, `nombreCompleto`, `esSocio`, `fechaAlta`, `fechaBaja`) VALUES
(100, 'Socio', '$2y$10$Rhz4h3yshDeWhEQRhaayxOHMO36QOeRKCVCYxZrieN31AtRisItWW', 'Luca Agnoli', 'Si', '2022-11-22', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encargos`
--

CREATE TABLE `encargos` (
  `id` int(11) NOT NULL,
  `codigoComanda` varchar(5) COLLATE utf8mb4_spanish_ci NOT NULL,
  `idArticulo` int(11) NOT NULL,
  `estadoEncargo` varchar(21) COLLATE utf8mb4_spanish_ci NOT NULL,
  `idEmpleado` int(11) DEFAULT NULL,
  `tiempoEstimado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `codigoMesa` int(5) NOT NULL,
  `idMozo` int(11) DEFAULT NULL,
  `estadoMesa` varchar(21) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`codigoMesa`, `idMozo`, `estadoMesa`) VALUES
(10000, NULL, 'Cerrado'),
(10001, NULL, 'Cerrado'),
(10002, NULL, 'Cerrado'),
(10004, NULL, 'Cerrado'),
(10005, NULL, 'Cerrado'),
(10006, NULL, 'Cerrado');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `codigoMesa` (`codigoMesa`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encargos`
--
ALTER TABLE `encargos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `codigoComanda` (`codigoComanda`),
  ADD KEY `idArticulo` (`idArticulo`),
  ADD KEY `idEmpleado` (`idEmpleado`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`codigoMesa`),
  ADD KEY `idMozo` (`idMozo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `encargos`
--
ALTER TABLE `encargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD CONSTRAINT `comandas_ibfk_1` FOREIGN KEY (`codigoMesa`) REFERENCES `mesas` (`codigoMesa`);

--
-- Filtros para la tabla `encargos`
--
ALTER TABLE `encargos`
  ADD CONSTRAINT `encargos_ibfk_1` FOREIGN KEY (`codigoComanda`) REFERENCES `comandas` (`codigo`),
  ADD CONSTRAINT `encargos_ibfk_2` FOREIGN KEY (`idArticulo`) REFERENCES `articulos` (`id`),
  ADD CONSTRAINT `encargos_ibfk_3` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados` (`id`);

--
-- Filtros para la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD CONSTRAINT `mesas_ibfk_1` FOREIGN KEY (`idMozo`) REFERENCES `empleados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

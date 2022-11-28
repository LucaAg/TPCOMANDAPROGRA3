-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2022 a las 20:23:17
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

--
-- Volcado de datos para la tabla `comandas`
--

INSERT INTO `comandas` (`codigo`, `codigoMesa`, `estadoComanda`, `nombreCliente`, `imagenComanda`, `precioFinal`) VALUES
('dy5CI', 10001, 'En preparacion', 'Ariadna', '../Media/Comandas//dy5CI-10000-Ariadna.jfif', 2300),
('fmqgn', 10004, 'Finalizada', 'Jose lopez', '../Media/Comandas//fmqgn-10004-Jose lopez.jfif', 1700),
('KLrCB', 10005, 'En preparacion', 'Blanca Ortigoza', '../Media/Comandas//KLrCB-10005-Blanca Ortigoza.jfif', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `codigoMesa` (`codigoMesa`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD CONSTRAINT `comandas_ibfk_1` FOREIGN KEY (`codigoMesa`) REFERENCES `mesas` (`codigoMesa`),
  ADD CONSTRAINT `comandas_ibfk_2` FOREIGN KEY (`codigoMesa`) REFERENCES `mesas` (`codigoMesa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

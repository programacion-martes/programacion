-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-05-2026 a las 01:30:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_ventas`
--
CREATE DATABASE IF NOT EXISTS `sistema_ventas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sistema_ventas`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `id` int(11) NOT NULL,
  `nombre_categoria` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_productos`
--

INSERT INTO `categorias_productos` (`id`, `nombre_categoria`) VALUES
(1, 'Monitores'),
(2, 'Teclados'),
(3, 'Altavoces'),
(4, 'Mouse'),
(5, 'micrófonos'),
(6, 'Audífonos '),
(7, 'Tarjetas gráficas'),
(8, 'Case PC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `documento` char(1) NOT NULL,
  `numerodocumento` varchar(80) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `telefono` varchar(45) NOT NULL,
  `direccion` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `documento`, `numerodocumento`, `nombre`, `apellido`, `telefono`, `direccion`) VALUES
(1, 'V', '31279765', 'Francisco', 'Diaz', '04125052658', 'italia'),
(2, 'V', '12211634', 'Roque', 'Feller', '04126325698', 'La limpia'),
(3, 'V', '13628961', 'Carlos ', 'Sabaneta', '04126369854', 'Sector sabaneta'),
(4, 'V', '30256987', 'Wisin', 'Yandel', '04126365214', 'Barquisimeto'),
(5, 'V', '31883801', 'CarlosÑ', 'Villalobos   ', '041210676299', 'asdasdsa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_ventas`
--

CREATE TABLE `detalles_ventas` (
  `id` int(11) NOT NULL,
  `ventaid` int(11) NOT NULL,
  `precioid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_ventas`
--

INSERT INTO `detalles_ventas` (`id`, `ventaid`, `precioid`) VALUES
(1, 1, 27),
(2, 2, 30),
(3, 3, 22),
(4, 3, 22),
(5, 3, 22),
(6, 3, 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iva`
--

CREATE TABLE `iva` (
  `id` int(11) NOT NULL,
  `porcentaje` decimal(10,2) NOT NULL DEFAULT 16.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `iva`
--

INSERT INTO `iva` (`id`, `porcentaje`) VALUES
(1, 30.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios`
--

CREATE TABLE `precios` (
  `id` int(11) NOT NULL,
  `productoid` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios`
--

INSERT INTO `precios` (`id`, `productoid`, `precio`) VALUES
(18, 1, 140.00),
(20, 3, 100.00),
(21, 4, 150.00),
(22, 5, 33.00),
(23, 6, 30.00),
(24, 7, 28.00),
(25, 8, 50.00),
(26, 9, 30.00),
(27, 10, 30.00),
(28, 11, 30.00),
(29, 12, 350.00),
(30, 13, 115.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `categoria_productoid` int(11) NOT NULL,
  `nombre_producto` text NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_productoid`, `nombre_producto`, `stock`) VALUES
(1, 1, 'Monitor curvo 32 pulgadas Samsung', 10),
(3, 2, 'Teclado Gaming Razer Cynosa Chroma Rgb Back Lighting', 5),
(4, 1, 'Monitor Gamer Msi 24 144hz Pro Mp243l E14 Full Hd Ips Hdmi', 10),
(5, 3, 'Altavoces Gaming Onikuma L6 Rgb Bluetooth Pc Escritorio', 1),
(6, 4, 'Mouse Logitech', 10),
(7, 5, 'Microfono Gaming Onikuma M830 Condensador Usb Rgb', 20),
(8, 6, 'Audifonos Fantech Tamago Ii Whg04 Bluetooth + Usb', 15),
(9, 6, 'Audifonos Con Microfono Gaming Xtrikeme Gh-513w Bt + Usb', 30),
(10, 6, 'Audifonos Gaming Con Rgb Supra-aurales, Gd900 Pro', 9),
(11, 2, 'Teclado Gaming Mecanico Xtrike Me 61 Keys 60% Full Rgb', 15),
(12, 7, 'Tarjeta Grafica Msi Gaming Geforce Rtx 3050 6gb Gddr6', 50),
(13, 8, 'Case Msi Gaming Mag Force M100a 4xfan Rgb 120mm M-atx', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(40) NOT NULL,
  `contraseña` varchar(80) NOT NULL,
  `rol` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contraseña`, `rol`) VALUES
(1, 'ADMIN', '$2y$10$ka7vOeYOeVP72R7qRI988ugsLmpJeabeu44KOGFW42DurLVL/BV56', 1),
(2, 'CAJAPRINCIPAL', '$2y$10$N2X5iz4Svm/oQ4kDYMjw..MdwJll9FFfdjXjaBUy6s9hzW.walWce', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `clienteid` int(11) NOT NULL,
  `iva_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `clienteid`, `iva_id`) VALUES
(1, '2026-05-05', 1, NULL),
(2, '2026-05-05', 4, NULL),
(3, '2026-05-06', 5, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `precioid` (`precioid`),
  ADD KEY `ventaid` (`ventaid`);

--
-- Indices de la tabla `iva`
--
ALTER TABLE `iva`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `precios`
--
ALTER TABLE `precios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productoid` (`productoid`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_productoid` (`categoria_productoid`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clienteid` (`clienteid`),
  ADD KEY `iva_id` (`iva_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `iva`
--
ALTER TABLE `iva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `precios`
--
ALTER TABLE `precios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  ADD CONSTRAINT `detalles_ventas_ibfk_1` FOREIGN KEY (`precioid`) REFERENCES `precios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalles_ventas_ibfk_2` FOREIGN KEY (`ventaid`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`iva_id`) REFERENCES `iva` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

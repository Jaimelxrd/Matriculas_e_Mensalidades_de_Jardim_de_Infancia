-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05-Jun-2026 às 20:45
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `kindergarten_db`
--
CREATE DATABASE kindergarten_db;
USE kindergarten_db;
-- --------------------------------------------------------

--
-- Estrutura da tabela `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('admin','super_admin','secretaria') DEFAULT 'admin',
  `foto_path` varchar(255) DEFAULT NULL,
  `ultimo_acesso` timestamp NULL DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `administradores`
--

INSERT INTO `administradores` (`id`, `nome`, `email`, `usuario`, `senha`, `nivel`, `foto_path`, `ultimo_acesso`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Administrador Master', 'admin@kindergarten.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NULL, NULL, 'ativo', '2026-06-05 10:38:08', '2026-06-05 10:38:08'),
(4, 'Administrador Sistema', 'adminlxrd@kindergarten.com', 'lxrd', '$2y$10$WE4hm2zP8NiqN14cOj5KrOB.tjuzbKxxUv50ZA37Ym/G03J8e/P6q', 'super_admin', NULL, '2026-06-05 16:58:57', 'ativo', '2026-06-05 10:53:11', '2026-06-05 16:58:57');

-- --------------------------------------------------------

--
-- Estrutura da tabela `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `acao` varchar(255) DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `acao`, `detalhes`, `ip_address`, `data_hora`) VALUES
(1, NULL, 'login_falhou', 'Tentativa de login com usuário: admin@admin.com', '::1', '2026-06-05 10:44:28'),
(2, NULL, 'login_falhou', 'Tentativa de login com usuário: admin', '::1', '2026-06-05 10:45:03'),
(3, NULL, 'login_falhou', 'Tentativa de login com usuário: admin@admin.com', '::1', '2026-06-05 10:45:11'),
(4, NULL, 'login_falhou', 'Tentativa de login com usuário: admin@admin.com', '::1', '2026-06-05 10:45:12'),
(5, NULL, 'login_falhou', 'Tentativa de login com usuário: admin', '::1', '2026-06-05 10:47:18'),
(6, 4, 'login', 'Administrador Administrador Sistema fez login', '::1', '2026-06-05 10:53:42'),
(7, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 3 - Tipo: matricula', '::1', '2026-06-05 11:03:42'),
(8, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 1 - Tipo: matricula', '::1', '2026-06-05 11:03:45'),
(9, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 25 - Tipo: mensalidade', '::1', '2026-06-05 11:03:47'),
(10, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 1 - Tipo: mensalidade', '::1', '2026-06-05 11:03:49'),
(11, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 13 - Tipo: mensalidade', '::1', '2026-06-05 11:03:49'),
(12, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 2 - Tipo: mensalidade', '::1', '2026-06-05 11:03:50'),
(13, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 14 - Tipo: mensalidade', '::1', '2026-06-05 11:03:50'),
(14, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 26 - Tipo: mensalidade', '::1', '2026-06-05 11:03:51'),
(15, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 3 - Tipo: mensalidade', '::1', '2026-06-05 11:03:51'),
(16, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 15 - Tipo: mensalidade', '::1', '2026-06-05 11:03:51'),
(17, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 27 - Tipo: mensalidade', '::1', '2026-06-05 11:03:51'),
(18, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 4 - Tipo: mensalidade', '::1', '2026-06-05 11:03:52'),
(19, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 28 - Tipo: mensalidade', '::1', '2026-06-05 11:03:52'),
(20, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 16 - Tipo: mensalidade', '::1', '2026-06-05 11:03:52'),
(21, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 5 - Tipo: mensalidade', '::1', '2026-06-05 11:03:52'),
(22, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 17 - Tipo: mensalidade', '::1', '2026-06-05 11:03:53'),
(23, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 29 - Tipo: mensalidade', '::1', '2026-06-05 11:03:53'),
(24, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 6 - Tipo: mensalidade', '::1', '2026-06-05 11:03:53'),
(25, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 30 - Tipo: mensalidade', '::1', '2026-06-05 11:03:54'),
(26, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 7 - Tipo: mensalidade', '::1', '2026-06-05 11:03:54'),
(27, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 19 - Tipo: mensalidade', '::1', '2026-06-05 11:03:54'),
(28, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 31 - Tipo: mensalidade', '::1', '2026-06-05 11:03:54'),
(29, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 8 - Tipo: mensalidade', '::1', '2026-06-05 11:03:54'),
(30, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 32 - Tipo: mensalidade', '::1', '2026-06-05 11:03:55'),
(31, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 20 - Tipo: mensalidade', '::1', '2026-06-05 11:03:55'),
(32, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 9 - Tipo: mensalidade', '::1', '2026-06-05 11:03:55'),
(33, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 21 - Tipo: mensalidade', '::1', '2026-06-05 11:03:55'),
(34, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 33 - Tipo: mensalidade', '::1', '2026-06-05 11:03:56'),
(35, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 10 - Tipo: mensalidade', '::1', '2026-06-05 11:03:56'),
(36, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 22 - Tipo: mensalidade', '::1', '2026-06-05 11:03:56'),
(37, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 34 - Tipo: mensalidade', '::1', '2026-06-05 11:03:56'),
(38, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 11 - Tipo: mensalidade', '::1', '2026-06-05 11:03:57'),
(39, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 35 - Tipo: mensalidade', '::1', '2026-06-05 11:03:57'),
(40, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 23 - Tipo: mensalidade', '::1', '2026-06-05 11:03:57'),
(41, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 12 - Tipo: mensalidade', '::1', '2026-06-05 11:03:58'),
(42, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 24 - Tipo: mensalidade', '::1', '2026-06-05 11:03:58'),
(43, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 36 - Tipo: mensalidade', '::1', '2026-06-05 11:03:58'),
(44, 4, 'criar_admin', 'Criou novo administrador: secretaria', '::1', '2026-06-05 11:16:22'),
(45, 4, 'logout', 'Administrador Administrador Sistema fez logout', '::1', '2026-06-05 11:17:05'),
(46, NULL, 'login', 'Administrador secretaria fez login', '::1', '2026-06-05 11:17:13'),
(47, NULL, 'logout', 'Administrador secretaria fez logout', '::1', '2026-06-05 11:17:50'),
(48, 4, 'login', 'Administrador Administrador Sistema fez login', '::1', '2026-06-05 11:17:52'),
(49, 4, 'enviar_mensagem', 'Enviou mensagem para aluno ID: 1', '::1', '2026-06-05 11:30:13'),
(50, 4, 'criar_comunicado', 'Criou comunicado: Equacoes do segundo grau', '::1', '2026-06-05 11:36:18'),
(51, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 2', '::1', '2026-06-05 11:38:11'),
(52, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 1', '::1', '2026-06-05 11:38:30'),
(53, 4, 'criar_material', 'Publicou material: Equacoes do segundo grau', '::1', '2026-06-05 11:50:28'),
(54, 4, 'login', 'Administrador Administrador Sistema fez login', '::1', '2026-06-05 13:08:20'),
(55, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 1', '::1', '2026-06-05 13:21:21'),
(56, 4, 'logout', 'Administrador Administrador Sistema fez logout', '::1', '2026-06-05 13:21:41'),
(57, 4, 'login', 'Administrador Administrador Sistema fez login', '::1', '2026-06-05 14:18:29'),
(58, 4, 'enviar_mensagem', 'Enviou mensagem para aluno ID: 7', '::1', '2026-06-05 14:18:55'),
(59, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 72 - Tipo: mensalidade', '::1', '2026-06-05 14:19:35'),
(60, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 7 - Tipo: matricula', '::1', '2026-06-05 14:19:37'),
(61, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 6 - Tipo: matricula', '::1', '2026-06-05 14:19:37'),
(62, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 5 - Tipo: matricula', '::1', '2026-06-05 14:19:38'),
(63, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 4 - Tipo: matricula', '::1', '2026-06-05 14:19:38'),
(64, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 49 - Tipo: mensalidade', '::1', '2026-06-05 14:19:40'),
(65, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 73 - Tipo: mensalidade', '::1', '2026-06-05 14:19:40'),
(66, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 61 - Tipo: mensalidade', '::1', '2026-06-05 14:19:40'),
(67, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 37 - Tipo: mensalidade', '::1', '2026-06-05 14:19:41'),
(68, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 62 - Tipo: mensalidade', '::1', '2026-06-05 14:19:41'),
(69, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 74 - Tipo: mensalidade', '::1', '2026-06-05 14:19:42'),
(70, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 50 - Tipo: mensalidade', '::1', '2026-06-05 14:19:42'),
(71, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 38 - Tipo: mensalidade', '::1', '2026-06-05 14:19:42'),
(72, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 51 - Tipo: mensalidade', '::1', '2026-06-05 14:19:43'),
(73, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 63 - Tipo: mensalidade', '::1', '2026-06-05 14:19:43'),
(74, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 75 - Tipo: mensalidade', '::1', '2026-06-05 14:19:43'),
(75, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 39 - Tipo: mensalidade', '::1', '2026-06-05 14:19:44'),
(76, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 52 - Tipo: mensalidade', '::1', '2026-06-05 14:19:44'),
(77, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 76 - Tipo: mensalidade', '::1', '2026-06-05 14:19:45'),
(78, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 64 - Tipo: mensalidade', '::1', '2026-06-05 14:19:45'),
(79, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 40 - Tipo: mensalidade', '::1', '2026-06-05 14:19:45'),
(80, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 65 - Tipo: mensalidade', '::1', '2026-06-05 14:19:46'),
(81, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 77 - Tipo: mensalidade', '::1', '2026-06-05 14:19:46'),
(82, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 53 - Tipo: mensalidade', '::1', '2026-06-05 14:19:46'),
(83, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 41 - Tipo: mensalidade', '::1', '2026-06-05 14:19:47'),
(84, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 54 - Tipo: mensalidade', '::1', '2026-06-05 14:19:48'),
(85, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 66 - Tipo: mensalidade', '::1', '2026-06-05 14:19:48'),
(86, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 78 - Tipo: mensalidade', '::1', '2026-06-05 14:19:48'),
(87, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 42 - Tipo: mensalidade', '::1', '2026-06-05 14:19:49'),
(88, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 67 - Tipo: mensalidade', '::1', '2026-06-05 14:19:49'),
(89, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 79 - Tipo: mensalidade', '::1', '2026-06-05 14:19:50'),
(90, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 43 - Tipo: mensalidade', '::1', '2026-06-05 14:19:50'),
(91, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 55 - Tipo: mensalidade', '::1', '2026-06-05 14:19:51'),
(92, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 68 - Tipo: mensalidade', '::1', '2026-06-05 14:19:51'),
(93, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 80 - Tipo: mensalidade', '::1', '2026-06-05 14:19:52'),
(94, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 56 - Tipo: mensalidade', '::1', '2026-06-05 14:19:52'),
(95, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 44 - Tipo: mensalidade', '::1', '2026-06-05 14:19:52'),
(96, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 44 - Tipo: mensalidade', '::1', '2026-06-05 14:19:53'),
(97, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 57 - Tipo: mensalidade', '::1', '2026-06-05 14:19:53'),
(98, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 69 - Tipo: mensalidade', '::1', '2026-06-05 14:19:53'),
(99, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 81 - Tipo: mensalidade', '::1', '2026-06-05 14:19:54'),
(100, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 45 - Tipo: mensalidade', '::1', '2026-06-05 14:19:54'),
(101, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 82 - Tipo: mensalidade', '::1', '2026-06-05 14:19:55'),
(102, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 46 - Tipo: mensalidade', '::1', '2026-06-05 14:19:55'),
(103, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 58 - Tipo: mensalidade', '::1', '2026-06-05 14:19:56'),
(104, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 70 - Tipo: mensalidade', '::1', '2026-06-05 14:19:56'),
(105, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 71 - Tipo: mensalidade', '::1', '2026-06-05 14:19:57'),
(106, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 83 - Tipo: mensalidade', '::1', '2026-06-05 14:19:57'),
(107, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 59 - Tipo: mensalidade', '::1', '2026-06-05 14:19:57'),
(108, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 47 - Tipo: mensalidade', '::1', '2026-06-05 14:19:58'),
(109, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 60 - Tipo: mensalidade', '::1', '2026-06-05 14:19:58'),
(110, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 48 - Tipo: mensalidade', '::1', '2026-06-05 14:19:59'),
(111, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 84 - Tipo: mensalidade', '::1', '2026-06-05 14:19:59'),
(112, 4, 'excluir_admin', 'Excluiu administrador ID: 5', '::1', '2026-06-05 15:21:24'),
(113, 4, 'excluir_material', 'Excluiu material ID: 1', '::1', '2026-06-05 15:21:45'),
(114, 4, 'logout', 'Administrador Administrador Sistema fez logout', '::1', '2026-06-05 15:22:39'),
(115, 4, 'login', 'Administrador Administrador Sistema fez login', '::1', '2026-06-05 15:35:26'),
(116, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 8 - Tipo: matricula', '::1', '2026-06-05 15:35:50'),
(117, 4, 'criar_material', 'Publicou material: Equacoes do segundo grau', '::1', '2026-06-05 15:38:40'),
(118, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 85 - Tipo: mensalidade', '::1', '2026-06-05 15:39:22'),
(119, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 86 - Tipo: mensalidade', '::1', '2026-06-05 15:39:23'),
(120, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 87 - Tipo: mensalidade', '::1', '2026-06-05 15:39:24'),
(121, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 88 - Tipo: mensalidade', '::1', '2026-06-05 15:39:24'),
(122, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 89 - Tipo: mensalidade', '::1', '2026-06-05 15:39:25'),
(123, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 90 - Tipo: mensalidade', '::1', '2026-06-05 15:39:25'),
(124, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 91 - Tipo: mensalidade', '::1', '2026-06-05 15:39:26'),
(125, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 92 - Tipo: mensalidade', '::1', '2026-06-05 15:39:27'),
(126, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 93 - Tipo: mensalidade', '::1', '2026-06-05 15:39:27'),
(127, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 94 - Tipo: mensalidade', '::1', '2026-06-05 15:39:28'),
(128, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 95 - Tipo: mensalidade', '::1', '2026-06-05 15:39:28'),
(129, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 96 - Tipo: mensalidade', '::1', '2026-06-05 15:39:29'),
(130, 4, 'enviar_mensagem', 'Enviou mensagem para aluno ID: 8', '::1', '2026-06-05 15:40:41'),
(131, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 1', '::1', '2026-06-05 15:42:10'),
(132, 4, 'criar_comunicado', 'Criou comunicado: Bem-vindos!', '::1', '2026-06-05 15:42:27'),
(133, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 1', '::1', '2026-06-05 15:42:42'),
(134, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 1', '::1', '2026-06-05 15:42:51'),
(135, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 1', '::1', '2026-06-05 15:42:57'),
(136, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 1', '::1', '2026-06-05 15:43:14'),
(137, 4, 'excluir_comunicado', 'Excluiu comunicado ID: 1', '::1', '2026-06-05 15:48:01'),
(138, 4, 'criar_material', 'Publicou material: Teste', '::1', '2026-06-05 15:52:40'),
(139, 4, 'login', 'Administrador Administrador Sistema fez login', '::1', '2026-06-05 15:55:16'),
(140, 4, 'logout', 'Administrador Administrador Sistema fez logout', '::1', '2026-06-05 15:55:18'),
(141, 4, 'login', 'Administrador Administrador Sistema fez login', '::1', '2026-06-05 15:55:40'),
(142, 4, 'logout', 'Administrador Administrador Sistema fez logout', '::1', '2026-06-05 15:55:42'),
(143, 4, 'login', 'Administrador Administrador Sistema fez login', '::1', '2026-06-05 16:58:57'),
(144, 4, 'editar_aluno', 'Editou dados do aluno ID: 9 - Maria Jose', '::1', '2026-06-05 17:12:54'),
(145, 4, 'editar_aluno', 'Editou dados do aluno ID: 8 - Jaime Fernando Nhacumba', '::1', '2026-06-05 17:14:36'),
(146, 4, 'editar_aluno', 'Editou dados do aluno ID: 8 - Jaime Fernando Nhacumba', '::1', '2026-06-05 17:14:53'),
(147, 4, 'editar_aluno', 'Editou dados do aluno ID: 9 - Maria Jose', '::1', '2026-06-05 17:16:09'),
(148, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 9 - Tipo: matricula', '::1', '2026-06-05 17:26:48'),
(149, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 97 - Tipo: mensalidade', '::1', '2026-06-05 17:26:49'),
(150, 4, 'confirmar_pagamento', 'Confirmou pagamento ID: 98 - Tipo: mensalidade', '::1', '2026-06-05 17:26:51');

-- --------------------------------------------------------

--
-- Estrutura da tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `codigo_aluno` varchar(50) NOT NULL,
  `nome_completo` varchar(200) NOT NULL,
  `data_nascimento` date NOT NULL,
  `sexo` enum('Masculino','Feminino') NOT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `info_saude` text DEFAULT NULL,
  `periodo` enum('Meio período','Integral') DEFAULT 'Integral',
  `nome_responsavel` varchar(200) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `relacao` varchar(50) NOT NULL,
  `endereco` text NOT NULL,
  `certidao_path` varchar(255) DEFAULT NULL,
  `bi_responsavel_path` varchar(255) DEFAULT NULL,
  `foto_passe_path` varchar(255) DEFAULT NULL,
  `aceite_termos` tinyint(1) DEFAULT 0,
  `como_soube` varchar(100) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status_pagamento` enum('pendente','aprovado','recusado') DEFAULT 'pendente',
  `status_matricula` enum('pendente','confirmada','cancelada') DEFAULT 'pendente',
  `data_matricula` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `foto_path` varchar(255) DEFAULT NULL,
  `ultima_atividade` timestamp NULL DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `alunos`
--

INSERT INTO `alunos` (`id`, `codigo_aluno`, `nome_completo`, `data_nascimento`, `sexo`, `turma_id`, `info_saude`, `periodo`, `nome_responsavel`, `telefone`, `email`, `relacao`, `endereco`, `certidao_path`, `bi_responsavel_path`, `foto_passe_path`, `aceite_termos`, `como_soube`, `observacoes`, `status_pagamento`, `status_matricula`, `data_matricula`, `updated_at`, `foto_path`, `ultima_atividade`, `foto_perfil`) VALUES
(8, 'KG-2026-00001', 'Jaime Fernando Nhacumba', '0003-05-01', 'Masculino', 2, 'Sem alergias', 'Integral', '', '', '', 'Pai', '', 'documentos/6a22ec8caa4a6.png', 'documentos/6a22ec8cab64a.png', 'fotos/6a22ec8cac381.png', 1, 'redes sociais', '', 'aprovado', 'confirmada', '2026-06-05 15:34:36', '2026-06-05 17:14:48', NULL, NULL, 'assets/uploads/alunos/aluno_8_1780679688.png'),
(9, 'KG-2026-00002', 'Maria Jose', '2023-01-01', 'Feminino', 3, '', 'Meio período', '', '', '', 'Pai', '', NULL, NULL, NULL, 1, '', '', 'aprovado', 'confirmada', '2026-06-05 16:43:26', '2026-06-05 17:14:59', NULL, NULL, 'assets/uploads/alunos/aluno_9_1780678687.png');

--
-- Acionadores `alunos`
--
DELIMITER $$
CREATE TRIGGER `gerar_codigo_aluno` BEFORE INSERT ON `alunos` FOR EACH ROW BEGIN
    DECLARE ano_atual VARCHAR(4);
    DECLARE ultimo_numero INT;
    DECLARE novo_codigo VARCHAR(50);
    
    SET ano_atual = YEAR(CURDATE());
    
    SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(codigo_aluno, '-', -1) AS UNSIGNED)), 0) INTO ultimo_numero
    FROM alunos
    WHERE codigo_aluno LIKE CONCAT('KG-', ano_atual, '-%');
    
    SET novo_codigo = CONCAT('KG-', ano_atual, '-', LPAD(ultimo_numero + 1, 5, '0'));
    SET NEW.codigo_aluno = novo_codigo;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `avisos`
--

CREATE TABLE `avisos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `conteudo` text NOT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `data_envio` date NOT NULL,
  `data_expiracao` date DEFAULT NULL,
  `criado_por` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `descricao`, `created_at`) VALUES
(1, 'Literatura Infantil', 'Livros e contos para crianças', '2026-06-05 11:42:59'),
(2, 'Material Didático', 'Apostilas e exercícios', '2026-06-05 11:42:59'),
(3, 'Vídeos Educativos', 'Conteúdo em vídeo', '2026-06-05 11:42:59'),
(4, 'Artigos e Pesquisas', 'Artigos sobre educação infantil', '2026-06-05 11:42:59');

-- --------------------------------------------------------

--
-- Estrutura da tabela `comunicados`
--

CREATE TABLE `comunicados` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `mensagem` text NOT NULL,
  `tipo` enum('geral','urgente','evento','aviso') DEFAULT 'geral',
  `admin_id` int(11) NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_expiracao` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `comunicados`
--

INSERT INTO `comunicados` (`id`, `titulo`, `mensagem`, `tipo`, `admin_id`, `data_envio`, `data_expiracao`, `ativo`) VALUES
(3, 'Bem-vindos!', 'Seja bem-vindo(a)!\r\n\r\nEstamos felizes por ter você conosco. Explore os materiais disponíveis, participe das atividades e aproveite ao máximo sua experiência de aprendizagem.\r\n\r\nDesejamos muito sucesso em seus estudos!', 'geral', 4, '2026-06-05 15:42:27', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `comunicados_lidos`
--

CREATE TABLE `comunicados_lidos` (
  `id` int(11) NOT NULL,
  `comunicado_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `data_leitura` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `comunicados_lidos`
--

INSERT INTO `comunicados_lidos` (`id`, `comunicado_id`, `aluno_id`, `data_leitura`) VALUES
(3, 3, 8, '2026-06-05 15:48:19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `valor` text NOT NULL,
  `descricao` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `descricao`) VALUES
(1, 'valor_mensalidade', '2500.00', 'Valor padrão da mensalidade'),
(2, 'ano_letivo', '2026', 'Ano letivo atual'),
(3, 'escola_nome', 'Kindergarten', 'Nome da escola');

-- --------------------------------------------------------

--
-- Estrutura da tabela `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `tipo_documento` enum('certidao_nascimento','bi_responsavel','foto_tipo_passe') NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `caminho_arquivo` varchar(500) NOT NULL,
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_verificacao` enum('pendente','verificado','rejeitado') DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `encarregados`
--

CREATE TABLE `encarregados` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `relacao_crianca` varchar(20) NOT NULL,
  `endereco` text NOT NULL,
  `senha_pin` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `encarregados`
--

INSERT INTO `encarregados` (`id`, `aluno_id`, `nome_completo`, `telefone`, `email`, `relacao_crianca`, `endereco`, `senha_pin`, `created_at`) VALUES
(1, 9, 'Jaime Nhacumba', '+258847852208', 'jaime@gmail.com', 'Pai', 'Rua da Escola, nº 123 Magoanine \"c\", Maputo 1105 Moçambique', '$2y$10$GFJGnhMTVPhHvVq3JziC5eB6rSzObDLw7HAdhu2ikH4rLLBwRUWWi', '2026-06-05 17:12:54'),
(2, 8, 'Jaime Fernando', '+258847852208', 'jf847852208@gmail.com', 'Pai', 'Rua da Escola, nº 123 Magoanine \"c\", Maputo 1105 Moçambique', '$2y$10$1yPHA2VO/YLmpE5v/g37V.Bn8VHiepyW7.UfkxQ3M8QPHm924L3b.', '2026-06-05 17:14:36');

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `acao` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `materiais`
--

CREATE TABLE `materiais` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('livro','artigo','video','apostila','recurso') DEFAULT 'recurso',
  `categoria_id` int(11) DEFAULT NULL,
  `arquivo_path` varchar(255) DEFAULT NULL,
  `link_externo` varchar(500) DEFAULT NULL,
  `imagem_capa` varchar(255) DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `publicado_por` int(11) NOT NULL,
  `visualizacoes` int(11) DEFAULT 0,
  `downloads` int(11) DEFAULT 0,
  `destaque` tinyint(1) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `data_publicacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `materiais`
--

INSERT INTO `materiais` (`id`, `titulo`, `descricao`, `tipo`, `categoria_id`, `arquivo_path`, `link_externo`, `imagem_capa`, `autor`, `turma_id`, `publicado_por`, `visualizacoes`, `downloads`, `destaque`, `status`, `data_publicacao`) VALUES
(2, 'Equacoes do segundo grau', 'Material completo sobre equações do 2º grau, com conceitos, fórmulas, exemplos resolvidos e exercícios práticos', 'livro', 2, 'livros6a22ed8070b6c.pdf', '', NULL, 'Administrador', NULL, 4, 3, 0, 0, 'ativo', '2026-06-05 15:38:40'),
(3, 'Teste', '', 'recurso', 4, 'recursos6a22f0c882d15.png', '', NULL, '', NULL, 4, 1, 0, 0, 'ativo', '2026-06-05 15:52:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `materiais_acessos`
--

CREATE TABLE `materiais_acessos` (
  `id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `tipo_acesso` enum('visualizacao','download') DEFAULT 'visualizacao',
  `data_acesso` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `materiais_acessos`
--

INSERT INTO `materiais_acessos` (`id`, `material_id`, `aluno_id`, `tipo_acesso`, `data_acesso`) VALUES
(8, 2, 8, 'visualizacao', '2026-06-05 15:38:47'),
(9, 2, 8, 'visualizacao', '2026-06-05 15:48:57'),
(10, 3, 8, 'visualizacao', '2026-06-05 15:52:49'),
(11, 2, 8, 'visualizacao', '2026-06-05 15:52:55');

-- --------------------------------------------------------

--
-- Estrutura da tabela `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `data_matricula` date NOT NULL,
  `turma_desejada` varchar(50) DEFAULT NULL,
  `status_matricula` enum('ativa','cancelada','trancada','concluida') DEFAULT 'ativa',
  `data_aprovacao` date DEFAULT NULL,
  `aprovado_por` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `remetente_id` int(11) NOT NULL,
  `destinatario_id` int(11) NOT NULL,
  `remetente_tipo` enum('admin','aluno') NOT NULL,
  `assunto` varchar(200) NOT NULL,
  `mensagem` text NOT NULL,
  `anexo_path` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `lida_em` timestamp NULL DEFAULT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `remetente_id`, `destinatario_id`, `remetente_tipo`, `assunto`, `mensagem`, `anexo_path`, `lida`, `lida_em`, `data_envio`) VALUES
(1, 4, 1, 'admin', 'testando', 'testando funcionalidade', NULL, 0, NULL, '2026-06-05 11:30:13'),
(2, 1, 4, 'aluno', 'Resposta: testando', 'werr', NULL, 1, '2026-06-05 13:21:13', '2026-06-05 11:30:23'),
(3, 4, 7, 'admin', 'efwgr', 'edqfrtg', NULL, 0, NULL, '2026-06-05 14:18:55'),
(4, 7, 4, 'aluno', 'Resposta: efwgr', 'ee', NULL, 1, '2026-06-05 14:19:16', '2026-06-05 14:19:05'),
(5, 4, 8, 'admin', 'Boas Vindas', '🎓 Bem-vindo(a)!\r\n\r\nÉ com grande alegria que recebemos você em nossa plataforma. Aqui você encontrará materiais de estudo, atividades e recursos que irão apoiar sua aprendizagem e contribuir para o seu sucesso acadêmico.\r\n\r\nDesejamos uma excelente jornada de estudos, repleta de descobertas, crescimento e conquistas. Conte conosco para aprender, evoluir e alcançar seus objetivos!\r\n\r\nBons estudos! 📚✨', NULL, 0, NULL, '2026-06-05 15:40:41'),
(6, 8, 4, 'aluno', 'Resposta: Boas Vindas', 'obrigado', NULL, 1, '2026-06-05 15:41:26', '2026-06-05 15:41:16');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagens_contato`
--

CREATE TABLE `mensagens_contato` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `assunto` varchar(200) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `status` enum('nao_respondido','respondido') DEFAULT 'nao_respondido',
  `resposta` text DEFAULT NULL,
  `respondida_por` int(11) DEFAULT NULL,
  `resposta_enviada_em` timestamp NULL DEFAULT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensalidades`
--

CREATE TABLE `mensalidades` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `mes_referencia` varchar(20) NOT NULL,
  `ano` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_vencimento` date NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `status` enum('pendente','paga','atrasada') DEFAULT 'pendente',
  `comprovativo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mensalidades`
--

INSERT INTO `mensalidades` (`id`, `aluno_id`, `mes_referencia`, `ano`, `valor`, `data_vencimento`, `data_pagamento`, `status`, `comprovativo_path`, `created_at`) VALUES
(85, 8, 'Janeiro', 2026, 4500.00, '2026-01-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(86, 8, 'Fevereiro', 2026, 4500.00, '2026-02-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(87, 8, 'Março', 2026, 4500.00, '2026-03-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(88, 8, 'Abril', 2026, 4500.00, '2026-04-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(89, 8, 'Maio', 2026, 4500.00, '2026-05-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(90, 8, 'Junho', 2026, 4500.00, '2026-06-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(91, 8, 'Julho', 2026, 4500.00, '2026-07-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(92, 8, 'Agosto', 2026, 4500.00, '2026-08-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(93, 8, 'Setembro', 2026, 4500.00, '2026-09-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(94, 8, 'Outubro', 2026, 4500.00, '2026-10-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(95, 8, 'Novembro', 2026, 4500.00, '2026-11-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(96, 8, 'Dezembro', 2026, 4500.00, '2026-12-10', '2026-06-05', 'paga', NULL, '2026-06-05 15:34:36'),
(97, 9, 'Janeiro', 2026, 4500.00, '2026-01-10', '2026-06-05', 'paga', NULL, '2026-06-05 16:43:26'),
(98, 9, 'Fevereiro', 2026, 4500.00, '2026-02-10', '2026-06-05', 'paga', NULL, '2026-06-05 16:43:26'),
(99, 9, 'Março', 2026, 4500.00, '2026-03-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(100, 9, 'Abril', 2026, 4500.00, '2026-04-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(101, 9, 'Maio', 2026, 4500.00, '2026-05-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(102, 9, 'Junho', 2026, 4500.00, '2026-06-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(103, 9, 'Julho', 2026, 4500.00, '2026-07-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(104, 9, 'Agosto', 2026, 4500.00, '2026-08-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(105, 9, 'Setembro', 2026, 4500.00, '2026-09-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(106, 9, 'Outubro', 2026, 4500.00, '2026-10-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(107, 9, 'Novembro', 2026, 4500.00, '2026-11-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26'),
(108, 9, 'Dezembro', 2026, 4500.00, '2026-12-10', NULL, 'pendente', NULL, '2026-06-05 16:43:26');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `metodo_pagamento` enum('dinheiro','transferencia','cartao','mbway') NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `status_pagamento` enum('pendente','confirmado','cancelado') DEFAULT 'pendente',
  `descricao` varchar(200) DEFAULT NULL,
  `comprovante` varchar(255) DEFAULT NULL,
  `data_confirmacao` date DEFAULT NULL,
  `confirmado_por` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamentos_matricula`
--

CREATE TABLE `pagamentos_matricula` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `tipo` enum('matricula','mensalidade') DEFAULT 'matricula',
  `data_pagamento` date DEFAULT NULL,
  `comprovativo_path` varchar(255) DEFAULT NULL,
  `status` enum('pendente','confirmado','recusado') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pagamentos_matricula`
--

INSERT INTO `pagamentos_matricula` (`id`, `aluno_id`, `valor`, `tipo`, `data_pagamento`, `comprovativo_path`, `status`, `created_at`) VALUES
(8, 8, 1500.00, 'matricula', '2026-06-05', NULL, 'confirmado', '2026-06-05 15:34:36'),
(9, 9, 1500.00, 'matricula', '2026-06-05', NULL, 'confirmado', '2026-06-05 16:43:26');

-- --------------------------------------------------------

--
-- Estrutura da tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expira_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usado` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `faixa_etaria` varchar(50) DEFAULT NULL,
  `periodo` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `capacidade` int(11) DEFAULT 20
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `turmas`
--

INSERT INTO `turmas` (`id`, `nome`, `faixa_etaria`, `periodo`, `created_at`, `capacidade`) VALUES
(1, 'Mini Kids', '2 a 3 anos', 'Meio período', '2026-06-05 09:52:50', 20),
(2, 'Júnior', '4 a 5 anos', 'Integral', '2026-06-05 09:52:50', 20),
(3, 'Pré-Escolar', '5 a 6 anos', 'Integral', '2026-06-05 09:52:50', 20),
(4, 'Mini Kids', '2 a 3 anos', 'Meio período', '2026-06-05 11:00:11', 15),
(5, 'Júnior', '4 a 5 anos', 'Integral', '2026-06-05 11:00:11', 20),
(6, 'Pré-Escolar', '5 a 6 anos', 'Integral', '2026-06-05 11:00:11', 25);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices para tabela `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Índices para tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_aluno` (`codigo_aluno`),
  ADD KEY `turma_id` (`turma_id`),
  ADD KEY `idx_codigo_aluno` (`codigo_aluno`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status_matricula`),
  ADD KEY `idx_status_matricula` (`status_matricula`);

--
-- Índices para tabela `avisos`
--
ALTER TABLE `avisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `comunicados`
--
ALTER TABLE `comunicados`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `comunicados_lidos`
--
ALTER TABLE `comunicados_lidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_leitura` (`comunicado_id`,`aluno_id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices para tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices para tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices para tabela `encarregados`
--
ALTER TABLE `encarregados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aluno_id` (`aluno_id`);

--
-- Índices para tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `materiais`
--
ALTER TABLE `materiais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publicado_por` (`publicado_por`),
  ADD KEY `turma_id` (`turma_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices para tabela `materiais_acessos`
--
ALTER TABLE `materiais_acessos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices para tabela `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices para tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_remetente` (`remetente_id`),
  ADD KEY `idx_destinatario` (`destinatario_id`),
  ADD KEY `idx_lida` (`lida`);

--
-- Índices para tabela `mensagens_contato`
--
ALTER TABLE `mensagens_contato`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `mensalidades`
--
ALTER TABLE `mensalidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `idx_status` (`status`);

--
-- Índices para tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices para tabela `pagamentos_matricula`
--
ALTER TABLE `pagamentos_matricula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices para tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `avisos`
--
ALTER TABLE `avisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `comunicados`
--
ALTER TABLE `comunicados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `comunicados_lidos`
--
ALTER TABLE `comunicados_lidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `encarregados`
--
ALTER TABLE `encarregados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `materiais`
--
ALTER TABLE `materiais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `materiais_acessos`
--
ALTER TABLE `materiais_acessos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `mensagens_contato`
--
ALTER TABLE `mensagens_contato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `mensalidades`
--
ALTER TABLE `mensalidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos_matricula`
--
ALTER TABLE `pagamentos_matricula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `administradores` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Limitadores para a tabela `avisos`
--
ALTER TABLE `avisos`
  ADD CONSTRAINT `avisos_ibfk_1` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `comunicados_lidos`
--
ALTER TABLE `comunicados_lidos`
  ADD CONSTRAINT `comunicados_lidos_ibfk_1` FOREIGN KEY (`comunicado_id`) REFERENCES `comunicados` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comunicados_lidos_ibfk_2` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `encarregados`
--
ALTER TABLE `encarregados`
  ADD CONSTRAINT `encarregados_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `materiais`
--
ALTER TABLE `materiais`
  ADD CONSTRAINT `materiais_ibfk_1` FOREIGN KEY (`publicado_por`) REFERENCES `administradores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materiais_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `materiais_ibfk_3` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `materiais_acessos`
--
ALTER TABLE `materiais_acessos`
  ADD CONSTRAINT `materiais_acessos_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `materiais` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materiais_acessos_ibfk_2` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `mensalidades`
--
ALTER TABLE `mensalidades`
  ADD CONSTRAINT `mensalidades_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `pagamentos_matricula`
--
ALTER TABLE `pagamentos_matricula`
  ADD CONSTRAINT `pagamentos_matricula_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

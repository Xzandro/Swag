-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: sql462.your-server.de
-- Erstellungszeit: 24. Sep 2017 um 07:34
-- Server-Version: 5.6.35-1
-- PHP-Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `swagdev_db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fights`
--

CREATE TABLE `fights` (
  `fight_id` int(11) NOT NULL,
  `origin_id` int(11) NOT NULL,
  `guild_point_var` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `round1` int(11) NOT NULL,
  `round2` int(11) NOT NULL,
  `league_type` int(11) NOT NULL,
  `wizard_id` int(11) NOT NULL,
  `wizard_name` varchar(255) NOT NULL,
  `opp_wizard_id` int(11) NOT NULL,
  `opp_wizard_name` varchar(255) NOT NULL,
  `guild_id` int(11) NOT NULL,
  `opp_guild_id` int(11) NOT NULL,
  `log_type` int(11) NOT NULL,
  `battle_time` int(11) NOT NULL,
  `win_count` int(11) NOT NULL,
  `lose_count` int(11) NOT NULL,
  `draw_count` int(11) NOT NULL,
  `battle_end` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `guilds`
--

CREATE TABLE `guilds` (
  `guild_id` int(11) NOT NULL,
  `origin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `region_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `guild_id` int(11) NOT NULL,
  `opp_guild_id` int(11) NOT NULL,
  `log_type` int(11) NOT NULL,
  `last_fight` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `regions`
--

CREATE TABLE `regions` (
  `region_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `regions`
--

INSERT INTO `regions` (`region_id`, `name`) VALUES
(1, 'Global'),
(2, 'Europe'),
(3, 'Korea'),
(4, 'Asia');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `fights`
--
ALTER TABLE `fights`
  ADD PRIMARY KEY (`fight_id`),
  ADD KEY `match_id` (`match_id`),
  ADD KEY `wizard_id` (`wizard_id`),
  ADD KEY `opp_wizard_id` (`opp_wizard_id`);

--
-- Indizes für die Tabelle `guilds`
--
ALTER TABLE `guilds`
  ADD PRIMARY KEY (`guild_id`);

--
-- Indizes für die Tabelle `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`);

--
-- Indizes für die Tabelle `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`region_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `fights`
--
ALTER TABLE `fights`
  MODIFY `fight_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `guilds`
--
ALTER TABLE `guilds`
  MODIFY `guild_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `regions`
--
ALTER TABLE `regions`
  MODIFY `region_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

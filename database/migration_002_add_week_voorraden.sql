-- Migratie: bewaar overgebleven dozen per week
-- Hiermee wordt de traybestelling berekend als:
-- klantdozen + 3 reserve - overgebleven dozen, afgerond naar boven per 3 dozen.

CREATE TABLE IF NOT EXISTS `week_voorraden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weeknummer` int(11) NOT NULL,
  `jaar` int(11) NOT NULL,
  `overgebleven_dozen` int(11) NOT NULL DEFAULT 0,
  `bijgewerkt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `week_jaar` (`weeknummer`, `jaar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

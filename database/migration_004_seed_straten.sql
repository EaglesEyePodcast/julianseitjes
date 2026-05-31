-- Migratie: vul bezorgstraten veilig aan
-- Herstelt situaties waarin de straten-tabel leeg bleef na een eerdere migratie.

CREATE TABLE IF NOT EXISTS `straten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(100) NOT NULL,
  `actief` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `naam` (`naam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `straten` (`naam`, `actief`) VALUES
('1e Weerdsweg', 1),
('2e Weerdsweg', 1),
('Anna Reynvaanstraat', 1),
('Borgerlerstraat', 1),
('Hallensstraat', 1),
('Jacob van Bredastraat', 1),
('J.P. Sweelinckstraat', 1),
('Johannes Sinthenstraat', 1),
('Kromme Kerkstraat', 1),
('Radstakeweg', 1),
('Reinckenstraat', 1),
('Richard Paffraedstraat', 1),
('Sint Jurrienstraat', 1),
('Zwolseweg', 1)
ON DUPLICATE KEY UPDATE `actief` = VALUES(`actief`);

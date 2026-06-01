-- Migratie: Voeg straten en registratie toe
-- Voer uit na aanmaak originele database

-- ============================================
-- Tabel: straten (geldige leveringsgebieden)
-- ============================================
CREATE TABLE `straten` (
  `id` int(11) NOT NULL,
  `naam` varchar(100) NOT NULL UNIQUE,
  `actief` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `straten` (`naam`, `actief`) VALUES
('1e Weerdsweg', 1),
('2e Weerdsweg', 1),
('Alexander Hegiusstraat', 1),
('Anna Reynvaanstraat', 1),
('Borgerlerstraat', 1),
('Florens Radewijnszstraat', 1),
('Hallensstraat', 1),
('Jacob van Bredastraat', 1),
('JP Sweelinckstraat', 1),
('Johannes Sinthenstraat', 1),
('Kromme Kerkstraat', 1),
('Radstakeweg', 1),
('Reinckenstraat', 1),
('Richard Paffraedstraat', 1),
('Sallandstraat', 1),
('Sint Jurrienstraat', 1),
('Zwolseweg', 1);

-- ============================================
-- Tabel: registraties (wachtende klanten)
-- ============================================
CREATE TABLE `registraties` (
  `id` int(11) NOT NULL,
  `naam` varchar(100) NOT NULL,
  `straat` varchar(100) NOT NULL,
  `huisnummer` varchar(10) NOT NULL,
  `telefoon` varchar(20) DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `aantal_dozen` int(11) NOT NULL DEFAULT 1,
  `frequentie` enum('wekelijks','2wekelijks','3wekelijks') NOT NULL DEFAULT 'wekelijks',
  `startweek` int(11) NOT NULL DEFAULT 1,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `aangemaakt` timestamp NULL DEFAULT current_timestamp(),
  `goedgekeurd_op` timestamp NULL,
  `notitie` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Index aanmaken
-- ============================================
ALTER TABLE `straten`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `registraties`
  ADD PRIMARY KEY (`id`);

-- ============================================
-- AUTO_INCREMENT
-- ============================================
ALTER TABLE `straten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `registraties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

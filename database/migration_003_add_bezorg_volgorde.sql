-- Migratie: handmatige looproute per klant
-- Lage nummers worden eerst getoond in de weeklijst.
-- Klanten met 0 blijven onderaan in automatische straat/huisnummer-volgorde.

ALTER TABLE `klanten`
  ADD COLUMN `bezorg_volgorde` int(11) NOT NULL DEFAULT 0 AFTER `startweek`;

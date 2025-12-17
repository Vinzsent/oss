-- Create household_materials table if it doesn't exist
CREATE TABLE IF NOT EXISTS `household_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_name` varchar(255) NOT NULL,
  `material_type` varchar(255) DEFAULT NULL,
  `total_households` int(11) NOT NULL DEFAULT 0,
  `survey_year` int(4) DEFAULT NULL,
  `households` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert the household materials data
INSERT INTO `household_materials` (`material_name`, `total_households`, `survey_year`, `households`) VALUES
('Concrete', 2713, 2025, 2713),
('Semi or Half Concrete', 1881, 2025, 1881),
('Made up of Light Materials', 1009, 2025, 1009),
('Salvaged House', 50, 2025, 50);

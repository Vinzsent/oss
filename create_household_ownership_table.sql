-- Create household_ownership table if it doesn't exist
CREATE TABLE IF NOT EXISTS `household_ownership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownership_type` varchar(255) NOT NULL,
  `households` int(11) NOT NULL DEFAULT 0,
  `survey_year` int(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert the household ownership data
INSERT INTO `household_ownership` (`ownership_type`, `households`, `survey_year`) VALUES
('Owned', 3953, 2025),
('Rented', 951, 2025),
('Shared with Owner', 464, 2025),
('Shared with Renter', 50, 2025),
('Informal Settler Families (ISF)', 235, 2025);

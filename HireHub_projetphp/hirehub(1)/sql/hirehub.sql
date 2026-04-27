-- Création de la base de données
CREATE DATABASE IF NOT EXISTS hire_hub;
USE hire_hub;

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ROLE_USER', 'ROLE_ADMIN') DEFAULT 'ROLE_USER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des offres d'emploi
CREATE TABLE job_offers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    type ENUM('CDI', 'CDD', 'Stage', 'Alternance', 'Freelance') NOT NULL,
    salary VARCHAR(100),
    image_path VARCHAR(500),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table des candidatures
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    offer_id INT NOT NULL,
    user_id INT NOT NULL,
    cv_path VARCHAR(500) NOT NULL,
    motivation TEXT,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_application (offer_id, user_id),
    FOREIGN KEY (offer_id) REFERENCES job_offers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertion des comptes de test
INSERT INTO users (email, password, role) VALUES
('admin@hirehub.com', 'admin123', 'ROLE_ADMIN'),
('user1@test.com', 'user1', 'ROLE_USER'),
('user2@test.com', 'user2', 'ROLE_USER');

-- Insertion d'offres d'exemple
INSERT INTO job_offers (title, description, company, location, type, salary, created_by) VALUES
('Développeur PHP', 'Recherche développeur PHP expérimenté...', 'Tech', 'Casablanca', 'CDI', '45-50kDH', 1),
('Stage Marketing', 'Stage de 6 mois en marketing digital...', 'ProMarketing', 'casablanca', 'Stage', '2500DH/mois', 1),
('Alternance Web Design', 'Alternance sur 2 ans en design web...', 'DesignCom', 'Remote', 'Alternance', '2700DH/mois', 1);
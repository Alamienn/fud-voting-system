-- =============================================
-- FUD ONLINE VOTING SYSTEM - DATABASE SCHEMA
-- =============================================

CREATE DATABASE IF NOT EXISTS fud_voting_system;
USE fud_voting_system;

-- ===== STUDENTS TABLE =====
CREATE TABLE IF NOT EXISTS students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    reg_no VARCHAR(30) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    department VARCHAR(50) NOT NULL,
    level VARCHAR(10) NOT NULL,
    password VARCHAR(255) NOT NULL,
    has_voted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== POSITIONS TABLE =====
CREATE TABLE IF NOT EXISTS positions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    description TEXT,
    election_id INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== CANDIDATES TABLE =====
CREATE TABLE IF NOT EXISTS candidates (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    position_id INT(11) NOT NULL,
    department VARCHAR(50) NOT NULL,
    bio TEXT,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE
);

-- ===== ELECTIONS TABLE =====
CREATE TABLE IF NOT EXISTS elections (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('pending', 'active', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== VOTES TABLE =====
CREATE TABLE IF NOT EXISTS votes (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) NOT NULL,
    candidate_id INT(11) NOT NULL,
    position_id INT(11) NOT NULL,
    election_id INT(11) NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE,
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (student_id, position_id, election_id)
);

-- ===== ADMINS TABLE =====
CREATE TABLE IF NOT EXISTS admins (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== INSERT SAMPLE DATA =====

-- Insert default admin
INSERT INTO admins (username, password) VALUES 
('admin', MD5('admin123'));

-- Insert sample positions
INSERT INTO positions (title, description) VALUES 
('President', 'Chief executive of the Students\' Union Government'),
('Vice President', 'Deputy to the President'),
('Secretary General', 'Chief administrative officer'),
('Treasurer', 'Financial officer of the SUG');

-- Insert sample candidates
INSERT INTO candidates (full_name, position_id, department, bio) VALUES 
('Amina Musa', 1, 'Computer Science', 'Experienced student leader with a vision for digital transformation'),
('John Okafor', 1, 'Economics', 'Dedicated to improving student welfare and academic excellence'),
('Fatima Kabir', 2, 'Political Science', 'Passionate about gender equality and student rights'),
('David Adebayo', 2, 'Engineering', 'Committed to infrastructure development and innovation'),
('Oluwaseun Ibrahim', 3, 'Law', 'Advocate for transparency and good governance'),
('Chioma Mbah', 3, 'Mass Communication', 'Believes in effective communication and student engagement');

-- Insert sample election
INSERT INTO elections (title, start_date, end_date, status) VALUES 
('SUG Election 2026', '2026-03-10 08:00:00', '2026-03-15 17:00:00', 'active');
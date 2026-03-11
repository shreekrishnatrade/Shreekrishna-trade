-- Shree Krishna Services Database Schema
-- FIXED VERSION - Import this after selecting your database in phpMyAdmin

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(50),
    pincode VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(100),
    reset_token VARCHAR(100),
    reset_token_expiry DATETIME,
    INDEX idx_email (email),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Memberships Table
CREATE TABLE IF NOT EXISTS memberships (
    membership_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    plan_type ENUM('basic', 'premium', 'vip') NOT NULL,
    plan_name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    auto_renewal BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Service Requests Table
CREATE TABLE IF NOT EXISTS service_requests (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50),
    pincode VARCHAR(10),
    service_type VARCHAR(50) NOT NULL,
    appliance_brand VARCHAR(50),
    appliance_model VARCHAR(50),
    problem_description TEXT NOT NULL,
    preferred_date DATE,
    preferred_time TIME,
    status ENUM('pending', 'confirmed', 'assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    assigned_technician_id INT,
    estimated_cost DECIMAL(10,2),
    actual_cost DECIMAL(10,2),
    parts_replaced TEXT,
    technician_notes TEXT,
    customer_feedback TEXT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmed_at DATETIME,
    assigned_at DATETIME,
    completed_at DATETIME,
    cancelled_at DATETIME,
    cancellation_reason TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Technicians Table
CREATE TABLE IF NOT EXISTS technicians (
    technician_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    experience_years INT,
    photo_url VARCHAR(255),
    position VARCHAR(100),
    bio TEXT,
    is_available BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    joined_date DATE,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_jobs INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_specialization (specialization),
    INDEX idx_is_available (is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Projects Table
CREATE TABLE IF NOT EXISTS projects (
    project_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    service_type VARCHAR(50) NOT NULL,
    category VARCHAR(50),
    location VARCHAR(100) NOT NULL,
    customer_name VARCHAR(100),
    problem_description TEXT,
    solution_provided TEXT,
    parts_replaced TEXT,
    date_completed DATE NOT NULL,
    technician_id INT,
    technician_name VARCHAR(100),
    image_url VARCHAR(255),
    before_image_url VARCHAR(255),
    after_image_url VARCHAR(255),
    cost DECIMAL(10,2),
    duration_hours DECIMAL(4,2),
    customer_testimonial TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (technician_id) REFERENCES technicians(technician_id) ON DELETE SET NULL,
    INDEX idx_service_type (service_type),
    INDEX idx_is_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    replied BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Statistics Table (for homepage counters)
CREATE TABLE IF NOT EXISTS site_statistics (
    stat_id INT PRIMARY KEY AUTO_INCREMENT,
    satisfied_clients INT DEFAULT 0,
    services_delivered INT DEFAULT 0,
    years_experience INT DEFAULT 0,
    expert_technicians INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 4.50,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity Logs Table
CREATE TABLE IF NOT EXISTS activity_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    admin_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES admin_users(admin_id) ON DELETE SET NULL,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial statistics
INSERT INTO site_statistics (satisfied_clients, services_delivered, years_experience, expert_technicians, average_rating) 
VALUES (500, 2000, 10, 15, 4.80);

-- Insert default admin user (username: admin, password: Admin@123)
INSERT INTO admin_users (username, password_hash, full_name, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@shreekrishna.com', 'super_admin');

-- Insert sample technicians
INSERT INTO technicians (name, phone, specialization, position, experience_years, is_available) VALUES
('Rajesh Kumar', '9876543210', 'AC Repair Specialist', 'Senior Technician', 8, TRUE),
('Amit Sharma', '9876543211', 'Washing Machine Expert', 'Lead Technician', 6, TRUE),
('Suresh Patel', '9876543212', 'Refrigerator Specialist', 'Senior Technician', 7, TRUE),
('Vikas Singh', '9876543213', 'Microwave & Oven Expert', 'Technician', 4, TRUE),
('Prakash Yadav', '9876543214', 'All Appliances', 'Junior Technician', 2, TRUE);

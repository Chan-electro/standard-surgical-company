CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_type VARCHAR(50) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    city VARCHAR(120) NOT NULL,
    project VARCHAR(255) NOT NULL,
    start_timeline VARCHAR(50) DEFAULT NULL,
    products_note VARCHAR(255) DEFAULT NULL,
    message TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(120) NOT NULL,
    subtype VARCHAR(120) DEFAULT NULL,
    rails_material VARCHAR(40) DEFAULT NULL,
    rails_count TINYINT DEFAULT NULL,
    central_locking TINYINT(1) DEFAULT 0,
    iv_pole TINYINT(1) DEFAULT 0,
    urine_bag_holder TINYINT(1) DEFAULT 0,
    space_saving TINYINT(1) DEFAULT 0,
    pediatric TINYINT(1) DEFAULT 0,
    tv_mount TINYINT(1) DEFAULT 0,
    hero_image VARCHAR(255) DEFAULT NULL,
    gallery JSON DEFAULT NULL,
    specialties VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(150) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    cover_image VARCHAR(255) DEFAULT NULL,
    excerpt TEXT,
    content LONGTEXT,
    category VARCHAR(120) DEFAULT NULL,
    published_at DATE DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_name VARCHAR(150) NOT NULL,
    role_title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO products (sku, name, category, subtype, rails_material, rails_count, central_locking, iv_pole, urine_bag_holder, space_saving, pediatric, tv_mount, hero_image, gallery, specialties, created_at) VALUES
('ICU-5000', 'ICU Care Bed Pro', 'ICU Bed', '5 Function', 'SS', 4, 1, 1, 1, 0, 0, 1, '/assets/img/products/icu-bed-pro.jpg', JSON_ARRAY('/assets/img/products/icu-bed-pro-1.jpg', '/assets/img/products/icu-bed-pro-2.jpg'), 'ICU,Critical Care', '2024-01-10 00:00:00'),
('ER-2400', 'Rapid Response Stretcher', 'Stretcher', 'Hydraulic', 'MS', 2, 1, 1, 1, 1, 0, 0, '/assets/img/products/rapid-response-stretcher.jpg', JSON_ARRAY('/assets/img/products/rapid-response-stretcher-1.jpg'), 'Emergency,ER', '2024-02-18 00:00:00'),
('PED-1100', 'Pediatric Comfort Bed', 'Hospital Bed', 'Pediatric', 'Polymer', 4, 1, 1, 0, 1, 1, 1, '/assets/img/products/pediatric-comfort-bed.jpg', JSON_ARRAY('/assets/img/products/pediatric-comfort-bed-1.jpg'), 'Pediatric', '2023-12-05 00:00:00'),
('WARD-3300', 'Wardcare Electric Bed', 'Ward Bed', '3 Function', 'MS', 4, 1, 1, 1, 0, 0, 0, '/assets/img/products/wardcare-electric-bed.jpg', JSON_ARRAY('/assets/img/products/wardcare-electric-bed-1.jpg'), 'General Ward,Dialysis', '2024-03-12 00:00:00')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO posts (slug, title, cover_image, excerpt, content, category, published_at) VALUES
('icu-design-best-practices', 'Designing ICU Bed Spaces for Enhanced Safety', '/assets/img/posts/icu-design.jpg', 'Discover how modular ICU furniture supports infection control and rapid response teams while improving patient comfort.', '', 'ICU', '2024-03-22'),
('dialysis-center-layout', 'Optimizing Dialysis Centers with Flexible Furniture', '/assets/img/posts/dialysis-layout.jpg', 'Flexible furniture layouts allow dialysis centers to maximize throughput while maintaining patient privacy and safety.', '', 'Dialysis', '2024-02-10'),
('pediatric-care-environments', 'Human-Centered Design for Pediatric Care', '/assets/img/posts/pediatric-care.jpg', 'Color, ergonomics, and integrated entertainment systems transform pediatric wards into healing spaces.', '', 'Pediatric', '2024-01-14'),
('emergency-response-readiness', 'Preparing Emergency Departments for Surges', '/assets/img/posts/emergency-readiness.jpg', 'Standardized stretchers, equipment storage, and mobility solutions keep emergency teams ready for any case load.', '', 'Emergency', '2023-12-08')
ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO testimonials (author_name, role_title, message) VALUES
('Dr. Neha Sharma', 'Chief Medical Officer, CityCare Hospitals', 'The ICU Care Bed Pro series has drastically improved patient handling time and staff ergonomics across our network.'),
('Rajesh Patel', 'Procurement Head, Sunrise Dialysis', 'Installation and training were seamless. Standard Surgical Company\'s project team ensured zero downtime during our expansion.'),
('Anita Kumar', 'Nursing Superintendent, Rainbow Kids Hospital', 'Pediatric Comfort Beds are a hit with families—soft edges, integrated entertainment, and effortless cleaning.')
ON DUPLICATE KEY UPDATE message = VALUES(message);

INSERT INTO clients (name, logo, website, display_order) VALUES
('CityCare Hospitals', '/assets/img/clients/citycare.svg', 'https://citycare.example.com', 1),
('Sunrise Dialysis', '/assets/img/clients/sunrise.svg', 'https://sunrisedialysis.example.com', 2),
('Rainbow Kids Hospital', '/assets/img/clients/rainbow.svg', 'https://rainbowkids.example.com', 3),
('MetroLife Clinics', '/assets/img/clients/metrolife.svg', 'https://metrolife.example.com', 4)
ON DUPLICATE KEY UPDATE logo = VALUES(logo);

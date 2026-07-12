CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    email VARCHAR(120) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    role ENUM(
        'Fleet Manager',
        'Driver',
        'Safety Officer',
        'Financial Analyst'
    ) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
);

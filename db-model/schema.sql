CREATE DATABASE u822318855_volunteerhub;

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    user_role INT NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(10) NOT NULL CHECK (phone REGEXP '^[0-9]{10}$'),
    dob DATE NOT NULL,
    profile VARCHAR(255)
        CHECK (
            LOWER(profile) REGEXP '\\.jpg$' OR
            LOWER(profile) REGEXP '\\.png$' OR
            LOWER(profile) REGEXP '\\.svg$'
        ),
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_role) REFERENCES roles(id)
);


CREATE TABLE dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    dt_start DATETIME NOT NULL,
    dt_end DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);



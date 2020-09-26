CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender VARCHAR(6) NOT NULL,
    birthday DATE NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    mobile VARCHAR(11) NOT NULL,    
    password VARCHAR(255) NOT NULL
);
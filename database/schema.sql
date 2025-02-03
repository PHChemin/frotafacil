SET foreign_key_checks = 0;

DROP TABLE IF EXISTS supply_expenses;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS travels;
DROP TABLE IF EXISTS trucks_routes;
DROP TABLE IF EXISTS routes;
DROP TABLE IF EXISTS trucks;
DROP TABLE IF EXISTS truck_brands;
DROP TABLE IF EXISTS fleets;
DROP TABLE IF EXISTS drivers;
DROP TABLE IF EXISTS managers;
DROP TABLE IF EXISTS users;

-- Tabela de usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cpf CHAR(11) NOT NULL,
    encrypted_password VARCHAR(255) NOT NULL,
    name VARCHAR(95),
    email VARCHAR(255)
);

-- Tabela de gestores
CREATE TABLE managers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de motoristas
CREATE TABLE drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    license_category VARCHAR(9),
    gender CHAR(1),
    commission_percent DECIMAL(5,2),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de frotas
CREATE TABLE fleets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL,
    manager_id INT NOT NULL,
    FOREIGN KEY (manager_id) REFERENCES managers(id) ON DELETE CASCADE
);

-- Tabela de marcas de caminhões
CREATE TABLE truck_brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL
);

-- Tabela de caminhões
CREATE TABLE trucks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    truck_brand_id INT NOT NULL,
    model VARCHAR(45) NOT NULL,
    color VARCHAR(45) NOT NULL,
    plate CHAR(7) NOT NULL,
    fleet_id INT NOT NULL,
    driver_id INT NOT NULL,
    FOREIGN KEY (truck_brand_id) REFERENCES truck_brands(id) ON DELETE NO ACTION,
    FOREIGN KEY (fleet_id) REFERENCES fleets(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE NO ACTION
);

-- Tabela de rotas
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_address VARCHAR(90) NOT NULL,
    arrival_address VARCHAR(90) NOT NULL,
    distance INT NOT NULL,
    value DECIMAL(10,2) NOT NULL
);

-- Tabela de caminhões e rotas (NxN)
CREATE TABLE trucks_routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    truck_id INT NOT NULL,
    route_id INT NOT NULL,
    FOREIGN KEY (truck_id) REFERENCES trucks(id) ON DELETE NO ACTION,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE NO ACTION
);

-- Tabela de viagens
CREATE TABLE travels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    truck_id INT NOT NULL,
    route_id INT NOT NULL,
    driver_id INT NOT NULL,
    fleet_id INT NOT NULL,
    start_date DATETIME,
    finish_date DATETIME,
    driver_commission DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (truck_id) REFERENCES trucks(id) ON DELETE NO ACTION,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE NO ACTION,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE NO ACTION,
    FOREIGN KEY (fleet_id) REFERENCES fleets(id) ON DELETE NO ACTION
);

-- Tabela de abastecimentos
CREATE TABLE supply_expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    travel_id INT NOT NULL,
    liters_filled DECIMAL(6,2) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    date DATE NOT NULL,
    invoice_path VARCHAR(512) NOT NULL,
    FOREIGN KEY (travel_id) REFERENCES travels(id) ON DELETE NO ACTION
);

-- Tabela de despesas
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    travel_id INT NOT NULL,
    name VARCHAR(45) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    description MEDIUMTEXT NOT NULL,
    date DATE NOT NULL,
    invoice_path VARCHAR(512) NOT NULL,
    FOREIGN KEY (travel_id) REFERENCES travels(id) ON DELETE NO ACTION
);

SET foreign_key_checks = 1;

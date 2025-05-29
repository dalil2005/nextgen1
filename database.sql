
CREATE TABLE Admin (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL
);




CREATE TABLE ManagementNotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    details TEXT NOT NULL,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE contact_form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE PrivateService (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    RoomType ENUM('Empty', 'Furnished', 'old') NOT NULL,
    PricePerRoom DECIMAL(10, 2) NOT NULL
);


CREATE TABLE ProfessionalService (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    BuildingType ENUM('Empty', 'Furnished', 'old') NOT NULL,
    PricePer50m2 DECIMAL(10, 2) NOT NULL
);
 -- Clients table
CREATE TABLE Clients (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FirstName VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    Email VARCHAR(255) UNIQUE,
    PhoneNumber VARCHAR(15),
    Password VARCHAR(255) NOT NULL
);
CREATE TABLE CleaningWorkers (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FirstName VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    Sex VARCHAR(10),
    Age INT,
    Email VARCHAR(100),
    PhoneNumber VARCHAR(15),
    AcceptedAt DATE,
    Specialty VARCHAR(255),
    Password VARCHAR(255),
    City VARCHAR(100),
    Status VARCHAR(10) DEFAULT 'pending' CHECK (Status IN ('accepted', 'pending'))
);
CREATE TABLE Orders (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ClientID INT NOT NULL,
    ServiceCategory ENUM('private', 'professional') NOT NULL,
    NumberOfRooms INT DEFAULT NULL,
    AreaSize INT DEFAULT NULL,
    NumberOfFloors INT DEFAULT NULL,
    PropertyCondition ENUM('furnished', 'empty', 'old') NOT NULL,
    PaymentMethod ENUM('online', 'cash') NOT NULL,
    City VARCHAR(100) NOT NULL,
    Municipality VARCHAR(100) NOT NULL,
    BuildingAddress VARCHAR(255) NOT NULL,
    OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CleaningDate DATE DEFAULT NULL,
    Rating INT DEFAULT NULL,
    Comments TEXT,
    Status ENUM('pending', 'in_progress', 'completed', 'canceled') NOT NULL DEFAULT 'pending',
    CancelReason TEXT DEFAULT NULL,
    FOREIGN KEY (ClientID) REFERENCES Clients(ID) ON DELETE CASCADE
);

-- Order images table
CREATE TABLE OrderImages (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT NOT NULL,
    ImageURL VARCHAR(255) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES Orders(ID) ON DELETE CASCADE
);

-- Payments table (PaymentDate set in code only when status is 'completed')
CREATE TABLE Payments (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT NOT NULL,
    PaymentMethod ENUM('online', 'cash') NOT NULL,
    PaymentStatus ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    Amount DECIMAL(10, 2) NOT NULL,
    PaymentDate TIMESTAMP NULL,
    FOREIGN KEY (OrderID) REFERENCES Orders(ID) ON DELETE CASCADE
);

-- OrderWorkers (many-to-many between Orders and Workers)
CREATE TABLE OrderWorkers (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT NOT NULL,
    WorkerID INT NOT NULL,
    AssignedDate DATETIME NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES Orders(ID) ON DELETE CASCADE,
    FOREIGN KEY (WorkerID) REFERENCES CleaningWorkers(ID) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (OrderID, WorkerID)
);
INSERT INTO PrivateService (RoomType, PricePerRoom) VALUES
('Empty', 100),
('Furnished', 150),
('New', 200);

INSERT INTO ProfessionalService (BuildingType, PricePer50m2) VALUES
('Empty', 500),
('Furnished', 700),
('New', 900);

INSERT INTO Admin (Username, Password)
VALUES ('admin', SHA2('admin', 256));


DROP DATABASE IF EXISTS philhealth_db;
CREATE DATABASE philhealth_db;
USE philhealth_db;

CREATE TABLE CONTACT (
    Contact_ID INT AUTO_INCREMENT PRIMARY KEY,
    Permanent_Home_Add VARCHAR(100) NOT NULL,
    Mailing_Add VARCHAR(100) NOT NULL,
    HomePhone_Num VARCHAR(12) DEFAULT 'N/A',
    Mobile_Num VARCHAR(11) NOT NULL,
    BusinessD_Line VARCHAR(15) DEFAULT 'N/A',
    Email_Add VARCHAR(30) DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE MEMBER_CLASSIFICATION (
    Contributor_ID INT AUTO_INCREMENT PRIMARY KEY,
    Member_Type VARCHAR(20) NOT NULL,
    Contributor_Type VARCHAR(50) NOT NULL, 
    Profession VARCHAR(20) DEFAULT 'N/A',
    Monthly_Income DECIMAL(11,2) DEFAULT 0.00,
    Proof_of_Income VARCHAR(35) DEFAULT 'N/A',
    Financial_Incapable VARCHAR(3) DEFAULT 'NO' 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE KONSULTA_PROVIDER (
    Provider_ID INT AUTO_INCREMENT PRIMARY KEY,
    Konsulta_Provider VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE MEMBER_T (
    PIN_ID VARCHAR(20) PRIMARY KEY,
    Member_Name VARCHAR(100) NOT NULL,
    MotherMaiden_Name VARCHAR(100) NOT NULL,
    Spouse_Name VARCHAR(100) DEFAULT 'N/A',            
    Date_of_Birth DATE NOT NULL,
    Place_of_Birth VARCHAR(50) NOT NULL,
    Sex CHAR(1) NOT NULL,                              
    Civil_Status VARCHAR(20) NOT NULL,
    Member_Citizenship VARCHAR(20) NOT NULL,
    Philsys_ID VARCHAR(19) DEFAULT 'N/A', 
    TIN_ID VARCHAR(11) DEFAULT 'N/A',     
    Contact_ID INT NOT NULL,
    Contributor_ID INT NOT NULL,
    Provider_ID INT DEFAULT NULL,
    CONSTRAINT fk_member_contact FOREIGN KEY (Contact_ID) REFERENCES CONTACT(Contact_ID) ON DELETE CASCADE,
    CONSTRAINT fk_member_classification FOREIGN KEY (Contributor_ID) REFERENCES MEMBER_CLASSIFICATION(Contributor_ID) ON DELETE CASCADE,
    CONSTRAINT fk_member_provider FOREIGN KEY (Provider_ID) REFERENCES KONSULTA_PROVIDER(Provider_ID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE DEPENDENT (
    Dep_ID INT AUTO_INCREMENT PRIMARY KEY,
    PIN_ID VARCHAR(20) NOT NULL, 
    DepName VARCHAR(100) NOT NULL,
    Relationship VARCHAR(20) NOT NULL,
    Dependent_Date_of_Birth DATE NOT NULL,
    Dependent_Citizenship VARCHAR(20) NOT NULL,
    Dependent_With_Permanent_Disability VARCHAR(3) DEFAULT 'NO',
    CONSTRAINT fk_dependent_member FOREIGN KEY (PIN_ID) REFERENCES MEMBER_T(PIN_ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    pin_id VARCHAR(20) DEFAULT NULL,
    fullname VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, role, fullname) VALUES 
('admin', 'admin123', 'admin', 'System Administrator');
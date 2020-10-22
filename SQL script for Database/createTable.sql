/*
    Create Databsase
*/

DROP SCHEMA IF EXISTS Auction;

CREATE DATABASE Auction;

USE Auction;

/*
    Create User and grant permission for use in PHP
*/

DROP USER IF EXISTS 'AuctionUser'@'%';

CREATE USER 'AuctionUser'@'%' IDENTIFIED BY 'PasswordAuctionDBMS2020';
GRANT ALL PRIVILEGES ON Auction.* TO 'AuctionUser'@'%';

-- The table must be created in the correct order
/*
    Creating and inserting values for the UserStatus table
*/
DROP TABLE IF EXISTS UserStatus;
CREATE TABLE UserStatus (
    UserGroup VARCHAR(6) PRIMARY KEY
) ENGINE=InnoDB;

INSERT INTO UserStatus VALUES('Buyer');
INSERT INTO UserStatus VALUES('Seller');

/*
    Creating and inserting values for the CategoryList table
*/

DROP TABLE IF EXISTS CategoryList;
CREATE TABLE CategoryList (
    Category VARCHAR(50) PRIMARY KEY
) ENGINE=InnoDB;
INSERT INTO CategoryList VALUES('Antiques');
INSERT INTO CategoryList VALUES('Art');
INSERT INTO CategoryList VALUES('Baby Stuff');
INSERT INTO CategoryList VALUES('Books');
INSERT INTO CategoryList VALUES('Office and Industrial Equipment');
INSERT INTO CategoryList VALUES('Cameras and Photo');
INSERT INTO CategoryList VALUES('Powered Vehicles');
INSERT INTO CategoryList VALUES('Collectables');
INSERT INTO CategoryList VALUES('Computers Equipment');
INSERT INTO CategoryList VALUES('Toys');
INSERT INTO CategoryList VALUES('Event Tickets');
INSERT INTO CategoryList VALUES('Garden Equipments');
INSERT INTO CategoryList VALUES('Health and Beauty');
INSERT INTO CategoryList VALUES('DIY Tools');
INSERT INTO CategoryList VALUES('Musical Instruments');

/*
    Creating and inserting values for the Users table
*/
DROP TABLE IF EXISTS Users;
CREATE TABLE Users (
    Username VARCHAR(50),
    AuthPassWord VARCHAR(50) NOT NULL,
    UserGroup VARCHAR(6),
    Email VARCHAR(100) NOT NULL,
    PRIMARY KEY (Username),
    CONSTRAINT FK_UserGroup FOREIGN KEY (UserGroup) REFERENCES auction.UserStatus(UserGroup) ON DELETE CASCADE
) ENGINE=InnoDB;

--generated values using mock data generator

DROP TABLE IF EXISTS Auctions;
CREATE TABLE Auctions (
    AuctionID INT AUTO_INCREMENT,
    UserName VARCHAR(50),
    ItemName TINYTEXT NOT NULL,
    ItemDescription MEDIUMTEXT NOT NULL,
    Category VARCHAR(50),
    StartingPrice FLOAT(10, 2) NOT NULL,
    ReservePrice FLOAT(10, 2) NOT NULL,
    EndingTime DATETIME NOT NULL,
    PRIMARY KEY (AuctionID),
    CONSTRAINT CHECK(ReservePrice > StartingPrice),
    CONSTRAINT FK_UserName_Bid FOREIGN KEY (Username) REFERENCES auction.Users(Username),
    CONSTRAINT FK_Category_Bid FOREIGN KEY (Category) REFERENCES auction.CategoryList(Category)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS Bids;
CREATE TABLE Bids (
    BidID INT AUTO_INCREMENT,
    UserName VARCHAR(50),
    AuctionID INT,
    BidPrice FLOAT(10, 2) NOT NULL,
    BidTime DATETIME NOT NULL,
    Outcome VARCHAR(7),
    PRIMARY KEY (BidID),
    CONSTRAINT CHECK(Outcome IN ("Success", "Failed", "Pending")), 
    CONSTRAINT FK_UserName_Bids FOREIGN KEY (Username) REFERENCES auction.Users(Username),
    CONSTRAINT FK_AuctionID_Bids FOREIGN KEY (AuctionID) REFERENCES auction.Auctions(AuctionID)
) ENGINE=InnoDB;
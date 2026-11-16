PRAGMA foreign_keys = ON;

CREATE TABLE "USER" (
    userId INTEGER PRIMARY KEY AUTOINCREMENT,
    userName VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    firstName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL
);
CREATE TABLE ACCOUNT (
    accountId INTEGER PRIMARY KEY AUTOINCREMENT,
    userId INTEGER NOT NULL REFERENCES "USER"(userId),
    accountType VARCHAR(100) NOT NULL CHECK (length(trim(accountType)) BETWEEN 1 AND 100),
    points INTEGER NOT NULL CHECK (typeof(points) = 'integer' AND points >= 0)
);
CREATE TABLE GIFTCARD (
    cardId INTEGER PRIMARY KEY AUTOINCREMENT,
    cardName VARCHAR(100) NOT NULL CHECK (length(trim(cardName)) BETWEEN 1 AND 100),
    cardType VARCHAR(100) NOT NULL CHECK (length(trim(cardType)) BETWEEN 1 AND 100 AND cardType IN ('Dining', 'Shopping', 'Travel')),
    cardValue FLOAT NOT NULL CHECK (typeof(cardValue) IN ('integer', 'real') AND cardValue > 0 AND cardValue <= 1.7976931348623157e308),
    points INTEGER NOT NULL CHECK (typeof(points) = 'integer' AND points > 0)
);

DROP DATABASE IF EXISTS sm_bank_db;
CREATE DATABASE sm_bank_db;
USE sm_bank_db;

DROP TABLE IF EXISTS account_types;
CREATE TABLE account_types(
    account_type VARCHAR(20),
    interest_rate FLOAT(5, 3),
    PRIMARY KEY (account_type)
);

DROP TABLE IF EXISTS users;
CREATE TABLE users(
    nic VARCHAR(12),
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50),
    address VARCHAR(200) NOT NULL,
    birthday VARCHAR(10) NOT NULL,
    profession VARCHAR(100),
    contact_number VARCHAR(10) NOT NULL,
    email VARCHAR(100) UNIQUE,
    user_type ENUM('admin', 'user') NOT NULL,
    last_login DATETIME,
    PRIMARY KEY (nic)
);

DROP TABLE IF EXISTS user_identity;
CREATE TABLE user_identity(
    username VARCHAR(101),
    nic VARCHAR(12) NOT NULL,
    hashed_password VARCHAR(40) NOT NULL,
    salt BIGINT(10) NOT NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    PRIMARY KEY (username),
    FOREIGN KEY (nic) REFERENCES users(nic)
);

DROP TABLE IF EXISTS accounts;
CREATE TABLE accounts(
    account_number BIGINT(10) AUTO_INCREMENT,
    nic VARCHAR(12) NOT NULL,
    account_type VARCHAR(20) NOT NULL,
    current_balance FLOAT(24, 2) DEFAULT 0,
    is_deleted TINYINT(1) DEFAULT 0,
    PRIMARY KEY (account_number),
    FOREIGN KEY (nic) REFERENCES users(nic),
    FOREIGN KEY (account_type) REFERENCES account_types(account_type)
);

DROP TABLE IF EXISTS money_transfers;
CREATE TABLE money_transfers(
    money_transfer_id BIGINT(15) AUTO_INCREMENT,
    time_stamp DATETIME NOT NULL,
    debit_account BIGINT(10) NOT NULL,
    credit_account BIGINT(10) NOT NULL,
    amount FLOAT(24, 2) NOT NULL,
    PRIMARY KEY (money_transfer_id),
    FOREIGN KEY (debit_account) REFERENCES accounts(account_number),
    FOREIGN KEY (credit_account) REFERENCES accounts(account_number)
);

DROP TABLE IF EXISTS transactions;
CREATE TABLE transactions(
    transaction_id BIGINT(15) AUTO_INCREMENT,
    time_stamp DATETIME NOT NULL,
    account_number BIGINT(10) NOT NULL,
    description VARCHAR(50),
    money_transfer_id BIGINT(15),
    amount FLOAT(24, 2) NOT NULL,
    balance FLOAT(24, 2) NOT NULL,
    PRIMARY KEY (transaction_id),
    FOREIGN KEY (account_number) REFERENCES accounts(account_number),
    FOREIGN KEY (money_transfer_id) REFERENCES money_transfers(money_transfer_id)
);

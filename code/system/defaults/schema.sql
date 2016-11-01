
-- Database tables schema of WizyTówka CMS.
-- "wt_dbms" symbol means SQL lines adequate only for specified DBMS.

CREATE TABLE Users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, -- wt_dbms: sqlite
	id INTEGER PRIMARY KEY AUTO_INCREMENT,         -- wt_dbms: mysql
	id SERIAL PRIMARY KEY,                         -- wt_dbms: pgsql
	name VARCHAR(100) NOT NULL UNIQUE,
	password VARCHAR(100) NOT NULL,
	createdTime BIGINT NOT NULL DEFAULT 0
);

CREATE TABLE Files (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, -- wt_dbms: sqlite
	id INTEGER PRIMARY KEY AUTO_INCREMENT,         -- wt_dbms: mysql
	id SERIAL PRIMARY KEY,                         -- wt_dbms: pgsql
	name VARCHAR(100) NOT NULL UNIQUE,
	userId INTEGER REFERENCES Users(id) ON DELETE SET NULL,
	uploadedTime BIGINT NOT NULL DEFAULT 0
);

CREATE TABLE Languages (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, -- wt_dbms: sqlite
	id INTEGER PRIMARY KEY AUTO_INCREMENT,         -- wt_dbms: mysql
	id SERIAL PRIMARY KEY,                         -- wt_dbms: pgsql
	slug VARCHAR(100) NOT NULL UNIQUE,
	name VARCHAR(100) NOT NULL
);

CREATE TABLE Pages (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, -- wt_dbms: sqlite
	id INTEGER PRIMARY KEY AUTO_INCREMENT,         -- wt_dbms: mysql
	id SERIAL PRIMARY KEY,                         -- wt_dbms: pgsql
	slug VARCHAR(100) NOT NULL UNIQUE,
	contentType VARCHAR(100) NOT NULL,
	title VARCHAR(500) NOT NULL,
	titleMenu VARCHAR(500),
	titleHead VARCHAR(500),
	description VARCHAR(500),
	contents TEXT NOT NULL,
	settings TEXT NOT NULL,
	userId INTEGER REFERENCES Users(id) ON DELETE SET NULL,
	languageId INTEGER NOT NULL REFERENCES Languages(id) ON DELETE CASCADE,
	updatedTime BIGINT NOT NULL DEFAULT 0,
	createdTime BIGINT NOT NULL DEFAULT 0
);

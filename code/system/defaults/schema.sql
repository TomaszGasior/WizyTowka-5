-- This file is an template for real schema and it should be parsed before using.
-- "wt_dbms" symbol means SQL lines used only for specified DBMS.

ALTER DATABASE DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci; -- wt_dbms: mysql

CREATE TABLE Users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, -- wt_dbms: sqlite
	id INTEGER PRIMARY KEY AUTO_INCREMENT,         -- wt_dbms: mysql
	id SERIAL PRIMARY KEY,                         -- wt_dbms: pgsql
	name VARCHAR(100) NOT NULL UNIQUE COLLATE utf8mb4_bin, -- wt_dbms: mysql  -- Force case sensitive.
	name VARCHAR(100) NOT NULL UNIQUE,                     -- wt_dbms: ! mysql
	password VARCHAR(100) NOT NULL,
	email VARCHAR(100),
	permissions SMALLINT NOT NULL DEFAULT 0,
	lastLoginTime INTEGER UNSIGNED DEFAULT 0, -- wt_dbms: mysql
	lastLoginTime BIGINT DEFAULT 0,           -- wt_dbms: ! mysql
	createdTime INTEGER UNSIGNED NOT NULL DEFAULT 0 -- wt_dbms: mysql
	createdTime BIGINT NOT NULL DEFAULT 0           -- wt_dbms: ! mysql
);

CREATE TABLE Pages (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, -- wt_dbms: sqlite
	id INTEGER PRIMARY KEY AUTO_INCREMENT,         -- wt_dbms: mysql
	id SERIAL PRIMARY KEY,                         -- wt_dbms: pgsql
	slug VARCHAR(100) NOT NULL UNIQUE,
	title VARCHAR(500) NOT NULL,
	titleHead VARCHAR(500),
	description VARCHAR(500),
	noIndex SMALLINT NOT NULL CHECK(isDraft IN (0,1)),
	isDraft SMALLINT NOT NULL CHECK(isDraft IN (0,1)),
	contentType VARCHAR(50) NOT NULL,
	contents TEXT NOT NULL,
	settings TEXT NOT NULL,
	userId INTEGER,
	updatedTime INTEGER UNSIGNED NOT NULL DEFAULT 0, -- wt_dbms: mysql
	updatedTime BIGINT NOT NULL DEFAULT 0,           -- wt_dbms: ! mysql
	createdTime INTEGER UNSIGNED NOT NULL DEFAULT 0, -- wt_dbms: mysql
	createdTime BIGINT NOT NULL DEFAULT 0,           -- wt_dbms: ! mysql
	FOREIGN KEY(userId) REFERENCES Users(id) ON DELETE SET NULL
);
CREATE INDEX Pages_isDraft ON Pages(isDraft);

-- Workaround for MySQL's lack of support of CHECK constraint.
CREATE TRIGGER Pages_isDraft_INSERT BEFORE INSERT ON Pages FOR EACH ROW -- wt_dbms: mysql
	SET NEW.isDraft = IF(NEW.isDraft <> 0, 1, 0);                       -- wt_dbms: mysql
CREATE TRIGGER Pages_isDraft_UPDATE BEFORE UPDATE ON Pages FOR EACH ROW -- wt_dbms: mysql
	SET NEW.isDraft = IF(NEW.isDraft <> 0, 1, 0);                       -- wt_dbms: mysql
CREATE TRIGGER Pages_noIndex_INSERT BEFORE INSERT ON Pages FOR EACH ROW -- wt_dbms: mysql
	SET NEW.noIndex = IF(NEW.noIndex <> 0, 1, 0);                       -- wt_dbms: mysql
CREATE TRIGGER Pages_noIndex_UPDATE BEFORE UPDATE ON Pages FOR EACH ROW -- wt_dbms: mysql
	SET NEW.noIndex = IF(NEW.noIndex <> 0, 1, 0);                       -- wt_dbms: mysql


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

CREATE TABLE Pages (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, -- wt_dbms: sqlite
	id INTEGER PRIMARY KEY AUTO_INCREMENT,         -- wt_dbms: mysql
	id SERIAL PRIMARY KEY,                         -- wt_dbms: pgsql
	slug VARCHAR(100) NOT NULL UNIQUE,
	contentType VARCHAR(100) NOT NULL,
	title VARCHAR(500) NOT NULL,
	titleHead VARCHAR(500),
	description VARCHAR(500),
	isDraft SMALLINT NOT NULL CHECK(isDraft IN (0,1)),
	contents TEXT NOT NULL,
	settings TEXT NOT NULL,
	userId INTEGER REFERENCES Users(id) ON DELETE SET NULL,
	updatedTime BIGINT NOT NULL DEFAULT 0,
	createdTime BIGINT NOT NULL DEFAULT 0
);
CREATE INDEX Pages_isDraft ON Pages(isDraft);

-- Workaround for Pages.isDraft. MySQL have not support of CHECK constraint.
CREATE TRIGGER Pages_isDraft_INSERT BEFORE INSERT ON Pages FOR EACH ROW -- wt_dbms: mysql
	SET NEW.isDraft = IF(NEW.isDraft <> 0, 1, 0);                       -- wt_dbms: mysql
CREATE TRIGGER Pages_isDraft_UPDATE BEFORE UPDATE ON Pages FOR EACH ROW -- wt_dbms: mysql
	SET NEW.isDraft = IF(NEW.isDraft <> 0, 1, 0);                       -- wt_dbms: mysql
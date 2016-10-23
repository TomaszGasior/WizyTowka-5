
-- Database tables schema for SQLite DBMS.

-- Users table.

CREATE TABLE Users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	username TEXT NOT NULL UNIQUE,
	password TEXT NOT NULL,
	lastLoginTime INTEGER NOT NULL,
	registeredTime INTEGER NOT NULL
);

-- Files table.

CREATE TABLE Files (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	filename TEXT NOT NULL UNIQUE,
	userId INTEGER NOT NULL REFERENCES Users(id) ON UPDATE CASCADE ON DELETE SET NULL,
	uploadedTime INTEGER NOT NULL
);

-- PagesLanguages table.

CREATE TABLE PagesLanguages (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	name TEXT NOT NULL,
	current INTEGER NOT NULL DEFAULT 0
);

-- PagesStrings table.

CREATE TABLE PagesStrings (
	pageId INTEGER NOT NULL REFERENCES PagesRaw(id) ON UPDATE CASCADE ON DELETE CASCADE,
	languageId INTEGER NOT NULL REFERENCES PagesLanguages(id) ON UPDATE CASCADE ON DELETE CASCADE,
	title TEXT NOT NULL,
	contents TEXT NOT NULL DEFAULT "[]",   -- JSON data will be placed here.
	PRIMARY KEY(pageId, languageId)
);

-- PagesRaw table.

CREATE TABLE PagesRaw (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	slug TEXT NOT NULL UNIQUE,
	contentType TEXT NOT NULL,
	settings TEXT NOT NULL DEFAULT "[]",   -- JSON data will be placed here.
	userId INTEGER NOT NULL REFERENCES Users(id) ON UPDATE CASCADE ON DELETE SET NULL,
	updatedDate INTEGER NOT NULL,
	createdDate INTEGER NOT NULL
);
CREATE INDEX slug ON PagesRaw(slug);

-- Pages view.

CREATE VIEW Pages AS
SELECT PagesRaw.id, PagesRaw.slug, PagesStrings.title, PagesRaw.contentType, PagesStrings.contents,
       PagesRaw.settings, PagesRaw.userId, PagesRaw.updatedDate, PagesRaw.createdDate
FROM PagesRaw, PagesStrings
WHERE PagesRaw.id = PagesStrings.pageId AND PagesStrings.languageId = (SELECT id FROM PagesLanguages WHERE current = 1);
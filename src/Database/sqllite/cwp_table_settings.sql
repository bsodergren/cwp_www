CREATE TABLE "settings" (
	"id"	INTEGER NOT NULL UNIQUE,
	"setting_name" TEXT NOT NULL UNIQUE,
	"setting_value"	TEXT DEFAULT NULL,
	"setting_type"	TEXT NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);

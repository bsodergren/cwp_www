CREATE TABLE "pub_trim" (
	"id"	INTEGER NOT NULL UNIQUE,
	"pub_name"	TEXT,
	"bind"	TEXT,
	"head_trim"	TEXT,
	"foot_trim"	TEXT,
	"delivered_size"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
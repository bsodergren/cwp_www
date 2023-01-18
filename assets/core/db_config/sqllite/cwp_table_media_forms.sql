CREATE TABLE "media_forms" (
	"id"	INTEGER NOT NULL UNIQUE,
	"job_id"	int NOT NULL,
	"form_number"	int NOT NULL,
	"product"	varchar(26) NOT NULL,
	"count"	int NOT NULL,
	"bind"	varchar(4) NOT NULL,
	"config"	varchar(20) NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);
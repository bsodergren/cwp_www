CREATE TABLE "form_data" (
	"id"	INTEGER NOT NULL UNIQUE,
	"job_id"	int NOT NULL,
	"form_number"	int NOT NULL,
	"form_letter"	varchar(12) NOT NULL,
	"original"	varchar(200) NOT NULL,
	"market"	varchar(100) NOT NULL,
	"pub"	varchar(100) NOT NULL,
	"count"	varchar(10) NOT NULL,
	"ship"	varchar(100) NOT NULL,
	"former"	varchar(6) DEFAULT 'Front',
	"face_trim"	int DEFAULT '0',
	"no_bindery"	int NOT NULL DEFAULT '0',
	PRIMARY KEY("id" AUTOINCREMENT)
);
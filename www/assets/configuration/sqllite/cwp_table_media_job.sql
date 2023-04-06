CREATE TABLE "media_job" (
	"job_id"	INTEGER NOT NULL UNIQUE,
	"job_number"	int NOT NULL,
	"pdf_file"	varchar(200) NOT NULL,
	"zip_file"	varchar(300) DEFAULT NULL,
	"xlsx_dir"	varchar(300) DEFAULT NULL,
	"close"	varchar(70) DEFAULT NULL,
	"hidden"	int NOT NULL DEFAULT '0',
	PRIMARY KEY("job_id" AUTOINCREMENT)
);
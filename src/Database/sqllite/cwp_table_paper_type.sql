CREATE TABLE "paper_type" (
	"id"	INTEGER NOT NULL UNIQUE,
	"paper_wieght"	int NOT NULL,
	"paper_size"	varchar(20) NOT NULL,
	"pages"	int NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);
--
-- Dumping data for table `paper_type`
--

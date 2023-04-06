CREATE TABLE "flag_style" (
	"id"	INTEGER NOT NULL UNIQUE,
	"style_name"	varchar(150) NOT NULL,
	"ecol"	varchar(5) DEFAULT NULL,
	"erow"	varchar(3) DEFAULT NULL,
	"text"	varchar(50) DEFAULT NULL,
	"bold"	int DEFAULT NULL,
	"font_size"	int DEFAULT NULL,
	"h_align"	int DEFAULT NULL,
	"v_align"	int DEFAULT NULL,
	"width"	int DEFAULT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);
--
-- Dumping data for table `flag_style`
--

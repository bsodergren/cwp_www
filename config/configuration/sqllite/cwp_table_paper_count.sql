CREATE TABLE "paper_count" (
	"id"	INTEGER NOT NULL UNIQUE,
	"paper_id"	int NOT NULL,
	"pcs_carton"	int DEFAULT NULL,
	"back_lift"	int DEFAULT NULL,
	"front_lift"	int DEFAULT NULL,
	"max_carton"	int DEFAULT NULL,
	"max_half_skid"	int DEFAULT NULL,
	"max_full_skid"	int DEFAULT NULL,
	"half_skid_lifts_layer"	int DEFAULT NULL,
	"full_skid_lifts_layer"	int DEFAULT NULL,
	"back_half_skid_layers"	int DEFAULT NULL,
	"back_full_skid_layers"	int DEFAULT NULL,
	"front_half_skid_layers"	int DEFAULT NULL,
	"front_full_skid_layers"	int DEFAULT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);
--
-- Dumping data for table `paper_count`
--

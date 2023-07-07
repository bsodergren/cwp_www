CREATE TABLE "form_data_count" (
	"id"	INTEGER NOT NULL,
	"form_id"	INTEGER,
	"job_id"	INTEGER,
	"packaging"	TEXT NOT NULL,
	"full_boxes"	INTEGER,
	"layers_last_box"	INTEGER,
	"lifts_last_layer"	INTEGER,
	"lift_size"	INTEGER,
	"lifts_per_layer"	INTEGER,
	"form_number"	TEXT,
	"count"	INTEGER,
	"layers_per_skid"	INTEGER,
	"market"	TEXT,
	"former"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
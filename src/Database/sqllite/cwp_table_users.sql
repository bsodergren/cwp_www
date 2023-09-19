
CREATE TABLE "users" (
	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK ("id" >= 0),
	"email" VARCHAR(249) NOT NULL,
	"password" VARCHAR(255) NOT NULL,
	"username" VARCHAR(100) DEFAULT NULL,
	"status" INTEGER NOT NULL CHECK ("status" >= 0) DEFAULT "0",
	"verified" INTEGER NOT NULL CHECK ("verified" >= 0) DEFAULT "0",
	"resettable" INTEGER NOT NULL CHECK ("resettable" >= 0) DEFAULT "1",
	"roles_mask" INTEGER NOT NULL CHECK ("roles_mask" >= 0) DEFAULT "0",
	"registered" INTEGER NOT NULL CHECK ("registered" >= 0),
	"last_login" INTEGER CHECK ("last_login" >= 0) DEFAULT NULL,
	"force_logout" INTEGER NOT NULL CHECK ("force_logout" >= 0) DEFAULT "0",
	CONSTRAINT "email" UNIQUE ("email")
);
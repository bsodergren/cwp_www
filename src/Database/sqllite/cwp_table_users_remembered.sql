CREATE TABLE "users_remembered" (
	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK ("id" >= 0),
	"user" INTEGER NOT NULL CHECK ("user" >= 0),
	"selector" VARCHAR(24) NOT NULL,
	"token" VARCHAR(255) NOT NULL,
	"expires" INTEGER NOT NULL CHECK ("expires" >= 0),
	CONSTRAINT "selector" UNIQUE ("selector")
);
CREATE INDEX "users_remembered.user" ON "users_remembered" ("user");



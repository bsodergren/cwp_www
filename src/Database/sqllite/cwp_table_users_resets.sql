CREATE TABLE "users_resets" (
	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK ("id" >= 0),
	"user" INTEGER NOT NULL CHECK ("user" >= 0),
	"selector" VARCHAR(20) NOT NULL,
	"token" VARCHAR(255) NOT NULL,
	"expires" INTEGER NOT NULL CHECK ("expires" >= 0),
	CONSTRAINT "selector" UNIQUE ("selector")
);
CREATE INDEX "users_resets.user_expires" ON "users_resets" ("user", "expires");



CREATE TABLE "users_confirmations" (
	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK ("id" >= 0),
	"user_id" INTEGER NOT NULL CHECK ("user_id" >= 0),
	"email" VARCHAR(249) NOT NULL,
	"selector" VARCHAR(16) NOT NULL,
	"token" VARCHAR(255) NOT NULL,
	"expires" INTEGER NOT NULL CHECK ("expires" >= 0),
	CONSTRAINT "selector" UNIQUE ("selector")
);
CREATE INDEX "users_confirmations.email_expires" ON "users_confirmations" ("email", "expires");
CREATE INDEX "users_confirmations.user_id" ON "users_confirmations" ("user_id");



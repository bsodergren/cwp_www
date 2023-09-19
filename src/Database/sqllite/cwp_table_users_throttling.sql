
CREATE TABLE "users_throttling" (
	"bucket" VARCHAR(44) PRIMARY KEY NOT NULL,
	"tokens" REAL NOT NULL CHECK ("tokens" >= 0),
	"replenished_at" INTEGER NOT NULL CHECK ("replenished_at" >= 0),
	"expires_at" INTEGER NOT NULL CHECK ("expires_at" >= 0)
);
CREATE INDEX "users_throttling.expires_at" ON "users_throttling" ("expires_at");

-- Adminer 4.3.1 PostgreSQL dump

DROP TABLE IF EXISTS "Repetiteur_term_availibilityDay";
CREATE TABLE "public"."Repetiteur_term_availibilityDay" (
    "repetiteur_term_id" bigint NOT NULL,
    "day" character varying(8) NOT NULL
) WITH (oids = false);


DROP TABLE IF EXISTS "repetiteur_term";
CREATE SEQUENCE repetiteur_term_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."repetiteur_term" (
    "id" bigint DEFAULT nextval('repetiteur_term_id_seq') NOT NULL,
    "userid" integer NOT NULL,
    "annee" integer NOT NULL,
    "term" character varying(2),
    "fee" smallint NOT NULL,
    "feemin" smallint NOT NULL,
    "feemax" smallint NOT NULL,
    "available" smallint NOT NULL,
    "commentaire" text NOT NULL,
    CONSTRAINT "Nonerepetiteur_term_id_pkey" PRIMARY KEY ("id"),
    CONSTRAINT "Nonerepetiteur_term_userid_annee_term" UNIQUE ("userid", "annee", "term")
) WITH (oids = false);

CREATE INDEX "Nonerepetiteur_term_userid" ON "public"."repetiteur_term" USING btree ("userid");


DROP TABLE IF EXISTS "repetiteur_term_availibilityday";
CREATE TABLE "public"."repetiteur_term_availibilityday" (
    "repetiteur_term_id" bigint NOT NULL,
    "day" character varying(8) NOT NULL
) WITH (oids = false);

CREATE INDEX "Nonerepetiteur_term_availibilityday_repetiteur_term_id" ON "public"."repetiteur_term_availibilityday" USING btree ("repetiteur_term_id");


DROP TABLE IF EXISTS "repetiteur_term_language";
CREATE TABLE "public"."repetiteur_term_language" (
    "repetiteur_term_id" bigint NOT NULL,
    "language" character varying(2) NOT NULL
) WITH (oids = false);

CREATE INDEX "Nonerepetiteur_term_language_repetiteur_term_id" ON "public"."repetiteur_term_language" USING btree ("repetiteur_term_id");


DROP TABLE IF EXISTS "repetiteur_term_place";
CREATE TABLE "public"."repetiteur_term_place" (
    "repetiteur_term_id" bigint NOT NULL,
    "place" character varying(8) NOT NULL
) WITH (oids = false);

CREATE INDEX "Nonerepetiteur_term_place_repetiteur_term_id" ON "public"."repetiteur_term_place" USING btree ("repetiteur_term_id");


DROP TABLE IF EXISTS "repetiteur_term_placeComment";
CREATE TABLE "public"."repetiteur_term_placeComment" (
    "repetiteur_term_id" bigint NOT NULL,
    "commentaire" character varying(256) NOT NULL
) WITH (oids = false);


DROP TABLE IF EXISTS "repetiteur_term_placecomment";
CREATE TABLE "public"."repetiteur_term_placecomment" (
    "repetiteur_term_id" bigint NOT NULL,
    "commentaire" character varying(256) NOT NULL,
    CONSTRAINT "Nonerepetiteur_term_placecomment_repetiteur_term_id_pkey" PRIMARY KEY ("repetiteur_term_id")
) WITH (oids = false);

CREATE INDEX "Nonerepetiteur_term_placecomment_repetiteur_term_id" ON "public"."repetiteur_term_placecomment" USING btree ("repetiteur_term_id");


DROP TABLE IF EXISTS "repetiteur_term_subject";
CREATE TABLE "public"."repetiteur_term_subject" (
    "repetiteur_term_id" bigint NOT NULL,
    "sujet" character varying(8) NOT NULL
) WITH (oids = false);

CREATE INDEX "Nonerepetiteur_term_subject_repetiteur_term_id" ON "public"."repetiteur_term_subject" USING btree ("repetiteur_term_id");


DROP TABLE IF EXISTS "users";
CREATE SEQUENCE users_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."users" (
    "id" integer DEFAULT nextval('users_id_seq') NOT NULL,
    "pseudo" character varying(16) NOT NULL,
    "levelweb" smallint DEFAULT 1 NOT NULL,
    "levelstudies" smallint NOT NULL,
    "email" character varying(128) NOT NULL,
    "name" character varying(32) NOT NULL,
    "forename" character varying(32) NOT NULL,
    "section" character varying(4) NOT NULL,
    "passwordsalt" character varying(16) NOT NULL,
    "password" character varying(40) NOT NULL,
    "timeregistered" bigint NOT NULL,
    "timelastconnection" bigint DEFAULT 0 NOT NULL,
    "active" smallint DEFAULT 1 NOT NULL,
    CONSTRAINT "Noneusers_id_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "users_activation";
CREATE TABLE "public"."users_activation" (
    "id" integer NOT NULL,
    "regkey" character varying(16) NOT NULL,
    CONSTRAINT "Noneusers_activation_id_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


-- 2017-05-12 07:00:31.547898+00


-- Database: PLF

-- DROP DATABASE IF EXISTS "plf_dev";


-- CREATE ROLE plf WITH
-- 	LOGIN
-- 	SUPERUSER
-- 	CREATEDB
-- 	CREATEROLE
-- 	INHERIT
-- 	REPLICATION
-- 	CONNECTION LIMIT -1
-- 	PASSWORD 'Chri12!!';

-- CREATE DATABASE "plf_dev"
--     WITH
--     OWNER = plf
--     ENCODING = 'UTF8'
--     TABLESPACE = pg_default
--     CONNECTION LIMIT = -1
--     IS_TEMPLATE = False;



UPDATE pg_database SET datcollate='fr_BE.UTF8', datctype='fr_BE.UTF-8' WHERE datname='plf_dev';


GRANT TEMPORARY, CONNECT ON DATABASE "plf_dev" TO PUBLIC;

GRANT ALL ON DATABASE "plf_dev" TO plf;
BEGIN;

CREATE TABLE appsync_umbrella (
    id INT NOT NULL,
    name VARCHAR NOT NULL UNIQUE,
    orgsync_id INT NOT NULL UNIQUE,
    PRIMARY KEY(id)
);

CREATE SEQUENCE appsync_umbrella_seq;

INSERT INTO appsync_umbrella (id, name, orgsync_id) VALUES (nextval('appsync_umbrella_seq'), 'CSIL', 87895);
INSERT INTO appsync_umbrella (id, name, orgsync_id) VALUES (nextval('appsync_umbrella_seq'), 'Orientation', 112682);
INSERT INTO appsync_umbrella (id, name, orgsync_id) VALUES (nextval('appsync_umbrella_seq'), 'Student Development', 101226);
INSERT INTO appsync_umbrella (id, name, orgsync_id) VALUES (nextval('appsync_umbrella_seq'), 'University Housing', 107843);

CREATE TABLE appsync_umbrella_admin (
    id INT NOT NULL,
    username VARCHAR NOT NULL,
    umbrella_id INT REFERENCES appsync_umbrella(orgsync_id),
    PRIMARY KEY(id)
);

CREATE SEQUENCE appsync_umbrella_admin_seq;

CREATE TABLE appsync_portal (
    orgsync_id INT NOT NULL UNIQUE,
    name VARCHAR NOT NULL UNIQUE,
    umbrella_id INT REFERENCES appsync_umbrella(orgsync_id),
    PRIMARY KEY(orgsync_id)
);

CREATE TABLE appsync_log_entry (
    id INT NOT NULL,
    description VARCHAR,
    username VARCHAR NOT NULL,
    occurred_on INT NOT NULL,
    PRIMARY KEY(id)
);

CREATE SEQUENCE appsync_log_entry_seq;

CREATE TABLE appsync_settings (
    id INT NOT NULL,
    setting VARCHAR NOT NULL UNIQUE,
    value VARCHAR NOT NULL,
    PRIMARY KEY(id)
);

CREATE SEQUENCE appsync_settings_seq;

COMMIT;

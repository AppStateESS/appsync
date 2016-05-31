--
--

START TRANSACTION;

-- Insert the original umbrellas into the appsync_umbrella table.

INSERT INTO appsync_umbrella (id, name, orgsync_id) VALUES (nextval('appsync_umbrella_seq'), 'CSIL', 87895);
INSERT INTO appsync_umbrella (id, name, orgsync_id) VALUES (nextval('appsync_umbrella_seq'), 'Orientation', 112682);
INSERT INTO appsync_umbrella (id, name, orgsync_id) VALUES (nextval('appsync_umbrella_seq'), 'Student Development', 101226);
INSERT INTO appsync_umbrella (id, name, orgsync_id) VALUES (nextval('appsync_umbrella_seq'), 'University Housing', 107843);



COMMIT;

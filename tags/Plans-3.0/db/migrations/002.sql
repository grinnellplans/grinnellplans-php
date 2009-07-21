INSERT INTO plans SELECT userid, plan FROM accounts;

ALTER TABLE accounts DROP COLUMN plan;

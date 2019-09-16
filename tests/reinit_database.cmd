@echo off
REM reinitialize the database
mysql -uroot -p%MYSQL_ROOT_PASSWORD% < clear_db.sql
mysql -uroot -p%MYSQL_ROOT_PASSWORD% < db.sql
mysql -uxingyu -pxingyu -Dxingyu < tests\db_init.sql

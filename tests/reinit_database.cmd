@echo off
REM reinitialize the database
mysql -uroot -p%MYSQL_ROOT_PASSWORD% < clear_db.sql
mysql -uroot -p%MYSQL_ROOT_PASSWORD% < db.sql
if [%1] == [] (
    mysql -uxingyu -pxingyu -Dxingyu < tests\db_init.sql
) else (
    mysql -uxingyu -pxingyu -Dxingyu < %1
)

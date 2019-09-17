# Supported Developing machine
It has been tested on the following Machines

| OS      | PHP | MySQL |
|---------|-----|-------|
| Windows | 7.2 | 8     |
| MacOS   | 7.2 | 8     |
| Ubuntu  | 7.2 | 5.7   |
| CentOS  | 7.2 | 5.5   |

For lower version of mysql, there may exist authentication issues. In such cases, you can try to use `db_maria.sql` to initialize the database. The difference lies at `set old_passwords=0`.

The developing step is simple. First you initialize the database using `db.sql` and `db_init.sql`. Then you start the php developing
server `php -S localhost:8080`. Next open another console and use the repository root as your working directory. Set environment variable `XINGYU_ROOT=http://localhost:8080/`. Finally, run `phpunit tests` to run all tests.

# Deployment machine
Currently, I use Apache on Ubuntu Server to deploy the backend codes.

# Frontend development
Wechat developer tool is essential. This platform is restrictive and I am considering abandon this frontend in the future.
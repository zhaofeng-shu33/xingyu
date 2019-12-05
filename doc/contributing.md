# Supported Developing machine
It has been tested on the following Machines

| OS      | PHP | MySQL |
|---------|-----|-------|
| Windows | 7.2 | 8     |
| MacOS   | 7.2 | 8     |
| Ubuntu  | 7.2 | 5.7   |
| CentOS  | 7.2 | 5.5   |

For lower version of mysql, there may exist authentication issues. In such cases, you can try to use `db_maria.sql` to initialize the database. The difference lies at `set old_passwords=0`.

The developing step is simple. First you initialize the database using `db.sql` and `db_init.sql`.
To automate this process we provide the script `tests/reinit_database.sh` (`test\reinit_database.cmd` for Windows). Just run `bash tests/reinit_database.sh` from project root (`tests\reinit_database.cmd` for cmd prompt).

Then you start the php developing
server `php -S localhost:8080` at `backend` directory. Next open another console and use the repository root as your working directory. Set environment variable `XINGYU_ROOT=http://localhost:8080/`. Finally, run `phpunit tests` to run all tests.


You can join the [slack](https://lab2c.slack.com/join/shared_invite/enQtODQyMTY4OTcyNTMwLWRkOTlkYmM2MWI3NGYzOWMwYTRkYzEzMTBjNjcxMWMxNTMxZjg2N2U1YzE5ZjI4YTE3ZTQ2ZWU2YzEyODNmMmU) channel `xingyu` to communicate.

## Install dependencies

```shell
composer install # at backend directory
```
For Chinese users, configure the mirror first.
See [aliyun](https://mirrors.aliyun.com/composer/).

## Deployment machine
Currently, I use Apache on Ubuntu Server to deploy the backend codes.

## Group Naming Convention
Each group should contains the target organization name. For example, if your volunteer organization is to serve old people in Sunset House every Friday. 
Then your group name should be called 'Friday Sunset House'.

## Backend with wechat integration

Change the `appid` and `appsecret` in `backend/mysql.php` to your test appid and appsecret. This step is required if you need frontend login.

## Front end development
You need wechat developer tool as prerequisite and of course you need a wechat account.
Make sure backend settings are finished before you start front end development.

* Copy `config.json.sample` to `config.json` in the same directory.

* Change the `host` variable in `miniprogram/config.json` to your localhost url.

* (required for login feature) Change the `appid` in `miniprogram/config.json` to your test appid.

* (required for login feature) Add the group privilege to one test student. For example:
   ```shell
    mysql -uxingyu -pxingyu -Dxingyu -e "update xingyu_student set wechat_nickname='your_wechat_displayname' where name='张三';" # 
    # Note: nickname != wechat_unique_id
   ```
   

After changing the default value in `config.json`, run `php generate-config.php` within the same directory to generate `project.config.json` and `config.js`.

For how to obtain test account, see

* [official guide](https://developers.weixin.qq.com/miniprogram/dev/devtools/sandbox.html)
* [solve blank account](https://developers.weixin.qq.com/community/develop/doc/000886459dc1b8be37a8c677b51000)

I think this platform is restrictive and I am considering abandon this front end in the future.


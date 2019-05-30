# 接口文档

## 基本约定

1. 返回值格式是JSON {err:0, msg:'', result: Object}，0为没有错误，操作成功，非0有msg详细信息。
result 的键值和具体请求有关，如果 result 有一个 key 是 `student_list`，那么有：
```JavaScript
result['student_list'] = [[name, school],[name_2, school_2]]
```
2. 学校名称与代号对照表

| school | short_code |
|--------|------------|
| 哈工大 | hit        |
| 深大   | szu        |
| 清华   | thu        |
| 北大   | pku        |
| 南科大 | sust       |

3. `$root` 表示应用部署的根URL。

4. semester 这个参数为1表示2018年秋季学期，为2表示2019年春季学期，在 GET 和 POST 请求中，不提供这个参数默认为2。

5. 小组名称
## Web API
1. 添加1个学生到2019年春季学期的流动组
```shell
curl -X POST $root/xingyu/add_student_flow.php -H "Content-Type: application/json" -d '{"student_name":"张三","student_school":"hit"}'
```
必有参数为 student_name(string), student_school(enum)，school只能从五个学校的代号里选取。
返回结果 err = 3 时表示该学生已经存在。

2. 根据姓名的前几个汉字获取流动组学生列表不分页
```shell
curl -X GET $root/xingyu/get_student_list.php?student_name_prefix=张&semester=2
```
返回结果为 `result['student_list']`

3. 获取某个活动的固定志愿者
```shell
curl -X GET $root/xingyu/get_fixed_student.php?student_group=[group_name]&semester=2
```
返回结果为 `result['student_list']`

4. 批量添加某个活动参与的全部学生
```shell
curl -X POST $root/xingyu/add_activity.php -H "Content-Type: application/json" -d '{"week":3,"name":"周二下午", "student_list":["张三"]}'
```
必有参数为 深大的周数(week)，int；[3-18] 和小组名称(name)(string)；以及学生名字列表(student_list:['name_1','name_2'])
可选参数为semester, semester=1表示2018年秋季学期，=2表示2019年春季学期，不提供这个参数默认为2。

返回结果 err = 5 时表示该活动已经存在。

5. 获取小组列表
GET 方法到 /xingyu/get_group_list.php&semester=2
semester=1表示2018年秋季学期，=2表示2019年春季学期，不提供这个参数默认为2。
返回结果 ： result['group_list'] = [[group_name_1],[group_name_2]]

6. 获取某个活动的全部志愿者（用于提交成功后查看结果）
GET 方法到 /xingyu/get_all_student.php?student_group=[group_name]&week=[week_num]
如果student_group 和 week参数不提供，需提供 name, location 和time 三个参数，用于查询特色活动，name 和 location 都是字符串，其中 time 的格式是 '2019-03-05'这种。
返回结果 ： result['student_list'] = [[name, school],[name_2, school_2]]

7. 补录某个活动参与的学生
POST 方法到 /xingyu/append_activity.php
(JSON格式数据）必有参数为 深大的周数(week)，int；[3-18] 和小组名称(name)(string)；以及学生名字列表(student_list:['name_1','name_2'])，其中学生名字的列表为要
补录的同学，允许同学之前已经存在。
可选参数为semester, semester=1表示2018年秋季学期，=2表示2019年春季学期，不提供这个参数默认为2。

返回结果 err = 5 时表示该活动不存在。


8. 更改1个流动组学生的学校信息
POST 方法到 /xingyu/modify_student_flow.php
(JSON格式数据）必有参数为 student_name(string), student_school(enum)，school只能从五个学校的代号里选取。

9. 获取所有的特色活动的信息
GET 方法到 /xingyu/get_special_activity.php
没有参数
返回的结果 result['special_activity_list'] 是一个array, 每个array的 item 长度为3,依次为 name, location, time。

10. 删除某个活动的学生
POST 方法到 /xingyu/remote_activity_student.php
(JSON格式数据）必有参数为 深大的周数(week)，int；[3-18] 和小组名称(name)(string)；以及学生名字列表(student_list:['name_1','name_2'])，其中学生名字的列表为要
删除的同学，允许同学之前没参加过该活动，此时也不会报错。
返回结果 err = 5 时表示该活动不存在。

11. 更改志愿者的组别信息
POST 方法到 /xingyu/modify_student_group.php?action=add
如果action=add 是添加一个新的组别，如果action=delete 是删除这个组别（目前没有约束）。
(JSON格式数据）必有参数为 student_name(string), group_id(int)，group_id > 0.

12. 获取五校统计信息
GET 方法到 /xingyu/get_statistics.php
返回 JSON格式数据 [{'school':'hit','total_student':23,'total_count':45}, ...]
其中 total_student 表示该校人数信息， total_count 表示该校人次信息。

13. 删除流动志愿者
POST 方法到 /xingyu/delete_student_flow.php
(JSON格式数据）必有参数为 student_name(string), student_school(enum)，school只能从五个学校的代号里选取。
注意：如果这个志愿者有参加过活动，则必须先通过其他的接口取消他参加的活动才能删除。

14. 获取各校志愿者本学期的统计信息
GET 方法到 /xingyu/download_summary.php?student_school, school只能从五个学校的代号里选取
返回 excel 格式数据，如果学校未识别，返回的 body 为空。

## 登录相关

登录相关：第一次登录需同时完成1和2两步，假如只完成一步也算第一次登录失败。后面登录只需完成第1步。
1. 凭证校验 GET 方法到 /xingyu/openid.php?code=abc
其中 code 是从微信开发者服务器上获得的；
返回 JSON 格式的数据，有 {"err":1,"msg":"invalid code","result":""} 或者
{"err":0,"msg":"","result":{"openid":"id","session_key":"key"}} 。

2. 关联志愿者信息与openid。POST 方法到 /xingyu/openid.php
提交JSON格式的数据 {"openid":"id", "nickname":"nickname"}
返回 JSON 格式的数据，{"err":0,"msg":"","result":""}，其中 err 为 0 表示操作成功。

## 权限相关
数据库中 student 表 里面 openid 非空者为管理员，有相应POST接口的权限。
所有 POST 请求 openid 是必有参数，可以置空或不填，但这种情况下肯定无法进行数据库的写操作。
如果在POST请求中返回的错误码 err = 44，说明当前用户没有权限执行这个操作。
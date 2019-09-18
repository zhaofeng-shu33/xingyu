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

4. semester 这个参数为1表示2018年秋季学期，为2表示2019年春季学期，在 GET 和 POST 请求中，不提供这个参数默认为当前
学期。

5. 小组名称（2019年春季学期）：周一下午、周二下午、周四下午金色年华、周四下午童伴时光、周五下午单周、周四下午双周

## Web API
1. 添加1个学生到当前学期的流动组
```shell
curl -X POST $root/xingyu/add_student_flow.php -H "Content-Type: application/json" -d '{"student_name":"张三","student_school":"hit"}'
```
必有参数为 student_name(string), student_school(enum)，school只能从五个学校的代号里选取。
如果学生不存在，自动创建该学生。
返回结果 err = 3 时表示该学生已经有一个组，无法添加。

2. 根据姓名的前几个汉字获取流动组学生列表不分页
```shell
curl -X GET "$root/xingyu/get_student_list.php?student_name_prefix=张&semester=2"
```
semester为可选参数，不提供默认为当前学期。
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
可选参数为 semester.
返回结果 err = 5 时表示该活动已经存在。

5. 获取某学期小组列表
```shell
curl -X GET $root/xingyu/get_group_list.php&semester=2&all=0
```
all是必有参数，如果all=1，忽略semester参数，返回结果为 `result['group_list'] = [[id_1, group_name_1, semester_1],[id_2, group_name_2, semester_2]]`
否则，根据semester参数，返回结果为 `result['group_list'] = [[group_name_1],[group_name_2]]`

6. 获取某个活动的全部志愿者（用于提交成功后查看结果）
```shell
curl -X GET "$root/xingyu/get_all_student.php?student_group=周二下午&week=4&semester=2" # 常规活动请求格式
curl -X GET "$root/xingyu/get_all_student.php?name=前期体验活动&location=金色年华&time=2019-03-05" # 常规和扩展活动请求格式
```
返回结果为 `result['student_list']`

7. 补录某个活动参与的学生
```shell
curl -X POST $root/xingyu/append_activity.php -H "Content-Type: application/json" -d '{"week":4,"name":"周二下午",student_list":["张三"]}'
```
必有参数为 深大的周数(week)，int；[3-18] 和小组名称(name)(string)；以及学生名字列表(student_list:['name_1','name_2'])，其中学生名字的列表为要
补录的同学，允许同学之前已经存在。
可选参数为 semester.

返回结果 err = 5 时表示该活动不存在。

8. 更改1个流动组学生的学校信息
```shell
curl -X POST $root/xingyu/modify_student_flow.php -H "Content-Type: application/json" -d '{"student_name":"张三","student_school":"hit"}'
```
必有参数为 student_name(string), student_school(enum)，school 只能从五个学校的代号里选取。

9. 获取所有的特色活动的信息
```shell
curl -X GET $root/xingyu/get_special_activity.php
```
返回的结果 result['special_activity_list'] 是一个 array, 每个 array 的 item 长度为3, 依次为 name, location, time。

10. 删除某个活动参加的学生信息
```shell
curl -X POST $root/xingyu/remove_activity_student.php -H "Content-Type: application/json" -d '{"week":4,"name":"周二下午",student_list":["张三"]}'
```
必有参数为 深大的周数(week)，int；[3-18] 和小组名称(name)(string)；以及学生名字列表(student_list:['name_1','name_2'])，其中学生名字的列表为要
删除的同学，允许同学之前没参加过该活动，此时也不会报错。可选参数为 semester。如果该活动没有学生参加，该活动也被删除。
返回结果 err = 5 时表示该活动不存在。

11. 更改志愿者的组别信息
```shell
curl -X POST $root/xingyu/modify_student_group.php?action=add -H "Content-Type: application/json" -d '{"student_name":"张三","group_id":2}'
```
如果 `action=add` 是添加一个新的组别，如果 `action=delete` 是删除这个组别（目前没有约束）。
旧 API：
必有参数为 student_name(string), group_id(int)，要求 group_id > 0.
`action=delete`情况下，返回结果 err = 5 时表示该学生不属于 id 为传递的 group_id 的组。
新 API:
必有参数为 student_name(string), group_name(string)，可选参数为semester(int)
`action=delete`情况下，返回结果 err = 5 时表示该学生不属于 semester + group_name 对应的组。

目前新旧 API 均可使用。

12. 获取五校（或者参加童伴时光机构）统计信息
```shell
curl -X GET $root/xingyu/get_statistics.php
```
返回 JSON格式数据 [{'school':'hit','total_student':23,'total_count':45}, ...]
其中 total_student 表示该校人数信息， total_count 表示该校人次信息。

13. 删除流动志愿者
```shell
curl -X POST $root/xingyu/delete_student_flow.php -H "Content-Type: application/json" -d '{"student_name":"张三","student_school":"hit"}'
```
必有参数为 student_name(string), student_school(enum)，school只能从五个学校的代号里选取。
返回结果 err = 4 时表示该学生参与过活动，无法删除。
注意：如果这个志愿者有参加过活动，则必须先通过其他的接口取消他参加的活动才能删除。

14. 获取各校志愿者本学期的统计信息或者参加童伴时光机构志愿活动的志愿者统计信息
```shell
curl -X GET $root/xingyu/download_summary.php?student_school=hit
```
返回 excel 格式数据，如果学校不在代号列表里面，返回的 body 为空。

15. 数据可视化接口
```shell
curl -X GET $root/xingyu/plot.php?type=bar
curl -X GET $root/xingyu/plot.php?type=line&semester=2
```
bar plot 绘制所有学期的次数-人数统计图， line plot 绘制某学期人数随周数变化图。 返回 png 格式的图片。

## 登录相关

登录相关：第一次登录需同时完成1和2两步，假如只完成一步也算第一次登录失败。后面登录只需完成第1步。
1. 凭证校验
```shell
curl -X GET $root/xingyu/openid.php?code=abc
```
其中 code 是通过 `wx.login` 从微信开发者服务器上获得的；
返回 JSON 格式的数据：
如果自有服务器请求微信开发者服务器失败，返回 {"err":1,"msg":"invalid code","result":""}；
如果请求成功，返回 {"err":0,"msg":"","result":{"openid":"id","session_key":"key"}}

2. 关联志愿者信息与openid。
```shell
curl -X POST $root/xingyu/openid.php -H "Content-Type: application/json" -d '{"openid":"abc","nickname":"张小三"}'
```
返回 JSON 格式的数据，{"err":0,"msg":"","result":""}，其中 err 为 0 表示操作成功。

## 权限相关
数据库中 student 表 里面 openid 非空者为管理员，有相应 POST 接口的权限。
所有 POST 请求 openid 是必有参数，可以置空或不填，但这种情况下肯定无法进行数据库的写操作。
如果在POST请求中返回的错误码 err = 44，说明当前用户没有权限执行这个操作。
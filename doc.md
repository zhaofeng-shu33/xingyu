# 接口文档
返回值格式是JSON {err:0, msg:'', result: Object}，0为没有错误，非0有msg详细信息。

1. 添加1个学生到2019年春季学期的流动组
POST 方法到 /xingyu/add_student_flow.php
(JSON格式数据）必有参数为 student_name(string), student_school(enum)，school只能从五个学校的代号里选取。
返回结果 err = 3 时表示该学生已经存在。

2. 根据姓名的前几个汉字获取流动组学生列表不分页
GET 方法到 /xingyu/get_student_list.php?student_name_prefix=[name]&semester=2
semester=1表示2018年秋季学期，=2表示2019年春季学期，不提供这个参数默认为2。
返回结果 ： result['student_list'] = [[name, school],[name_2, school_2]]

3. 获取某个活动的固定志愿者
GET 方法到 /xingyu/get_fixed_student.php?student_group=[group_name]&semester=2
semester=1表示2018年秋季学期，=2表示2019年春季学期，不提供这个参数默认为2。
返回结果 ： result['student_list'] = [[name, school],[name_2, school_2]]

4. 批量添加某个活动参与的全部学生
POST 方法到 /xingyu/add_activity.php
(JSON格式数据）必有参数为 深大的周数(week)，int；[3-18] 和小组名称(name)(string)；以及学生名字列表(student_list:['name_1','name_2'])
返回结果 err = 5 时表示该活动已经存在。

5. 获取小组列表
GET 方法到 /xingyu/get_group_list.php
无参数
返回结果 ： result['group_list'] = [[group_name_1],[group_name_2]]

6. 获取某个活动的全部志愿者（用于提交成功后查看结果）
GET 方法到 /xingyu/get_all_student.php?student_group=[group_name]&week=[week_num]
如果student_group 和 week参数不提供，需提供 name, location 和time 三个参数，用于查询特色活动，name 和 location 都是字符串，其中 time 的格式是 '2019-03-05'这种。
返回结果 ： result['student_list'] = [[name, school],[name_2, school_2]]

7. 补录某个活动参与的学生
POST 方法到 /xingyu/append_activity.php
(JSON格式数据）必有参数为 深大的周数(week)，int；[3-18] 和小组名称(name)(string)；以及学生名字列表(student_list:['name_1','name_2'])，其中学生名字的列表为要
补录的同学，允许同学之前已经存在。
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

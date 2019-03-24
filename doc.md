# 接口文档
返回值格式是 {err:0, msg:'', result: Object}，0为没有错误，非0有msg详细信息。

1. 添加1个学生到流动组
POST 方法到 /xingyu/add_student_flow.php
(JSON格式数据）必有参数为 student_name(string), student_school(enum)，school只能从五个学校的代号里选取。

2. 根据姓名的前几个汉字获取流动组学生列表不分页
GET 方法到 /xingyu/get_student_list.php?student_name_prefix=[name]

3. 获取某个活动的固定志愿者
GET 方法到 /xingyu/get_fixed_student.php?student_group=[group_name]

4. 批量添加某个活动参与的流动学生
POST 方法到 /xingyu/add_activity.php
(JSON格式数据）必有参数为 深大的周数(week)，int；[3-18] 和小组名称(name)(string)；以及学生名字列表(student_list:['name_1','name_2'])

5. 获取小组列表
GET 方法到 /xingyu/get_group_list.php
无参数


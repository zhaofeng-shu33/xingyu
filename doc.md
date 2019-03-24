# 接口文档
返回值格式是 {code:0, msg:'', result: Object}，0为没有错误，非0有msg详细信息。

1. 添加1个学生到流动组
POST 方法到 /xingyu/add_student_flow.php
必有参数为 student_name(string), student_school(enum)，school只能从五个学校的代号里选取。

2. 根据姓名的前几个汉字获取流动组学生列表不分页

3. 获取某个活动的固定志愿者

4. 批量添加某个活动参与的流动学生

5. 标注某个固定组的学生本次活动没有参加



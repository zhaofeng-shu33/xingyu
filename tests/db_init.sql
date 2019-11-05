/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
-- 当前学期是2019秋季学期
insert into xingyu_student (name, school, wechat_openid) values ('赵丰', 'thu', 'abc');
insert into xingyu_student (name, school, wechat_openid) values ('林粤', 'szu', '');
insert into xingyu_student (name, school, wechat_openid) values ('张三', 'szu', '');
insert into xingyu_student (name, school, wechat_openid) values ('张四', 'hit', '');

insert into xingyu_semester (name, start_time) values ('2018秋季学期', '2018-09-03');
insert into xingyu_semester (name, start_time) values ('2019春季学期', '2019-03-04');
insert into xingyu_semester (name, start_time) values ('2019秋季学期', '2019-09-07');

insert into xingyu_group (name, semester_id) values ('流动', 3);
insert into xingyu_group (name, semester_id) values ('周一下午金色年华', 3);
insert into xingyu_group (name, semester_id) values ('周一下午童伴时光', 3);
insert into xingyu_group (name, semester_id) values ('周二下午金色年华', 1);

-- 赵丰在2019秋季学期周一下午金色年华组
insert into xingyu_student_group (student_id, group_id) values (1, 2);
-- 林粤在2019秋季学期周一下午金色年华组
insert into xingyu_student_group (student_id, group_id) values (2, 2);
-- 张三、张四在2019秋季学期流动组
insert into xingyu_student_group (student_id, group_id) values (3, 1);
insert into xingyu_student_group (student_id, group_id) values (4, 1);



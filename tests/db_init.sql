insert into xingyu_student (name, school, wechat_openid) values ('赵丰', 'thu', 'abc');
insert into xingyu_student (name, school, wechat_openid) values ('林粤', 'szu', '');

insert into xingyu_semester (name, start_time) values ('2018秋季学期', '2018-09-03');
insert into xingyu_semester (name, start_time) values ('2019春季学期', '2019-03-04');

insert into xingyu_group (name, semester_id) values ('流动', 2);
insert into xingyu_group (name, semester_id) values ('周一下午', 2);
insert into xingyu_group (name, semester_id) values ('周二下午', 2);
insert into xingyu_group (name, semester_id) values ('周二下午', 1);

insert into xingyu_student_group (student_id, group_id) values (1, 2);
insert into xingyu_student_group (student_id, group_id) values (2, 2);



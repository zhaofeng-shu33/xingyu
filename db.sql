-- mysql
create database if not exists xingyu charset utf8mb4;
create user if not exists 'xingyu'@'localhost' identified with mysql_native_password by 'xingyu';
grant all privileges on xingyu.* to 'xingyu'@'localhost';
flush privileges;
use xingyu;
create table xingyu_semester(
    id int primary key auto_increment,
    name varchar(10),
    start_time date not null
);
create table xingyu_group(
    id int primary key auto_increment,
    name varchar(12) not null default '流动',
    semester_id int not null,
    constraint semester_constraint foreign key (semester_id) REFERENCES xingyu_semester(id) on delete cascade
);
create table xingyu_student(
    id int primary key auto_increment,
    name varchar(5) not null,
	wechat_nickname varchar(20),
	wechat_openid varchar(30),
    school enum('thu', 'pku','hit','sust','szu') not null
);
create table xingyu_activity(
    id int primary key auto_increment,
    name varchar(20),
    location varchar(10) not null,
    institution varchar(4),
    time date not null,
	special boolean default false,
	group_id int default 1,
	constraint group_constraint foreign key(group_id) REFERENCES xingyu_group (id) on delete cascade
);
create table xingyu_student_activity(
    id int primary key auto_increment,
    student_id int not null,
    activity_id int not null,
    constraint student_constraint foreign key (student_id) REFERENCES xingyu_student (id) on delete cascade,
    constraint activity_constraint foreign key (activity_id) REFERENCES xingyu_activity (id) on delete cascade
);
create table xingyu_student_group(
    id int primary key auto_increment,
    student_id int not null,
    group_id int not null,
    constraint semester_group_constraint foreign key (group_id) REFERENCES xingyu_group (id) on delete cascade,
    constraint semester_student_constraint foreign key (student_id) REFERENCES xingyu_student (id) on delete cascade
);

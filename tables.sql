# 用户表
create table if not exists user (
	userId int unsigned not null auto_increment, # 用户ID
	userName varchar(32) not null, # 用户名
	userPass char(32) not null, # 用户密码
	registerTime date not null, # 注册时间 
	isAdmin bool default false, # 是否为管理员
	limitLevel tinyint unsigned default 0, # 权限等级
	primary key (userId),
	unique key (userName)
)
 insert into user values(10086, 'admin', md5('123'), '2017-10-12', true, 9);
# 文件操作记录
create table if not exists modifyRecord (
	userId int unsigned not null, # 用户ID
	fileId int unsigned not null, # 文件ID
	modifyDate date not null, # 修改时间 
	modifyMode enum('add','update','delete') not null, # 修改类型 
	foreign key (userId) references user(userId) on delete cascade on update cascade,
	foreign key (fileId) references file(fileId) on delete cascade on update cascade
)

# 文件表
create table if not exists file (
	fileId int unsigned not null auto_increment, # 文件ID
	userId int unsigned not null, # 用户ID
	filePath varchar(64) not null, # 文件名
	addTime varchar(32) not null, # 添加时间 
	fileName varchar(32) not null, # 文件名称
	limitRead tinyint unsigned default 0, # 权限 读
	limitWrite tinyint unsigned default 0, # 权限 写
	limitDownload tinyint unsigned default 0, # 权限 下载
	primary key (fileId),
	foreign key (userId) references user(userId) on delete cascade on update cascade
)
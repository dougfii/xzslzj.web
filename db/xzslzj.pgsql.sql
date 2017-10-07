-- #####################################################
-- IMPORTENT
-- #####################################################
-- select * from t_user order by random() limit 5; 随机取记录

-- CREATE USER user2 WITH PASSWORD 'xxxx';
-- CREATE DATABASE db2;
-- GRANT ALL PRIVILEGES ON DATABASE db2 TO user2;

-- DROP DATABASE IF EXISTS linkxun;
-- CREATE DATABASE linkxun;

-- C:\Program Files\PostgreSQL\9.2\bin>psql -d greentown -U postgres -f "C:\Program Files\PostgreSQL\9.2\share\extension\uuid-ossp--1.0.sql"
-- 高版本用 CREATE EXTENSION "uuid-ossp"; 


-- #####################################################
-- STRUCT PART
-- #####################################################
-- -----------------------------------------------------
-- Language plpgsql;
-- -----------------------------------------------------
--DROP LANGUAGE IF EXISTS plpgsql CASCADE;
--CREATE LANGUAGE plpgsql;

--######################################################
-- 基础结构表
--######################################################

-- -----------------------------------------------------
-- Table t_group			组织单位
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_group CASCADE;
CREATE SEQUENCE seq_group;
DROP TABLE IF EXISTS t_group CASCADE;
CREATE TABLE t_group
(
	id				BIGINT UNIQUE DEFAULT NEXTVAL('seq_group') NOT NULL ,
	fid				BIGINT NOT NULL DEFAULT 0 ,
	name			VARCHAR(200) NOT NULL ,
	company			VARCHAR(200) NOT NULL , -- 主管单位名称
	permission		TEXT DEFAULT '' ,
	act				BOOLEAN NOT NULL DEFAULT true ,
	del				BOOLEAN NOT NULL DEFAULT false ,
	time			TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_org				组织结构
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_org CASCADE;
CREATE SEQUENCE seq_org;
DROP TABLE IF EXISTS t_org CASCADE;
CREATE TABLE t_org
(
	id				BIGINT UNIQUE DEFAULT NEXTVAL('seq_org') NOT NULL ,
	gid				BIGINT NOT NULL DEFAULT 0 ,
	fid				BIGINT NOT NULL DEFAULT 0 ,
	name			VARCHAR(200) NOT NULL ,
	permission		TEXT DEFAULT '' ,
	act				BOOLEAN NOT NULL DEFAULT true ,
	del				BOOLEAN NOT NULL DEFAULT false ,
	time			TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_user				用户表
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_user CASCADE;
CREATE SEQUENCE seq_user;
DROP TABLE IF EXISTS t_user CASCADE;
CREATE TABLE t_user
(
	id				BIGINT UNIQUE DEFAULT NEXTVAL('seq_user') NOT NULL ,	
	gid				BIGINT NOT NULL DEFAULT 0 ,	
	oid				BIGINT NOT NULL DEFAULT 0 ,
	username		VARCHAR(200) NOT NULL ,				-- 登录名
	password		CHAR(32) NOT NULL ,					-- 密码
	name			VARCHAR(200) NOT NULL ,				-- 姓名
	serial			VARCHAR(200) DEFAULT '' ,			-- 编号
	sex				BOOLEAN NOT NULL DEFAULT true ,		-- 性别 true:男 false:女
	mobile			VARCHAR(20) DEFAULT '' ,			-- 手机
	imei			VARCHAR(20) DEFAULT '' ,			-- 手机识别码
	email			VARCHAR(50) DEFAULT '' ,			-- 邮箱
	lead			BOOLEAN NOT NULL DEFAULT false ,	-- 部门负责人
	admin			BOOLEAN NOT NULL DEFAULT false ,	-- 超级管理权限
	permission		TEXT DEFAULT '' ,					-- 权限
	act				BOOLEAN NOT NULL DEFAULT true ,
	del				BOOLEAN NOT NULL DEFAULT false ,
	time			TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);


-- -----------------------------------------------------
-- Table t_project				项目
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_project CASCADE;
CREATE SEQUENCE seq_project;
DROP TABLE IF EXISTS t_project CASCADE;
CREATE TABLE t_project
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_project') NOT NULL ,
	name						VARCHAR(200) NOT NULL ,
	pass						VARCHAR(50) NOT NULL ,
	gid							BIGINT NOT NULL DEFAULT 0 , -- group id
	company						VARCHAR(200) DEFAULT '' ,
	contacts					VARCHAR(50) NOT NULL ,
	mobile						VARCHAR(50) DEFAULT '' ,
	email						VARCHAR(50) DEFAULT '' ,

	n0							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n1							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n2							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n3							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n4							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n5							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n51							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n52							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n6							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n61							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n62							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n63							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n64							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n7							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n71							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n72							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n73							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n74							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n75							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n76							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n8							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n9							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n91							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n92							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n921						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	n922						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	n923						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	n924						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	n925						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	n10							INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	n9999						INTEGER NOT NULL DEFAULT 0 , -- 节点开放
	
	s0							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s1							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s2							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s3							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s4							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s5							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s51							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s52							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s6							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s61							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s62							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s63							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s64							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s7							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s71							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s72							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s73							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s74							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s75							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s76							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s8							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s9							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s91							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s92							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s921						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s922						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s923						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s924						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s925						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s10							INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	s9999						INTEGER NOT NULL DEFAULT 0 , -- 节点状态
	
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后登录
	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow1					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow1 CASCADE;
CREATE SEQUENCE seq_flow1;
DROP TABLE IF EXISTS t_flow1 CASCADE;
CREATE TABLE t_flow1
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow1') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者, 3:核定人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容, 3:核定意见
	keywords					TEXT DEFAULT '' , -- 关键词
	attachments					TEXT DEFAULT '' , -- 附件列表

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow2					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow2 CASCADE;
CREATE SEQUENCE seq_flow2;
DROP TABLE IF EXISTS t_flow2 CASCADE;
CREATE TABLE t_flow2
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow2') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者, 3:核定人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容, 3:核定意见
	keywords					TEXT DEFAULT '' , -- 关键词
	attachments					TEXT DEFAULT '' , -- 附件列表

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow3					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow3 CASCADE;
CREATE SEQUENCE seq_flow3;
DROP TABLE IF EXISTS t_flow3 CASCADE;
CREATE TABLE t_flow3
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow3') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id

	name						TEXT DEFAULT '' , -- 工程名称
	
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者, 3:核定人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容, 3:核定意见
	attachments					TEXT DEFAULT '' , -- 附件列表
	
	comp						TEXT DEFAULT '' , -- 3:施工单位
	date_ping					TEXT DEFAULT '' , -- 3:评定日期
	items						TEXT DEFAULT '' , -- 3:评分项目
	totals						TEXT DEFAULT '' , -- 3:评分项目
	m11							TEXT DEFAULT '' , -- 3:评定组成员
	m12							TEXT DEFAULT '' , -- 3:评定组成员
	m13							TEXT DEFAULT '' , -- 3:评定组成员
	m21							TEXT DEFAULT '' , -- 3:评定组成员
	m22							TEXT DEFAULT '' , -- 3:评定组成员
	m23							TEXT DEFAULT '' , -- 3:评定组成员
	m31							TEXT DEFAULT '' , -- 3:评定组成员
	m32							TEXT DEFAULT '' , -- 3:评定组成员
	m33							TEXT DEFAULT '' , -- 3:评定组成员
	m41							TEXT DEFAULT '' , -- 3:评定组成员
	m42							TEXT DEFAULT '' , -- 3:评定组成员
	m43							TEXT DEFAULT '' , -- 3:评定组成员
	m51							TEXT DEFAULT '' , -- 3:评定组成员
	m52							TEXT DEFAULT '' , -- 3:评定组成员
	m53							TEXT DEFAULT '' , -- 3:评定组成员
	m61							TEXT DEFAULT '' , -- 3:评定组成员
	m62							TEXT DEFAULT '' , -- 3:评定组成员
	m63							TEXT DEFAULT '' , -- 3:评定组成员

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow4					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow4 CASCADE;
CREATE SEQUENCE seq_flow4;
DROP TABLE IF EXISTS t_flow4 CASCADE;
CREATE TABLE t_flow4
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow4') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	name						TEXT DEFAULT '' , -- 工程名称
	
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容
	keywords					TEXT DEFAULT '' , -- 关键词
	attachments					TEXT DEFAULT '' , -- 附件列表
	
	code 						TEXT DEFAULT '' , --
	personals					TEXT DEFAULT '' , --
	progress					TEXT DEFAULT '' , --
	writer						TEXT DEFAULT '' , --
	writer_date					TEXT DEFAULT '' , --
	signer_date					TEXT DEFAULT '' , --

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow51					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow51 CASCADE;
CREATE SEQUENCE seq_flow51;
DROP TABLE IF EXISTS t_flow51 CASCADE;
CREATE TABLE t_flow51
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow51') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	v1c							TEXT DEFAULT '' , --
	v1n							TEXT DEFAULT '' , --
	v1d							TEXT DEFAULT '' , --
	v2c							TEXT DEFAULT '' , --
	v2n							TEXT DEFAULT '' , --
	v2d							TEXT DEFAULT '' , --
	v3c							TEXT DEFAULT '' , --
	v3n							TEXT DEFAULT '' , --
	v3d							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow52					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow52 CASCADE;
CREATE SEQUENCE seq_flow52;
DROP TABLE IF EXISTS t_flow52 CASCADE;
CREATE TABLE t_flow52
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow52') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	v1c							TEXT DEFAULT '' , --
	v1n							TEXT DEFAULT '' , --
	v1d							TEXT DEFAULT '' , --
	v2c							TEXT DEFAULT '' , --
	v2n							TEXT DEFAULT '' , --
	v2d							TEXT DEFAULT '' , --
	v3c							TEXT DEFAULT '' , --
	v3n							TEXT DEFAULT '' , --
	v3d							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow61					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow61 CASCADE;
CREATE SEQUENCE seq_flow61;
DROP TABLE IF EXISTS t_flow61 CASCADE;
CREATE TABLE t_flow61
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow61') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	v1c							TEXT DEFAULT '' , --
	v1n							TEXT DEFAULT '' , --
	v1d							TEXT DEFAULT '' , --
	v2c							TEXT DEFAULT '' , --
	v2n							TEXT DEFAULT '' , --
	v2d							TEXT DEFAULT '' , --
	v3c							TEXT DEFAULT '' , --
	v3n							TEXT DEFAULT '' , --
	v3d							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow62					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow62 CASCADE;
CREATE SEQUENCE seq_flow62;
DROP TABLE IF EXISTS t_flow62 CASCADE;
CREATE TABLE t_flow62
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow62') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id

	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者, 3:核定人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容, 3:核定意见
	attachments					TEXT DEFAULT '' , -- 附件列表
	
	comp						TEXT DEFAULT '' , -- 3:施工单位
	date_ping					TEXT DEFAULT '' , -- 3:评定日期
	items						TEXT DEFAULT '' , -- 3:评分项目
	totals						TEXT DEFAULT '' , -- 3:评分项目
	m11							TEXT DEFAULT '' , -- 3:评定组成员
	m12							TEXT DEFAULT '' , -- 3:评定组成员
	m13							TEXT DEFAULT '' , -- 3:评定组成员
	m21							TEXT DEFAULT '' , -- 3:评定组成员
	m22							TEXT DEFAULT '' , -- 3:评定组成员
	m23							TEXT DEFAULT '' , -- 3:评定组成员
	m31							TEXT DEFAULT '' , -- 3:评定组成员
	m32							TEXT DEFAULT '' , -- 3:评定组成员
	m33							TEXT DEFAULT '' , -- 3:评定组成员
	m41							TEXT DEFAULT '' , -- 3:评定组成员
	m42							TEXT DEFAULT '' , -- 3:评定组成员
	m43							TEXT DEFAULT '' , -- 3:评定组成员
	m51							TEXT DEFAULT '' , -- 3:评定组成员
	m52							TEXT DEFAULT '' , -- 3:评定组成员
	m53							TEXT DEFAULT '' , -- 3:评定组成员
	m61							TEXT DEFAULT '' , -- 3:评定组成员
	m62							TEXT DEFAULT '' , -- 3:评定组成员
	m63							TEXT DEFAULT '' , -- 3:评定组成员

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow63					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow63 CASCADE;
CREATE SEQUENCE seq_flow63;
DROP TABLE IF EXISTS t_flow63 CASCADE;
CREATE TABLE t_flow63
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow63') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	name						TEXT DEFAULT '' , -- 工程名称
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	v1c							TEXT DEFAULT '' , --
	v1n							TEXT DEFAULT '' , --
	v1d							TEXT DEFAULT '' , --
	v2c							TEXT DEFAULT '' , --
	v2n							TEXT DEFAULT '' , --
	v2d							TEXT DEFAULT '' , --
	v3c							TEXT DEFAULT '' , --
	v3n							TEXT DEFAULT '' , --
	v3d							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow64					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow64 CASCADE;
CREATE SEQUENCE seq_flow64;
DROP TABLE IF EXISTS t_flow64 CASCADE;
CREATE TABLE t_flow64
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow64') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	name						TEXT DEFAULT '' , -- 工程名称
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	v1c							TEXT DEFAULT '' , --
	v1n							TEXT DEFAULT '' , --
	v1d							TEXT DEFAULT '' , --
	v2c							TEXT DEFAULT '' , --
	v2n							TEXT DEFAULT '' , --
	v2d							TEXT DEFAULT '' , --
	v3c							TEXT DEFAULT '' , --
	v3n							TEXT DEFAULT '' , --
	v3d							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow71					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow71 CASCADE;
CREATE SEQUENCE seq_flow71;
DROP TABLE IF EXISTS t_flow71 CASCADE;
CREATE TABLE t_flow71
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow71') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者, 3:核定人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容, 3:核定意见
	keywords					TEXT DEFAULT '' , -- 关键词
	attachments					TEXT DEFAULT '' , -- 附件列表

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow72					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow72 CASCADE;
CREATE SEQUENCE seq_flow72;
DROP TABLE IF EXISTS t_flow72 CASCADE;
CREATE TABLE t_flow72
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow72') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	attachments					TEXT DEFAULT '' , -- 附件列表
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	
	v1c							TEXT DEFAULT '' , --
	v1n							TEXT DEFAULT '' , --
	v1d							TEXT DEFAULT '' , --
	v2c							TEXT DEFAULT '' , --
	v2n							TEXT DEFAULT '' , --
	v2d							TEXT DEFAULT '' , --
	v3c							TEXT DEFAULT '' , --
	v3n							TEXT DEFAULT '' , --
	v3d							TEXT DEFAULT '' , --
	v4c							TEXT DEFAULT '' , --
	v4n							TEXT DEFAULT '' , --
	v4d							TEXT DEFAULT '' , --
	v5c							TEXT DEFAULT '' , --
	v5n							TEXT DEFAULT '' , --
	v5d							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow73					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow73 CASCADE;
CREATE SEQUENCE seq_flow73;
DROP TABLE IF EXISTS t_flow73 CASCADE;
CREATE TABLE t_flow73
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow73') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	attachments					TEXT DEFAULT '' , -- 附件列表
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	
	v1c							TEXT DEFAULT '' , --
	v1n							TEXT DEFAULT '' , --
	v1d							TEXT DEFAULT '' , --
	v2c							TEXT DEFAULT '' , --
	v2n							TEXT DEFAULT '' , --
	v2d							TEXT DEFAULT '' , --
	v3c							TEXT DEFAULT '' , --
	v3n							TEXT DEFAULT '' , --
	v3d							TEXT DEFAULT '' , --
	v4c							TEXT DEFAULT '' , --
	v4n							TEXT DEFAULT '' , --
	v4d							TEXT DEFAULT '' , --
	v5c							TEXT DEFAULT '' , --
	v5n							TEXT DEFAULT '' , --
	v5d							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow74					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow74 CASCADE;
CREATE SEQUENCE seq_flow74;
DROP TABLE IF EXISTS t_flow74 CASCADE;
CREATE TABLE t_flow74
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow74') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者, 3:核定人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容, 3:核定意见
	keywords					TEXT DEFAULT '' , -- 关键词
	attachments					TEXT DEFAULT '' , -- 附件列表

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow75					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow75 CASCADE;
CREATE SEQUENCE seq_flow75;
DROP TABLE IF EXISTS t_flow75 CASCADE;
CREATE TABLE t_flow75
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow75') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者, 3:核定人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容, 3:核定意见
	keywords					TEXT DEFAULT '' , -- 关键词
	attachments					TEXT DEFAULT '' , -- 附件列表

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow76					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow76 CASCADE;
CREATE SEQUENCE seq_flow76;
DROP TABLE IF EXISTS t_flow76 CASCADE;
CREATE TABLE t_flow76
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow76') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者, 3:核定人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容, 3:核定意见
	keywords					TEXT DEFAULT '' , -- 关键词
	attachments					TEXT DEFAULT '' , -- 附件列表

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow8					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow8 CASCADE;
CREATE SEQUENCE seq_flow8;
DROP TABLE IF EXISTS t_flow8 CASCADE;
CREATE TABLE t_flow8
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow8') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow91					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow91 CASCADE;
CREATE SEQUENCE seq_flow91;
DROP TABLE IF EXISTS t_flow91 CASCADE;
CREATE TABLE t_flow91
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow91') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	comp						TEXT DEFAULT '' , --
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 项目法人
	date 						TEXT DEFAULT '' , -- 日期
	
	items	 					TEXT DEFAULT '' , -- 条目
	totals						TEXT DEFAULT '' , -- 统计
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow921					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow921 CASCADE;
CREATE SEQUENCE seq_flow921;
DROP TABLE IF EXISTS t_flow921 CASCADE;
CREATE TABLE t_flow921
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow921') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	tno 						TEXT DEFAULT '' , -- 表头编号
	tcontent 					TEXT DEFAULT '' , -- 表头内容
	tdate	 					TEXT DEFAULT '' , -- 表头日期
	tattachments	 			TEXT DEFAULT '' , -- 表头附件
	tmemo			 			TEXT DEFAULT '' , -- 表头备注

	no 							TEXT DEFAULT '' , -- 编号
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	owner						TEXT DEFAULT '' , -- 项目法人
	comp						TEXT DEFAULT '' , -- 项目法人名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow922					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow922 CASCADE;
CREATE SEQUENCE seq_flow922;
DROP TABLE IF EXISTS t_flow922 CASCADE;
CREATE TABLE t_flow922
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow922') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	tno 						TEXT DEFAULT '' , -- 表头编号
	tcontent 					TEXT DEFAULT '' , -- 表头内容
	tdate	 					TEXT DEFAULT '' , -- 表头日期
	tattachments	 			TEXT DEFAULT '' , -- 表头附件
	tmemo			 			TEXT DEFAULT '' , -- 表头备注

	no 							TEXT DEFAULT '' , -- 编号
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	owner						TEXT DEFAULT '' , -- 项目法人
	comp						TEXT DEFAULT '' , -- 项目法人名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow923					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow923 CASCADE;
CREATE SEQUENCE seq_flow923;
DROP TABLE IF EXISTS t_flow923 CASCADE;
CREATE TABLE t_flow923
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow923') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	tno 						TEXT DEFAULT '' , -- 表头编号
	tcontent 					TEXT DEFAULT '' , -- 表头内容
	tdate	 					TEXT DEFAULT '' , -- 表头日期
	tattachments	 			TEXT DEFAULT '' , -- 表头附件
	tmemo			 			TEXT DEFAULT '' , -- 表头备注

	no 							TEXT DEFAULT '' , -- 编号
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	owner						TEXT DEFAULT '' , -- 项目法人
	comp						TEXT DEFAULT '' , -- 项目法人名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow924					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow924 CASCADE;
CREATE SEQUENCE seq_flow924;
DROP TABLE IF EXISTS t_flow924 CASCADE;
CREATE TABLE t_flow924
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow924') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	tno 						TEXT DEFAULT '' , -- 表头编号
	tcontent 					TEXT DEFAULT '' , -- 表头内容
	tdate	 					TEXT DEFAULT '' , -- 表头日期
	tattachments	 			TEXT DEFAULT '' , -- 表头附件
	tmemo			 			TEXT DEFAULT '' , -- 表头备注

	no 							TEXT DEFAULT '' , -- 编号
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	owner						TEXT DEFAULT '' , -- 项目法人
	comp						TEXT DEFAULT '' , -- 项目法人名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow925					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow925 CASCADE;
CREATE SEQUENCE seq_flow925;
DROP TABLE IF EXISTS t_flow925 CASCADE;
CREATE TABLE t_flow925
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow925') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	
	tno 						TEXT DEFAULT '' , -- 表头编号
	tcontent 					TEXT DEFAULT '' , -- 表头内容
	tdate	 					TEXT DEFAULT '' , -- 表头日期
	tattachments	 			TEXT DEFAULT '' , -- 表头附件
	tmemo			 			TEXT DEFAULT '' , -- 表头备注
	
	no 							TEXT DEFAULT '' , -- 编号
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	owner						TEXT DEFAULT '' , -- 项目法人
	comp						TEXT DEFAULT '' , -- 项目法人名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	
	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_flow9999					工作流文档
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_flow9999 CASCADE;
CREATE SEQUENCE seq_flow9999;
DROP TABLE IF EXISTS t_flow9999 CASCADE;
CREATE TABLE t_flow9999
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_flow9999') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id

	name						TEXT DEFAULT '' , -- 工程名称

	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发者人
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 内容
	keywords					TEXT DEFAULT '' , -- 关键词
	attachments					TEXT DEFAULT '' , -- 附件列表

	code 						TEXT DEFAULT '' , --
	personals					TEXT DEFAULT '' , --
	progress					TEXT DEFAULT '' , --
	writer						TEXT DEFAULT '' , --
	writer_date					TEXT DEFAULT '' , --
	signer_date					TEXT DEFAULT '' , --

	uid							BIGINT NOT NULL DEFAULT 0 , -- 最后审批者
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 最后审批时间
	replyid						BIGINT NOT NULL DEFAULT 0 , -- 批复回应ID
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改

	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply1				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply1 CASCADE;
CREATE SEQUENCE seq_reply1;
DROP TABLE IF EXISTS t_reply1 CASCADE;
CREATE TABLE t_reply1
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply1') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);


-- -----------------------------------------------------
-- Table t_reply2				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply2 CASCADE;
CREATE SEQUENCE seq_reply2;
DROP TABLE IF EXISTS t_reply2 CASCADE;
CREATE TABLE t_reply2
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply2') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply3				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply3 CASCADE;
CREATE SEQUENCE seq_reply3;
DROP TABLE IF EXISTS t_reply3 CASCADE;
CREATE TABLE t_reply3
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply3') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply4				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply4 CASCADE;
CREATE SEQUENCE seq_reply4;
DROP TABLE IF EXISTS t_reply4 CASCADE;
CREATE TABLE t_reply4
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply4') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发人
	comp						TEXT DEFAULT '' , -- 单位(项目法人)
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply51				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply51 CASCADE;
CREATE SEQUENCE seq_reply51;
DROP TABLE IF EXISTS t_reply51 CASCADE;
CREATE TABLE t_reply51
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply51') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply52				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply52 CASCADE;
CREATE SEQUENCE seq_reply52;
DROP TABLE IF EXISTS t_reply52 CASCADE;
CREATE TABLE t_reply52
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply52') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply61				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply61 CASCADE;
CREATE SEQUENCE seq_reply61;
DROP TABLE IF EXISTS t_reply61 CASCADE;
CREATE TABLE t_reply61
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply61') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply62				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply62 CASCADE;
CREATE SEQUENCE seq_reply62;
DROP TABLE IF EXISTS t_reply62 CASCADE;
CREATE TABLE t_reply62
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply62') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply63				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply63 CASCADE;
CREATE SEQUENCE seq_reply63;
DROP TABLE IF EXISTS t_reply63 CASCADE;
CREATE TABLE t_reply63
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply63') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply64				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply64 CASCADE;
CREATE SEQUENCE seq_reply64;
DROP TABLE IF EXISTS t_reply64 CASCADE;
CREATE TABLE t_reply64
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply64') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply71				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply71 CASCADE;
CREATE SEQUENCE seq_reply71;
DROP TABLE IF EXISTS t_reply71 CASCADE;
CREATE TABLE t_reply71
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply71') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply72				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply72 CASCADE;
CREATE SEQUENCE seq_reply72;
DROP TABLE IF EXISTS t_reply72 CASCADE;
CREATE TABLE t_reply72
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply72') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply73				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply73 CASCADE;
CREATE SEQUENCE seq_reply73;
DROP TABLE IF EXISTS t_reply73 CASCADE;
CREATE TABLE t_reply73
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply73') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply74				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply74 CASCADE;
CREATE SEQUENCE seq_reply74;
DROP TABLE IF EXISTS t_reply74 CASCADE;
CREATE TABLE t_reply74
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply74') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply75				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply75 CASCADE;
CREATE SEQUENCE seq_reply75;
DROP TABLE IF EXISTS t_reply75 CASCADE;
CREATE TABLE t_reply75
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply75') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply76				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply76 CASCADE;
CREATE SEQUENCE seq_reply76;
DROP TABLE IF EXISTS t_reply76 CASCADE;
CREATE TABLE t_reply76
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply76') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply8				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply8 CASCADE;
CREATE SEQUENCE seq_reply8;
DROP TABLE IF EXISTS t_reply8 CASCADE;
CREATE TABLE t_reply8
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply8') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply91				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply91 CASCADE;
CREATE SEQUENCE seq_reply91;
DROP TABLE IF EXISTS t_reply91 CASCADE;
CREATE TABLE t_reply91
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply91') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply921				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply921 CASCADE;
CREATE SEQUENCE seq_reply921;
DROP TABLE IF EXISTS t_reply921 CASCADE;
CREATE TABLE t_reply921
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply921') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	
	no 							TEXT DEFAULT '' , -- 编号
	content 					TEXT DEFAULT '' , -- 批复
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	signer_code					TEXT DEFAULT '' , -- 质量监督员证书号
	owner						TEXT DEFAULT '' , -- 审定
	owner_code					TEXT DEFAULT '' , -- 质量监督员证书号
	comp						TEXT DEFAULT '' , -- 项目法人名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply922				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply922 CASCADE;
CREATE SEQUENCE seq_reply922;
DROP TABLE IF EXISTS t_reply922 CASCADE;
CREATE TABLE t_reply922
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply922') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	
	no 							TEXT DEFAULT '' , -- 编号
	content 					TEXT DEFAULT '' , -- 批复
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	signer_code					TEXT DEFAULT '' , -- 质量监督员证书号
	owner						TEXT DEFAULT '' , -- 审定
	owner_code					TEXT DEFAULT '' , -- 质量监督员证书号
	comp						TEXT DEFAULT '' , -- 项目法人名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply923				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply923 CASCADE;
CREATE SEQUENCE seq_reply923;
DROP TABLE IF EXISTS t_reply923 CASCADE;
CREATE TABLE t_reply923
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply923') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	
	no 							TEXT DEFAULT '' , -- 编号
	content 					TEXT DEFAULT '' , -- 批复
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	signer_code					TEXT DEFAULT '' , -- 质量监督员证书号
	owner						TEXT DEFAULT '' , -- 审定
	owner_code					TEXT DEFAULT '' , -- 质量监督员证书号
	comp						TEXT DEFAULT '' , -- 项目法人名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply924				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply924 CASCADE;
CREATE SEQUENCE seq_reply924;
DROP TABLE IF EXISTS t_reply924 CASCADE;
CREATE TABLE t_reply924
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply924') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	
	no 							TEXT DEFAULT '' , -- 编号
	content 					TEXT DEFAULT '' , -- 批复
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	signer_code					TEXT DEFAULT '' , -- 质量监督员证书号
	owner						TEXT DEFAULT '' , -- 审定
	owner_code					TEXT DEFAULT '' , -- 质量监督员证书号
	approver					TEXT DEFAULT '' , -- 批准
	approver_code				TEXT DEFAULT '' , -- 质量监督员证书号
	comp						TEXT DEFAULT '' , -- 工程质量监督机构名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	c9							TEXT DEFAULT '' , --
	c10							TEXT DEFAULT '' , --
	
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply925				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply925 CASCADE;
CREATE SEQUENCE seq_reply925;
DROP TABLE IF EXISTS t_reply925 CASCADE;
CREATE TABLE t_reply925
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply925') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	
	no 							TEXT DEFAULT '' , -- 编号
	content 					TEXT DEFAULT '' , -- 批复
	proj						TEXT DEFAULT '' , -- 工程
	stage						TEXT DEFAULT '' , -- 阶段
	signer						TEXT DEFAULT '' , -- 编制
	signer_code					TEXT DEFAULT '' , -- 质量监督员证书号
	owner						TEXT DEFAULT '' , -- 审定
	owner_code					TEXT DEFAULT '' , -- 质量监督员证书号
	approver					TEXT DEFAULT '' , -- 批准
	approver_code				TEXT DEFAULT '' , -- 质量监督员证书号
	comp						TEXT DEFAULT '' , -- 工程质量监督机构名称
	date 						TEXT DEFAULT '' , -- 日期
	
	c1							TEXT DEFAULT '' , --
	c2							TEXT DEFAULT '' , --
	c3							TEXT DEFAULT '' , --
	c4							TEXT DEFAULT '' , --
	c5							TEXT DEFAULT '' , --
	c6							TEXT DEFAULT '' , --
	c7							TEXT DEFAULT '' , --
	c8							TEXT DEFAULT '' , --
	c9							TEXT DEFAULT '' , --
	c10							TEXT DEFAULT '' , --
	
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_reply9999				工作流批复
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_reply9999 CASCADE;
CREATE SEQUENCE seq_reply9999;
DROP TABLE IF EXISTS t_reply9999 CASCADE;
CREATE TABLE t_reply9999
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_reply9999') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	fid							BIGINT NOT NULL DEFAULT 0 , -- flow id
	no 							TEXT DEFAULT '' , -- 编号
	signer						TEXT DEFAULT '' , -- 签发人
	comp						TEXT DEFAULT '' , -- 单位(项目法人)
	date 						TEXT DEFAULT '' , -- 日期
	content 					TEXT DEFAULT '' , -- 批复
	uid 						BIGINT NOT NULL DEFAULT 0 , -- 审批者
	act							BOOLEAN NOT NULL DEFAULT false , -- 审批状态
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_facade_type			外观质量评定表条目
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_facade_type CASCADE;
CREATE SEQUENCE seq_facade_type;
DROP TABLE IF EXISTS t_facade_type CASCADE;
CREATE TABLE t_facade_type
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_facade_type') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 ,
	name						VARCHAR(50) NOT NULL ,
	score						INT NOT NULL DEFAULT 0 ,
	level						INT NOT NULL DEFAULT 1 ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);


-- -----------------------------------------------------
-- Table t_article_type			文章类型
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_article_type CASCADE;
CREATE SEQUENCE seq_article_type;
DROP TABLE IF EXISTS t_article_type CASCADE;
CREATE TABLE t_article_type
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_article_type') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 ,
	name						VARCHAR(50) NOT NULL ,
	dir							BOOLEAN NOT NULL DEFAULT false , -- 目录显示
	act							BOOLEAN NOT NULL DEFAULT true ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_article				文章管理
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_article CASCADE;
CREATE SEQUENCE seq_article;
DROP TABLE IF EXISTS t_article CASCADE;
CREATE TABLE t_article
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_article') NOT NULL ,
	tid							BIGINT NOT NULL DEFAULT 0 , -- type id
	uid							BIGINT NOT NULL DEFAULT 0 , -- poster
	pic							VARCHAR(200) DEFAULT '' ,
	title						VARCHAR(200) DEFAULT '' ,
	content						TEXT DEFAULT '' ,
	html						TEXT DEFAULT '' ,
	roll						BOOLEAN NOT NULL DEFAULT false , -- 首页滚动播放
	views						INTEGER NOT NULL DEFAULT 0 ,
	last						TIMESTAMP(0) DEFAULT NULL , -- 最后修改
	act							BOOLEAN NOT NULL DEFAULT true ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_feedback			意见反馈
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_feedback CASCADE;
CREATE SEQUENCE seq_feedback;
DROP TABLE IF EXISTS t_feedback CASCADE;
CREATE TABLE t_feedback
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_feedback') NOT NULL ,
	project						VARCHAR(50) DEFAULT '' , -- 投诉工程
	contacts					VARCHAR(50) DEFAULT '' , -- 联系人
	phone						VARCHAR(50) DEFAULT '' , -- 
	email						VARCHAR(50) DEFAULT '' , -- 
	content						TEXT DEFAULT '' , -- 反馈内容
	reply						TEXT DEFAULT '' , -- 答复内容
	uid							BIGINT NOT NULL DEFAULT 0 , -- 答复人
	replytime					TIMESTAMP(0) DEFAULT NULL , -- 答复时间
	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_attachment			附件
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_attachment CASCADE;
CREATE SEQUENCE seq_attachment;
DROP TABLE IF EXISTS t_attachment CASCADE;
CREATE TABLE t_attachment
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_attachment') NOT NULL ,
	pid							BIGINT NOT NULL DEFAULT 0 , -- project id
	tid							BIGINT NOT NULL DEFAULT 0 , -- type id
	no							INT NOT NULL DEFAULT 0 , --
	name						TEXT DEFAULT '' , -- 显示名称
	url							TEXT DEFAULT '' , -- 
	file						TEXT DEFAULT '' , -- 文件名称
	ext							VARCHAR(50) DEFAULT '' , -- 
	size						INT NOT NULL DEFAULT 0 , --
	act							BOOLEAN NOT NULL DEFAULT false ,
	del							BOOLEAN NOT NULL DEFAULT false ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_msg				消息表
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_msg CASCADE;
CREATE SEQUENCE seq_msg;
DROP TABLE IF EXISTS t_msg CASCADE;
CREATE TABLE t_msg
(
  id				BIGINT UNIQUE DEFAULT NEXTVAL('seq_msg') NOT NULL ,
  type			INTEGER NOT NULL DEFAULT 0 ,
  direct		INTEGER NOT NULL DEFAULT 0 , -- 1:管理员发送 2:项目用户发送
  senderid  	BIGINT NOT NULL DEFAULT 0 ,
  receiverid  	  BIGINT NOT NULL DEFAULT 0 ,
  sender	  VARCHAR(200) DEFAULT '' ,
  receiver  VARCHAR(200) DEFAULT '' ,
  nodeid  	BIGINT NOT NULL DEFAULT 0 , -- 节点
  objid   	BIGINT NOT NULL DEFAULT 0 , -- 对象
  msg   		TEXT DEFAULT '' ,
  read			BOOLEAN NOT NULL DEFAULT false ,
  del				BOOLEAN NOT NULL DEFAULT false ,
  time			TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (id)
);


-- -----------------------------------------------------
-- Table t_forget				忘记密码
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_forget CASCADE;
CREATE SEQUENCE seq_forget;
DROP TABLE IF EXISTS t_forget CASCADE;
CREATE TABLE t_forget
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_forget') NOT NULL ,
	pid       			INTEGER NOT NULL DEFAULT 0 , -- 项目ID
	code						VARCHAR(50) DEFAULT '' ,
	expire          TIMESTAMP(0) NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL '1 day') ,
	time			      TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);


-- ######################################################
-- 日志表
-- ######################################################

-- -----------------------------------------------------
-- Table t_log_login			登录日志
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_log_login CASCADE;
CREATE SEQUENCE seq_log_login;
DROP TABLE IF EXISTS t_log_login CASCADE;
CREATE TABLE t_log_login
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_log_login') NOT NULL ,
	tid							INTEGER NOT NULL DEFAULT 0 ,	-- type: 1:member 2:user
	uid							BIGINT NOT NULL DEFAULT 0 ,		-- 1:mid 2:uid
	ip							BIGINT NOT NULL DEFAULT 0 ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);

-- -----------------------------------------------------
-- Table t_log_event			数据事件日志
-- -----------------------------------------------------
DROP SEQUENCE IF EXISTS seq_log_event CASCADE;
CREATE SEQUENCE seq_log_event;
DROP TABLE IF EXISTS t_log_event CASCADE;
CREATE TABLE t_log_event
(
	id							BIGINT UNIQUE DEFAULT NEXTVAL('seq_log_event') NOT NULL ,
	event						TEXT NOT NULL DEFAULT '' ,
	spent						FLOAT NOT NULL DEFAULT 0 ,
	memory						INTEGER NOT NULL DEFAULT 0 ,
	time						TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (id)
);


-- #####################################################
-- DATA PART
-- #####################################################

-- -----------------------------------------------------
-- Insert t_group
-- -----------------------------------------------------
INSERT INTO t_group(name, company) VALUES('徐州市水利工程质量监督站', '徐州市水利工程质量监督站');

-- -----------------------------------------------------
-- Insert t_org
-- -----------------------------------------------------
INSERT INTO t_org(gid, name) VALUES(1, '市水利工程质量监督站');

-- -----------------------------------------------------
-- Insert t_user
-- -----------------------------------------------------
INSERT INTO t_user(username, password, name) VALUES('admin', md5('111111'), '管理员');

-- -----------------------------------------------------
-- Insert t_article_type
-- -----------------------------------------------------
INSERT INTO t_article_type(name) VALUES('单位信息');
INSERT INTO t_article_type(name, dir) VALUES('工作动态', true);
INSERT INTO t_article_type(name, dir) VALUES('政策法规', true);
INSERT INTO t_article_type(name, dir) VALUES('工程造价', true);
INSERT INTO t_article_type(name, dir) VALUES('行业管理', true);
INSERT INTO t_article_type(name, dir) VALUES('下载专区', true);

-- -----------------------------------------------------
-- Insert t_article
-- -----------------------------------------------------
INSERT INTO t_article(tid, uid, title) VALUES(1, 1, '单位介绍');
INSERT INTO t_article(tid, uid, title) VALUES(1, 1, '联系我们');

-- -----------------------------------------------------
-- Insert t_facade_type
-- -----------------------------------------------------
INSERT INTO t_facade_type(name) VALUES('检查项目');
INSERT INTO t_facade_type(name) VALUES('检测项目');





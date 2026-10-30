# THESE create statements will NOT work if this file is piped into MySQL. 
# Rather they will be detected by the Typo3 Install Tool and through that 
# you should upgrade the tables to content these fields.


CREATE TABLE tt_address (
	static_info_country varchar(3) DEFAULT '' NOT NULL,
	zone varchar(45) DEFAULT '' NOT NULL,
	language varchar(5) DEFAULT '' NOT NULL,
	date_of_birth int(11) DEFAULT '0' NOT NULL,
	comments varchar(1024) DEFAULT '' NOT NULL,
    module_sys_dmail_category int(10) unsigned DEFAULT '0' NOT NULL,
	module_sys_dmail_html tinyint(3) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'sys_dmail_group_category_mm'
#
#
CREATE TABLE sys_dmail_group_category_mm (
  fieldname varchar(30) DEFAULT '' NOT NULL
);

#
# Table structure for table 'sys_dmail_feuser_category_mm'
#
#
CREATE TABLE sys_dmail_feuser_category_mm (
  fieldname varchar(30) DEFAULT '' NOT NULL
);

#
# Table structure for table 'sys_dmail_ttaddress_category_mm'
#
#
CREATE TABLE sys_dmail_ttaddress_category_mm (
  fieldname varchar(30) DEFAULT '' NOT NULL
);

#
# Table structure for table 'sys_dmail_ttcontent_category_mm'
#
#
CREATE TABLE sys_dmail_ttcontent_category_mm (
  fieldname varchar(30) DEFAULT '' NOT NULL
);
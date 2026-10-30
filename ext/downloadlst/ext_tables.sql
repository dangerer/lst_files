#
# Table structure for table 'pages'
#
CREATE TABLE pages (

	new smallint(5) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (

	new smallint(5) unsigned DEFAULT '0' NOT NULL,
	for_all_language smallint(5) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'sys_category'
#
CREATE TABLE sys_category (

	feuserpage smallint(5) unsigned DEFAULT '0' NOT NULL,

);
CREATE TABLE tx_downloadlist_newflag(
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	contenttyp int(1) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	lst_region int(11) DEFAULT 0 NOT NULL,
	fe_language int(11) DEFAULT 0 NOT NULL,
	lst_newsletter int(1) DEFAULT 1 NOT NULL,
	technical_newsletter int(1) DEFAULT 1 NOT NULL,
	marketing_newsletter int(1) DEFAULT 1 NOT NULL,
	customer_number varchar(250) DEFAULT '' NOT NULL
);

CREATE TABLE tt_address (
		terms_acknowledged tinyint(4) unsigned DEFAULT '0' NOT NULL
);

CREATE TABLE tx_news_domain_model_news (
  lst_fullybooked tinyint(4) unsigned DEFAULT '0' NOT NULL
);
CREATE TABLE pages (
    source_id int(11) DEFAULT '0' NOT NULL
);
CREATE TABLE tt_content (
    source_id int(11) DEFAULT '0' NOT NULL
);
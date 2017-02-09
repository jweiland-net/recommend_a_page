#
# Table structure for table 'tx_recommendapage_domain_model_recommendedpage'
#
CREATE TABLE tx_recommendapage_domain_model_recommendedpage (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	referrer_pid int(11) unsigned DEFAULT '0' NOT NULL,
	target_pid int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# (Additional) table structure for table 'pages'
#
CREATE TABLE pages (
	tx_recommend_a_page_do_not_recommend TINYINT(1) DEFAULT 0 NOT NULL
);

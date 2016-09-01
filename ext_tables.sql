#
# Table structure for table 'tx_rookieinternshipconnector_domain_model_application'
#
CREATE TABLE tx_recommendapage_domain_model_recommendedpage (
	uid int(11) NOT NULL auto_increment,

	ref_pid int(11) unsigned DEFAULT '0' NOT NULL,
	target_pid int(11) unsigned DEFAULT '0' NOT NULL,

	crdate int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid)
);
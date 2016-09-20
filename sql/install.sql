CREATE TABLE IF NOT EXISTS `PREFIX_c3_mainfilter_selection` (
 id_filter_selection MEDIUMINT unsigned NOT NULL AUTO_INCREMENT
 ,name TINYTEXT NOT NULL
 ,tcreation DATETIME DEFAULT CURRENT_TIMESTAMP
 ,PRIMARY KEY (id_filter_selection)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `PREFIX_c3_mainfilter_selection_part` (
 id_filter_selection MEDIUMINT unsigned NOT NULL
 ,id_feature INT(10) unsigned NOT NULL
 ,id_feature_value INT(10) unsigned NOT NULL
 ,order_part TINYINT(1) NOT NULL DEFAULT 0
 ,tcreation DATETIME DEFAULT CURRENT_TIMESTAMP
 ,PRIMARY KEY (id_filter_selection, id_feature, id_feature_value, order_part)
 ,CONSTRAINT FOREIGN KEY (id_filter_selection) REFERENCES PREFIX_c3_mainfilter_selection (id_filter_selection) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT FOREIGN KEY (id_feature) REFERENCES PREFIX_feature (id_feature) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT FOREIGN KEY (id_feature_value) REFERENCES PREFIX_feature_value (id_feature_value) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `PREFIX_c3_mainfilter_selection_group` (
 id_filter_selection_group SMALLINT unsigned NOT NULL AUTO_INCREMENT
 ,name TINYTEXT NOT NULL
 ,number_step TINYINT(1) NOT NULL DEFAULT 0
 ,tcreation DATETIME DEFAULT CURRENT_TIMESTAMP
 ,PRIMARY KEY (id_filter_selection_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `PREFIX_c3_mainfilter_selection_group_member` (
 id_filter_selection_group SMALLINT unsigned NOT NULL
 ,id_filter_selection MEDIUMINT unsigned NOT NULL
 ,tcreation DATETIME DEFAULT CURRENT_TIMESTAMP
 ,PRIMARY KEY (id_filter_selection_group, id_filter_selection)
 ,CONSTRAINT FOREIGN KEY (id_filter_selection_group) REFERENCES PREFIX_c3_mainfilter_selection_group (id_filter_selection_group) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT FOREIGN KEY (id_filter_selection) REFERENCES PREFIX_c3_mainfilter_selection (id_filter_selection) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `PREFIX_c3_mainfilter_selection_group_shelf` (
 id_filter_selection_group SMALLINT unsigned NOT NULL
 ,id_category INT(10) unsigned NOT NULL
 ,tcreation DATETIME DEFAULT CURRENT_TIMESTAMP
 ,PRIMARY KEY (id_filter_selection_group, id_category)
 ,CONSTRAINT FOREIGN KEY (id_filter_selection_group) REFERENCES PREFIX_c3_mainfilter_selection_group (id_filter_selection_group) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT FOREIGN KEY (id_category) REFERENCES PREFIX_category (id_category) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE OR REPLACE VIEW `PREFIX_vc3_mainfilter_selection_group_filters` AS SELECT DISTINCT
 msgm.id_filter_selection_group AS id_filter_selection_group, msgm.id_filter_selection AS id_filter_selection
 FROM `PREFIX_c3_mainfilter_selection_group_member` AS msgm
 INNER JOIN `PREFIX_c3_mainfilter_selection` AS ms ON (ms.id_filter_selection = msgm.id_filter_selection)
 ORDER BY id_filter_selection_group, id_filter_selection;
CREATE OR REPLACE VIEW `PREFIX_vc3_mainfilter_selection_group` AS SELECT DISTINCT
 id_filter_selection_group, name, number_step
 FROM `PREFIX_c3_mainfilter_selection_group`
 ORDER BY tcreation;
CREATE OR REPLACE VIEW `PREFIX_vc3_mainfilter_selection_group_shelf` AS SELECT DISTINCT
 id_filter_selection_group, id_category
 FROM `PREFIX_c3_mainfilter_selection_group_shelf`
 ORDER BY id_category, tcreation, id_filter_selection_group;
CREATE OR REPLACE VIEW `PREFIX_vc3_mainfilter_selection_group_member` AS SELECT DISTINCT
 id_filter_selection, id_filter_selection_group
 FROM `PREFIX_c3_mainfilter_selection_group_member`
 ORDER BY id_filter_selection_group, id_filter_selection;
CREATE OR REPLACE VIEW `PREFIX_vc3_mainfilter_selection_part_informations` AS SELECT DISTINCT
 sp.id_filter_selection AS id_filter_selection, sp.id_feature AS id_feature, sp.id_feature_value AS id_feature_value, sp.order_part AS order_part
 ,fl.id_lang AS id_lang, fl.name AS name_feature
 ,fvl.value AS name_feature_value
 FROM `PREFIX_c3_mainfilter_selection_part` AS sp
 INNER JOIN `PREFIX_feature_lang` AS fl ON (fl.id_feature = sp.id_feature)
 INNER JOIN `PREFIX_feature_value_lang` AS fvl ON (fvl.id_feature_value = sp.id_feature_value AND fvl.id_lang = fl.id_lang)
 ORDER BY id_lang, id_filter_selection, order_part;

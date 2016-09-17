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
 ,order_part BIT NOT NULL DEFAULT 0
 ,tcreation DATETIME DEFAULT CURRENT_TIMESTAMP
 ,PRIMARY KEY (id_filter_selection, id_feature, id_feature_value, order_part)
 ,CONSTRAINT FOREIGN KEY (id_filter_selection) REFERENCES PREFIX_c3_mainfilter_selection (id_filter_selection) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT FOREIGN KEY (id_feature) REFERENCES PREFIX_feature (id_feature) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT FOREIGN KEY (id_feature_value) REFERENCES PREFIX_feature_value (id_feature_value) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `PREFIX_c3_mainfilter_selection_group` (
 id_filter_selection_group SMALLINT unsigned NOT NULL AUTO_INCREMENT
 ,name TINYTEXT NOT NULL
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
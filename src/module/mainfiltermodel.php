<?php
/*
 * Manage interactions between MainFilterController and the database of the shop
 * 
 * @author Schnepp David <david.schnepp@schneppd.com>
 * @since v0.1 2016/09/17
 */

namespace NsC3MainFilterModule;

include_once(dirname(__FILE__) . '/../framework/modulemodel.php');

class MainFilterModel extends \NsC3Framework\ModuleModel {

	/*
	 * the constructor
	 * 
	 * @author Schnepp David
	 * @since v0.1 2016/09/17
	 * @param DatabaseConnection $db the database instance used for each query
	 */
	public function __construct($db) {
		parent::__construct($db);
	}
	
	/*
	 * query and return the list of categories in this shop, except root (nothing to display)
	 * 
	 * @author Schnepp David
	 * @since v0.1 2016/09/17
	 * @return mixed[] the list of categories in this shop
	 */
	public function getCategories() {
		$sql = 'SELECT id_category FROM `' . $this->database->getDatabasePrefix() . 'category` WHERE active=1 AND id_parent > 0';
		return $this->database->getDatabaseInstance()->executeS($sql);
	}

}

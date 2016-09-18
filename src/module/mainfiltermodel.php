<?php
/*
 * Manage interactions between MainFilterController and the database of the shop
 * 
 * @author Schnepp David <david.schnepp@schneppd.com>
 * @since v0.1 2016/09/17
 */

namespace NsC3MainFilterModule;

include_once(dirname(__FILE__) . '/../framework/modulemodel.php');

class MainFilterModel extends \NsC3MainFilterFramework\ModuleModel {

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

	/*
	 * query and return the list of categories with a defined filter group
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @return mixed[] the list of categories with a defined filter group
	 */
	public function getCategoriesWithFilters() {
		$sql = 'SELECT id_filter_selection_group, id_category FROM `' . $this->database->getDatabasePrefix() . 'c3_mainfilter_selection_group_shelf`';
		return $this->database->getDatabaseInstance()->executeS($sql);
	}
	
	/*
	 * query and return the list of categories with a defined filter group
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @return mixed[] the list of categories with a defined filter group
	 */
	public function getFilterGroups() {
		$sql = 'SELECT id_filter_selection_group, name FROM `' . $this->database->getDatabasePrefix() . 'c3_mainfilter_selection_group`';
		return $this->database->getDatabaseInstance()->executeS($sql);
	}
	
	/*
	 * query and return the list of all possible selection values for given filter group
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @return mixed[] the list of all selection values (multi dimension array) for given filter group
	 */
	public function getAllFilterGroupSelectionValues(&$id_filter_selection_group, &$id_lang) {
		$sql = 'SELECT id_filter_selection_group, name FROM `' . $this->database->getDatabasePrefix() . 'c3_mainfilter_selection_group`';
		return $this->database->getDatabaseInstance()->executeS($sql);
	}
}

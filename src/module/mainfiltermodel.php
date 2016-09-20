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
		$sql = 'SELECT id_filter_selection_group, id_category FROM `' . $this->database->getDatabasePrefix() . 'vc3_mainfilter_selection_group_shelf`';
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
		$sql = 'SELECT id_filter_selection_group, name, number_step FROM `' . $this->database->getDatabasePrefix() . 'vc3_mainfilter_selection_group`';
		return $this->database->getDatabaseInstance()->executeS($sql);
	}
	
	public function getFiltersInFilterGroup(&$id_filter_selection_group) {
		$sql = 'SELECT id_filter_selection FROM `' . $this->database->getDatabasePrefix() . 'vc3_mainfilter_selection_group_filters` WHERE id_filter_selection_group = '. (int) $id_filter_selection_group;
		return $this->database->getDatabaseInstance()->executeS($sql);
	}
	
	public function getFiltersInFilterGroupToArray(&$id_filter_selection_group) {
		$filters = $this->getFiltersInFilterGroup($id_filter_selection_group);
		$res = array();
		foreach($filters as $filter){
			$id_filter_selection = (int) $filter['id_filter_selection'];
			array_push($res, $id_filter_selection);
		}
		return $res;
	}
	
	public function getFilterGroupChoices(&$id_lang, &$filter_selections, &$order_part) {
		$res = array();
		foreach($filter_selections as $id_filter_selection){
			$sql = 'SELECT id_feature, id_feature_value, name_feature, name_feature_value FROM `' . $this->database->getDatabasePrefix() . 'vc3_mainfilter_selection_part_informations` WHERE order_part = '. (int) $order_part .' AND id_filter_selection = '. (int) $id_filter_selection . ' AND id_lang = ' . (int) $id_lang;
			$choices = $this->database->getDatabaseInstance()->executeS($sql);
			foreach($choices as $choice){
				$id_feature = (string) $choice['id_feature'];
				if (!isset($res[$id_feature])) {
					$res[$id_feature] = array();
					$name_feature = (string)$choice['name_feature'];
					$res[$id_feature]['name'] = $name_feature;
					$res[$id_feature]['values'] = array();

				}

				$id_feature_value = $choice['id_feature_value'];
				if (!array_key_exists($id_feature_value, $res[$id_feature]['values'][$id_feature_value])) {
					$name_feature_value = (string) $choice['name_feature_value'];
					$res[$id_feature]['values'][$id_feature_value] = array();
					$res[$id_feature]['values'][$id_feature_value]['name'] = $name_feature_value;
					$res[$id_feature]['values'][$id_feature_value]['path'] = array();
				}
				array_push($res[$id_feature]['values'][$id_feature_value]['path'], (int) $id_filter_selection);
			}
		}
		return $res;
	}
}

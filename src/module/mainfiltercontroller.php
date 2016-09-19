<?php
/*
 * Process inputs from module view(c3mainfilter),
 * tells the model what to save
 * tells the view what to display/expose to prestashop
 * 
 * @author Schnepp David <david.schnepp@schneppd.com>
 * @since v0.1 2016/09/17
 */

namespace NsC3MainFilterModule;

include_once(dirname(__FILE__) . '/mainfiltermodel.php');
include_once(dirname(__FILE__) . '/../framework/modulecontroller.php');
include_once(dirname(__FILE__) . '/../framework/moduleio.php');

class MainFilterController extends \NsC3MainFilterFramework\ModuleController {

	/*
	 * the constructor
	 * 
	 * @author Schnepp David
	 * @since v0.1 2016/09/17
	 * @param ModuleInformations $infos the informations for this module
	 * @param DatabaseConnection $databaseConnection the database connection to use for the models
	 */
	public function __construct($infos, $databaseConnection) {
		parent::__construct($infos);
		$this->model = new MainFilterModel($databaseConnection);
	}
	
	/*
	 * regenerates cache files for:
	 * - categories where to display a filter
	 * - the data to expose as choices for each filter
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @param int $id_lang the lang in current call context, used to select the name value to use
	 */
	public function regenerateFiltersAndCategoriesCaches($id_lang) {
		static::emptyModuleCache();
		$this->generateCategoriesCacheFiles();
		$this->generateFilterGroupsCacheFiles($id_lang);
	}
	
	/*
	 * generate cache files for categories with a filter
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @param array $categoriesWithFilters the list of category with a filter defined
	 */
	private function generateCategoriesCacheFiles() {
		$categoriesWithFilters = $this->model->getCategoriesWithFilters();
		foreach($categoriesWithFilters as $category) {
			$id_category = (int) $category['id_category'];
			$id_filter_selection_group = (int) $category['id_filter_selection_group'];
			$file = 'category-' . $id_category . '.json';
			$filePath = static::$moduleInformations->getModuleCacheFilePath($file);
			$content = array('id_filter_selection_group' => $id_filter_selection_group);
			\NsC3MainFilterFramework\ModuleIO::writeArrayToJsonFile($content, $filePath);
		}
	}

	/*
	 * generate cache files for all filter groups
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @param array $categoriesWithFilters the list of category with a filter defined
	 */
	private function generateFilterGroupsCacheFiles(&$id_lang) {
		$filterGroups = $this->model->getFilterGroups();
		foreach($filterGroups as $filterGroup) {
			$id_filter_selection_group = (int) $filterGroup['id_filter_selection_group'];
			$nameFilterGroup = (string) $filterGroup['name'];
			$number_step = (int) $filterGroup['number_step'];
			
			$filters = $this->model->getFiltersInFilterGroupToArray($id_filter_selection_group);
			$filterGroupRootChoices = $this->model->getFilterGroupRootChoices($id_filter_selection_group, $id_lang, $filters);
			
			$file = 'filter-' . $id_filter_selection_group . '.json';
			$filePath = static::$moduleInformations->getModuleCacheFilePath($file);
			$filterGroupData = array();
			$filterGroupData['id_filter_selection_group'] = $id_filter_selection_group;
			$filterGroupData['name'] = $nameFilterGroup;
			$filterGroupData['number_step'] = $number_step;
			$filterGroupData['options'] = $filterGroupRootChoices;
			
			\NsC3MainFilterFramework\ModuleIO::writeArrayToJsonFile($filterGroupData, $filePath);
			$this->generateFilterGroupRootFiles();
			
		}
	}
	
	
}

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
	 * generate cache files for the categories with a filter attached
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @param array $categoriesWithFilters the list of category with a filter defined
	 */
	private function generateCategoriesCacheFiles() {
		$categoriesWithFilters = $this->model->getCategoriesWithFilters();//max complexity may be 500 * 3
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
		$filterGroups = $this->model->getFilterGroups();// max 5-15
		foreach($filterGroups as $filterGroup) {
			$id_filter_selection_group = (int) $filterGroup['id_filter_selection_group'];

			$filters = $this->model->getFiltersInFilterGroupToArray($id_filter_selection_group);
			$fileStart = 'filter-' . $id_filter_selection_group;
			
			$current_step = 0;
			$filterGroupRootChoices = $this->model->getFilterGroupChoices($id_lang, $filters, $current_step);
			$this->writeFilterGroupsCacheFiles($filterGroup, $fileStart, $filterGroupRootChoices, $current_step);
			
			$current_step++;
			$this->generateFilterGroupSelectionPartsFiles($filterGroup, $filterGroupRootChoices, $id_lang, $fileStart, $current_step);
		}
	}
	
	/*
	 * create the final cache file of provided filtergroup part
	 * 
	 * @author Schnepp David
	 * @since 2016/09/20
	 * @param array $filterGroup The filterGroup's data
	 * @param string $fileName the name of the file to create
	 * @param array $options All available choices for current part
	 * @param int $step for which step of filtergroup the file is created
	 */
	private function writeFilterGroupsCacheFiles(&$filterGroup, &$fileName, &$options, &$step) {
		$file = $fileName . '.json';
		$filePath = static::$moduleInformations->getModuleCacheFilePath($file);
		$filterGroupData = array();
		$filterGroupData['id_filter_selection_group'] = (int) $filterGroup['id_filter_selection_group'];
		if($step == 0) {
			$filterGroupData['name'] = (string) $filterGroup['name'];
			$filterGroupData['number_step'] = (int) $filterGroup['number_step'];
		}
		$filterGroupData['options'] = $this->removePathFromChoices($options);
		//$filterGroupData['options'] = $options;
		\NsC3MainFilterFramework\ModuleIO::writeArrayToJsonFile($filterGroupData, $filePath);
		
	}
	
	/*
	 * process given choices map to removes the path data (for final json cache creation)
	 * 
	 * @author Schnepp David
	 * @since 2016/09/20
	 * @param array $filterGroupChoices map of each available choice
	 * @return array processed choices map
	 */
	private function removePathFromChoices($filterGroupChoices) {
		foreach($filterGroupChoices as $id_feature => $data_feature) {
			foreach($data_feature['values'] as $id_feature_value => $data_feature_value) {
				unset($filterGroupChoices[$id_feature]['values'][$id_feature_value]['path']);
			}
		}
		return $filterGroupChoices;
	}
	
	/*
	 * recursively browse the given filterGroup choice tree and create corresponding cache file parts
	 * 
	 * @author Schnepp David
	 * @since 2016/09/20
	 * @param array $filterGroup The filterGroup's data
	 * @param array $filterGroupChoices all id_filter_selection remaining for current choice branch
	 * @param int $id_lang the lang for selecting feature and feature_value description
	 * @param string $fileStart file name of the previous branch
	 * @param int $current_step for which step of filtergroup the file is created
	 */
	private function generateFilterGroupSelectionPartsFiles(&$filterGroup, &$filterGroupChoices, &$id_lang, $fileStart, $current_step) {
		foreach($filterGroupChoices as $id_feature => $data_feature) {
			foreach($data_feature['values'] as $id_feature_value => $data_feature_value) {
				$newFileStart = $fileStart . '_' . $id_feature . '-' . $id_feature_value;
				$filters = $data_feature_value['path'];
				
				$newFilterGroupChoices = $this->model->getFilterGroupChoices($id_lang, $filters, $current_step);
				$this->writeFilterGroupsCacheFiles($filterGroup, $newFileStart, $newFilterGroupChoices, $current_step);
				
				$next_step = $current_step + 1;
				$number_step = (int) $filterGroup['number_step'];
				if($next_step < $number_step) {
					$this->generateFilterGroupSelectionPartsFiles($filterGroup, $newFilterGroupChoices, $id_lang, $newFileStart, $next_step);
				}
			}
		}
	}
	
	/*
	 * used to tell if the module should add mainfilter.js to the frontend js dependancies
	 * 
	 * @author Schnepp David
	 * @since 2016/10/29
	 * @param int $id_category_ext the category to test
	 * @return boolean Y/N does it exists
	 */
	public function existsFilterFile(&$id_category_ext) {
		$id_category = (int) $id_category_ext;
		if($id_category > 0) {
			$file = 'category-' . $id_category . '.json';
			$filePath = static::$moduleInformations->getModuleCacheFilePath($file);
			return \NsC3MainFilterFramework\ModuleIO::existFile($filePath);
		}
		return false;//no category page is used
	}
	
	/*
	 * used to get the corresponding first filter json data file
	 * 
	 * @author Schnepp David
	 * @since 2016/10/29
	 * @param int $id_category the category to get the data from
	 * @return string the complete path to the json file
	 */
	public function getFilterFileData(&$id_category) {
		$file = 'category-' . $id_category . '.json';
		$filePath = static::$moduleInformations->getModuleCacheFilePath($file);
		return $filePath;
	}
	
}

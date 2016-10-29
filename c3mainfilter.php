<?php
/* 
 * This module is used to display a stepped filter in category pages to show all products meeting the filter requirement
 * @author Schnepp David <david.schnepp@schneppd.com>
 * @since v0.1 2016/09/17
 * @param int C3MAINFILTER_NB the max number of product to show per call
 */

// if major problem with prestashop, abort
if (!defined('_PS_VERSION_'))
	exit;

include_once(dirname(__FILE__).'/../../config/config.inc.php');

require_once(dirname(__FILE__) . '/src/module/mainfiltercontroller.php');
require_once(dirname(__FILE__) . '/src/framework/databaseconnection.php');
require_once(dirname(__FILE__) . '/src/framework/moduleinformations.php');

class C3MainFilter extends Module {

	/*
	* The module's controller
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	*/
	protected $controller;
	
	/*
	 * the module constructor
	 * 
	 * @author Schnepp David
	 * @since v0.1 2016/09/17
	 */
	function __construct() {
		//setup this module's basic informations
		$this->name = 'c3mainfilter';
		$this->tab = 'front_office_features';
		$this->version = '0.2.0';
		$this->author = 'Schnepp David';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		//setup this module's informations for back-end
		$this->displayName = $this->l('C3MainFilter block');
		$this->description = $this->l("Adds C3's stepped filter list in front-end.");
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		
		$this->customizeModuleConstruction();

	}
	
	/*
	* Create the controller for this module and read the module's execution context
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	*/
	protected function customizeModuleConstruction() {
		$moduleInformations = new \NsC3MainFilterFramework\ModuleInformations($this->name, dirname(__FILE__), _PS_CACHE_DIR_, _DB_PREFIX_);
		$dbConnection = new \NsC3MainFilterFramework\DatabaseConnection(Db::getInstance(_PS_USE_SQL_SLAVE_), _DB_PREFIX_);
		$this->controller = new \NsC3MainFilterModule\MainFilterController($moduleInformations, $dbConnection);
	}
	
	/*
	* The module's installation steps
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	* @return boolean if the installation succeeded
	*/
	function install() {
		if(!$this->controller->installModuleInDatabase())
			return false;
		if(!$this->controller->installModuleCache())
			return false;

		// clear cache to delete possible afterfacts
		$this->_clearCache('*');

		//register module in hooks
		if (!parent::install() ||
				 // register module for following hooks
				 !$this->registerHook('header') ||
				 // max tags to display
				 !Configuration::updateValue('C3MAINFILTER_NB', 30)
		)
			return false;
		
		return true;
	}

	/*
	* The module's uninstallation steps
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	* @return boolean if the uninstallation succeeded
	*/
	public function uninstall() {
		// clear cache to delete possible afterfacts
		$this->_clearCache('*');

		if(!$this->controller->uninstallModuleInDatabase())
			return false;
		if(!$this->controller->uninstallModuleCache())
			return false;

		// uninstall module from hooks
		if (!parent::uninstall() || !Configuration::deleteByName('C3MAINFILTER_NB'))
			return false;

		return true;
	}

	/*
	* Defines css files to add to head hook
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	* @todo test if filter to display in category page
	*/
	public function hookHeader($params) {
		$this->context->controller->addCSS(($this->_path) . 'views/css/c3mainfilter.css', 'all');
	}

	/*
	* Process backend form post for module
	* here on each call, all the filters cache files will be regenerate
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	* @return string the html content to display
	*/
	public function getContent() {
		$output = null;
		$errors = array();
		//if correct sending
		if (Tools::isSubmit('submit'.$this->name)) {
			//check if module's cache dir exists
			$isCacheExist = $this->controller->isModuleCacheCreated();
			if (!$isCacheExist)
				$errors[] = $this->l('There is an error with the module\'s cache dir creation/existence (rights problem most likely).');
			// check if C3KEYWORDS_NB was provided
			$maxProductPerCategoryFilterCall = Tools::getValue('C3MAINFILTER_NB');
			if (!strlen($maxProductPerCategoryFilterCall))
				$errors[] = $this->l('Please complete the "Displayed tags" field.');
			elseif (!Validate::isInt($maxProductPerCategoryFilterCall) || (int) ($maxProductPerCategoryFilterCall) <= 0)
				$errors[] = $this->l('Invalid number.');
			// if errors, display error messages
			if (count($errors))
				$output = $this->displayError(implode('<br />', $errors));
			else {
				// update module values
				Configuration::updateValue('C3MAINFILTER_NB', (int) $maxProductPerCategoryFilterCall);
				$this->regenerateFiltersCaches();
				
				$output = $this->displayConfirmation($this->l('Tagblocks generated'));
			}
		}
		return $output . $this->renderForm();
	}
	
	/*
	* Regenerate all filter caches
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	* @todo create functionalities
	*/
	protected function regenerateFiltersCaches() {
		$id_lang = (int) $this->context->language->id;
		$this->controller->regenerateFiltersAndCategoriesCaches($id_lang);
	}
	
	/*
	* Create form to show in module's backend interface
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	* @return string final html
	*/
	public function renderForm() {
		// setup form fields
		$fields_form = array(
			 'form' => array(
				  'legend' => array(
						'title' => $this->l('Settings'),
						'icon' => 'icon-cogs'
				  ),
				  'input' => array(
						array(
							 'type' => 'text',
							 'label' => $this->l('Max product to display per filter page.'),
							 'name' => 'C3MAINFILTER_NB',
							 'class' => 'fixed-width-xs',
							 'desc' => $this->l('Set max number of product you would like to displayed per filter page. (default: 30)')
						)
				  ),
				  'submit' => array(
						'title' => $this->l('Generate Filter caches'),
				  )
			 ),
		);
		// setup form infos
		$helper = new HelperForm();
		// Module logic, token and currentIndex
		$helper->module = $this;
		$helper->table = $this->table;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&c3mainfilter_module=' . $this->tab . '&module_name=' . $this->name;
		// toolbar logic
		$helper->show_toolbar = false;
		// module langue
		$default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		// submit logic
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submit' . $this->name;


		$helper->tpl_vars = array(
			 'fields_value' => $this->getConfigFieldsValues(),
			 'languages' => $this->context->controller->getLanguages(),
			 'id_language' => $this->context->language->id
		);
		// generate form
		return $helper->generateForm(array($fields_form));
	}

	/*
	* Return config fields as array for backend form
	* 
	* @author Schnepp David
	* @since v0.1 2016/09/17
	* @return array the fields for backend form
	*/
	public function getConfigFieldsValues() {
		return array(
			 'C3MAINFILTER_NB' => Tools::getValue('C3MAINFILTER_NB', (int) Configuration::get('C3MAINFILTER_NB')),
		);
	}

}

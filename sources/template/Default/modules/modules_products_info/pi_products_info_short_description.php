<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class pi_products_info_short_description {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_short_description_name');
      $this->description = CLICSHOPPING::getDef('module_products_info_short_description_description');

      if (defined('MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

      if ($CLICSHOPPING_ProductsCommon->getID() && isset($_GET['Products']) ) {

        $content_width = (int)MMODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_CONTENT_WIDTH;
        $text_position = MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_POSITION;

        $CLICSHOPPING_Template = Registry::get('Template');

        $delete_word = (int)MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SHORT_DELETE_WORDS;
        $products_short_description_number = (int)MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SHORT_DESCRIPTION;
        $products_short_description = $CLICSHOPPING_ProductsCommon->getProductsShortDescription($CLICSHOPPING_ProductsCommon->getID(), $delete_word, $products_short_description_number );

        $products_short_description_content = '<!-- Start products hort description -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_info_short_description'));
        $products_short_description_content .= ob_get_clean();

        $products_short_description_content .= '<!-- end products short description -->' . "\n";

        $CLICSHOPPING_Template->addBlock($products_short_description_content, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_STATUS');
    }

    public function install() {
       $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MMODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'A quel endroit souhaitez-vous afficher le nom du produit ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Affiche le nom du produità gauche ou à droite<br><br><i>(Valeur Left = Gauche <br>Valeur Right = Droite <br>Valeur None = Aucun)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'right\', \'left\', \'none\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous indiquer le nombre de caractères dans la description courte ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SHORT_DESCRIPTION',
          'configuration_value' => '0',
          'configuration_description' => 'Veuillez indiquer la longueur de cette description.<br><br><i>- 0 pour aucune description<br>- 50 pour les 50 premiers caractères</i>',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le nombre de caratères que vous souhaitez supprimer au début du 1er paragraphe ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SHORT_DELETE_WORDS',
          'configuration_value' => '0',
          'configuration_description' => 'Si vous utilisez le module de gestion des onglets, ils peut etre intéressant de supprimer quelques mots.<br><br><i>- 0 pour aucune suppression<br>- 50 pour les 50 premiers caractères</i>',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SORT_ORDER',
          'configuration_value' => '121',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                              ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                            );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_STATUS',
        'MMODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_POSITION',
        'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SHORT_DESCRIPTION',
        'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SHORT_DELETE_WORDS',
        'MODULE_PRODUCTS_INFO_SHORT_DESCRIPTION_SORT_ORDER'
      );
    }
  }

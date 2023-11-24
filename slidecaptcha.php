<?php
/*
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * No redistribute in other sites, or copy.
 *
 * @author    RSI <rsi_2004@hotmail.com>
 * @copyright 2007-2015 RSI
 * @license   http://catalogo-onlinersi.net
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

ini_set('allow_url_fopen', true);

class SlideCaptcha extends Module
{
    public function __construct()
    {
        $this->name = 'slidecaptcha';
        if (_PS_VERSION_ < '1.4.0.0') {
            $this->tab = 'Blocks';
        }
        if (_PS_VERSION_ > '1.4.0.0' && _PS_VERSION_ < '1.5.0.0') {
            $this->tab = 'front_office_features';
            $this->author = 'RSI';
            $this->need_instance = 0;
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            $this->tab = 'front_office_features';
            $this->author = 'RSI';
            $this->bootstrap = true;
        }
        $this->version = '4.3.0';

        parent::__construct();
        $this->displayName = $this->l('SlideCaptcha');
        $this->description = $this->l('Block forms if the slide is not active - www.catalogo-onlinersi.net');
        if (_PS_VERSION_ < '1.5') {
            require(_PS_MODULE_DIR_ . $this->name . '/backward_compatibility/backward.php');
        }
    }

    public function install()
    {
        if (!Configuration::updateValue('SLIDECAPTCHA_SUBMITSC', 'true')
            || !parent::install()
            || !$this->registerHook('header')
            || (_PS_VERSION_ < "1.7.0.0" ? !$this->registerHook('footer') : !$this->registerHook('DisplayBeforeBodyClosingTag'))) {
            return false;
        }
        return true;
    }

    public function displayForm()
    {
        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages();
        $iso = Language::getIsoById($defaultLanguage);
        $divLangName = 'link_label';
        $this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="form">
		<fieldset><legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Settings') . '</legend>
			<label>' . $this->l('Auto submit') . '</label>
			<div class="margin-form">
				<select name="submitsc">		
				<option value="true"' . ((Configuration::get('SLIDECAPTCHA_SUBMITSC') == 'true') ? 'selected="selected"' : '') . '>yes</option>
				<option value="false"' . ((Configuration::get('SLIDECAPTCHA_SUBMITSC') == 'false') ? 'selected="selected"' : '') . '>no</option>					
				</select>
			</div>
			<p>' . $this->l('Put this code inside the contact-form.tpl of your theme (above the send button out from the div) or in any form that you need to block:
			') . '</p>			
			<input type="text" size="120" name="image1" value=\'{include file="$tpl_dir./../../modules/slidecaptcha/views/templates/front/slidecaptcha.tpl"}\' />
			<center><input type="submit" name="submitSlideCatpcha" value="' . $this->l('Save') . '" class="button" /></center><br/>
  			<center>	<a href="../modules/slidecaptcha/moduleinstall.pdf">README</a></center><br/>	
			<center>	<a href="../modules/slidecaptcha/termsandconditions.pdf">TERMS</center></a><br/>	
			 <center>  <p>Follow  us:</p></center>
     <center><p><a href="https://www.facebook.com/ShackerRSI" target="_blank"><img src="../modules/slidecaptcha/views/img/facebook.png" style="  width: 64px;margin: 5px;" /></a>
        <a href="https://twitter.com/prestashop_rsi" target="_blank"><img src="../modules/slidecaptcha/views/img/twitter.png" style="  width: 64px;margin: 5px;" /></a>
         <a href="https://www.pinterest.com/prestashoprsi/" target="_blank"><img src="../modules/slidecaptcha/views/img/pinterest.png" style="  width: 64px;margin: 5px;" /></a>
           <a href="https://plus.google.com/+shacker6/posts" target="_blank"><img src="../modules/slidecaptcha/views/img/googleplus.png" style="  width: 64px;margin: 5px;" /></a>
            <a href="https://www.linkedin.com/profile/view?id=92841578" target="_blank"><img src="../modules/slidecaptcha/views/img/linkedin.png" style="  width: 64px;margin: 5px;" /></a></p></center>
			<br/>
			<p>Video:</p>
			<iframe width="640" height="360" src="https://www.youtube.com/embed/VOiTwD--EkU?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe><br/>
			
			<p>Recommended:</p>
<object type="text/html" data="http://catalogo-onlinersi.net/modules/productsanywhere/images.php?idproduct=&desc=yes&buy=yes&type=home_default&price=yes&style=false&color=10&color2=40&bg=ffffff&width=800&height=310&lc=000000&speed=5&qty=15&skip=29,14,42,44,45&sort=1" width="800" height="310" style="border:0px #066 solid;"></object>
  		</fieldset>
  	</form>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<fieldset><legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Contribute') . '</legend>
			<p class="clear">' . $this->l('You can contribute with a donation if our free modules and themes are usefull for you. Clic on the link and support us!') . '</p>
			<p class="clear">' . $this->l('For more modules & themes visit: www.catalogo-onlinersi.net') . '</p>
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="HMBZNQAHN9UMJ">
			<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" width="1" height="1">
		</fieldset>
	</form>';
        return $this->_html;
    }

    public function getContent()
    {
        $errors = '';
        if (_PS_VERSION_ < '1.6.0.0') {
            if (Tools::isSubmit('submitSlideCatpcha')) {
                $submitsc = Tools::getValue('submitsc');
                Configuration::updateValue('SLIDECAPTCHA_SUBMITSC', $submitsc);
                $this->_html .= @$errors == '' ? $this->displayConfirmation('Settings updated successfully') : @$errors;
            }

            return $this->displayForm();
        } else {
            return $this->postProcess() . $this->_displayInfo() . $this->renderForm() . $this->_displayAdds();
        }
    }

    private function _displayInfo()
    {
        return $this->display(__FILE__, 'views/templates/hook/infos.tpl');
    }

    private function _displayAdds()
    {
        return $this->display(__FILE__, 'views/templates/hook/adds.tpl');
    }

    public static function getPageTypeByName($name)
    {
        $sql = 'SELECT id_page_type
				FROM ' . _DB_PREFIX_ . 'page_type
				WHERE name = \'' . pSQL($name) . '\'';
        if ($value = Db::getInstance()->getValue($sql)) {
            return $value;
        }

        //	Db::getInstance()->insert('page_type', array(
        //	'name' =>	$name,
        //	));
        return Db::getInstance()->Insert_ID();
    }

    public function hookHeader($params)
    {
        //var_dump(Context::getContext()->controller->php_self);
        $ptypee = Page::getCurrentId();
        $pety = $this->getPages($ptypee);
        foreach ($pety as $pet) {
            $petid = $pet['id_page'];
            $petnam = $pet['name'];
            $pettyp = $pet['id_page_type'];
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            $ptypee1 = Page::getPageTypeByName('contact');
            $ptypee2 = Page::getPageTypeByName('contact-form');
            $ptypee3 = Page::getPageTypeByName('contact-form.php');
        } else {
            $ptypee1 = SlideCaptcha::getPageTypeByName('contact');
            $ptypee2 = SlideCaptcha::getPageTypeByName('contact-form');
            $ptypee3 = SlideCaptcha::getPageTypeByName('contact-form.php');
        }


        $submitsc = Configuration::get('SLIDECAPTCHA_SUBMITSC');


        if (_PS_VERSION_ < '1.5.0.0') {
            Tools::addCSS(($this->_path) . 'views/css/QapTcha.jquery.css', 'all');
            Tools::addJS(($this->_path) . 'views/js/jquery-ui.js');
            Tools::addJS(($this->_path) . 'views/js/jquery.ui.touch.js');
            Tools::addJS(($this->_path) . 'views/js/QapTcha.jquery.js');
            $this->smarty->assign(array('submitsc' => $submitsc));
        } else {
            if (_PS_VERSION_ < '1.6.0.0') {
                $this->context->controller->addCSS(($this->_path) . 'views/css/QapTcha.jquery15.css', 'all');
            } else {
                $this->context->controller->addCSS(($this->_path) . 'views/css/QapTcha.jquery16.css', 'all');
            }

            if (_PS_VERSION_ > "1.5.0.0" && Context::getContext()->controller->php_self == 'contact' or Context::getContext()->controller->php_self == 'contact-form') {
                $this->context->controller->addJS(($this->_path) . 'views/js/jquery-ui.js');
                $this->context->controller->addJS(($this->_path) . 'views/js/jquery.ui.touch.js');
                $this->context->controller->addJS(($this->_path) . 'views/js/QapTcha.jquery.js');
            }
            $this->context->smarty->assign(array('submitsc' => $submitsc));
        }
        $this->smarty->assign(array('psversion' => _PS_VERSION_));

        return $this->display(__FILE__, 'views/templates/front/slidecaptcha-header.tpl');
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        return $this->hookFooter($params);
    }

    public function hookFooter($params)
    {
        $ptypee = Page::getCurrentId();
        $pety = $this->getPages($ptypee);
        foreach ($pety as $pet) {
            $petid = $pet['id_page'];
            $petnam = $pet['name'];
            $pettyp = $pet['id_page_type'];
        }
        if (_PS_VERSION_ > '1.5.0.0') {
            $ptypee1 = Page::getPageTypeByName('contact');
            $ptypee2 = Page::getPageTypeByName('contact-form');
            $ptypee3 = Page::getPageTypeByName('contact-form.php');
        } else {
            $ptypee1 = SlideCaptcha::getPageTypeByName('contact');
            $ptypee2 = SlideCaptcha::getPageTypeByName('contact-form');
            $ptypee3 = SlideCaptcha::getPageTypeByName('contact-form.php');
        }
        $submitsc = Configuration::get('SLIDECAPTCHA_SUBMITSC');


        if (_PS_VERSION_ < '1.5.0.0') {
            $this->context->smarty->assign(array('submitsc' => $submitsc));
        } else {

            $this->context->smarty->assign(array('pettyp' => $pettyp));
            $this->context->smarty->assign(array('ptypee1' => $ptypee1));
            $this->context->smarty->assign(array('ptypee2' => $ptypee2));
            $this->context->smarty->assign(array('ptypee3' => $ptypee3));
            $this->context->smarty->assign(array('ptypee' => $ptypee));
            $this->context->smarty->assign(array('submitsc' => $submitsc));

        }
        $this->context->smarty->assign(array('psversion' => _PS_VERSION_));

        if (_PS_VERSION_ > "1.5.0.0" && Context::getContext()->controller->php_self == 'contact' or Context::getContext()->controller->php_self == 'contact-form') {
            return $this->display(__FILE__, 'views/templates/front/slidecaptcha-footer.tpl');
        }
        if (_PS_VERSION_ < '1.5.0.0') {
            return $this->display(__FILE__, 'views/templates/front/slidecaptcha-footer.tpl');
        }


    }

    public function getPages($pety)
    {
        $result = Db::getInstance()->ExecuteS('
		SELECT pt.`id_page_type`,pt.`name`,p.`id_page_type`,p.`id_page`,p.`id_object`  
		FROM `' . _DB_PREFIX_ . 'page` p
		LEFT JOIN `' . _DB_PREFIX_ . 'page_type` pt ON p.`id_page_type` = pt.`id_page_type`
		WHERE p.`id_page` = ' . (int)$pety . ' LIMIT 1');
        return ($result);
    }

    public function postProcess()
    {
        $errors = '';
        $output = '';
        if (Tools::isSubmit('submitCoolshare')) {


            if ($submitsc = Tools::getValue('submitsc')) {
                Configuration::updateValue('SLIDECAPTCHA_SUBMITSC', $submitsc);
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::deleteFromContext('SLIDECAPTCHA_SUBMITSC');
            }


            $output .= $this->displayConfirmation($this->l('Settings updated.') . '<br/>');

            //if (!$errors)
            return $output;

        }
    }

    public function getConfigFieldsValues()
    {
        $fields_values = array(
            'submitsc' => Tools::getValue('submitsc', Configuration::get('SLIDECAPTCHA_SUBMITSC')),

        );
        return $fields_values;
    }

    public function renderForm()
    {
        $this->postProcess();

        $options1 = array(
            array(
                'id_option' => 'true',       // The value of the 'value' attribute of the <option> tag.
                'name' => 'yes'    // The value of the text content of the  <option> tag.
            ),
            array(
                'id_option' => 'false',
                'name' => 'no'
            ),

        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs',

                ),
                'description' => (_PS_VERSION_ < "1.7.0.0" ? $this->l('Put this code inside the contact-form.tpl of your theme (above the send button out from the div) or in any form that you need to block:
			') . '{include file="$tpl_dir./../../modules/slidecaptcha/views/templates/front/slidecaptcha.tpl"}' : $this->l('Put this code inside the themes/classic/modules/contactform/views/templates/widget/contactform.tpl of your theme (above the send button out from the div) or in any form that you need to block:
			') . '{include file="../../../../../../../modules/slidecaptcha/views/templates/front/slidecaptcha.tpl"}'),

                'input' => array(


                    array(
                        'type' => 'select',
                        'label' => $this->l('Auto submit'),
                        'name' => 'submitsc',
                        'options' => array(
                            'query' => $options1,
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),


                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )

            ),


        );
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCoolshare';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules',
                false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }
}
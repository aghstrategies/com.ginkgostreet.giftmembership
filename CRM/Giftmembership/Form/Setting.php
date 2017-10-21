<?php

use CRM_Giftmembership_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Giftmembership_Form_Setting extends CRM_Core_Form {

  private $settingNameSoftCreditTypes = 'giftmembership_soft_credit_types';
  private $settingMetadataSoftCreditTypes = array();

  public function setDefaultValues() {
    $defaults = array();
    $typeIds = Civi::settings()->get('giftmembership_soft_credit_types');

    $defaults[$this->settingNameSoftCreditTypes] = array_fill_keys($typeIds, 1);

    return $defaults;
  }

  public function buildQuickForm() {
    $metadata = $this->getSettingMetadataSoftCreditTypes();
    $this->addCheckBox($metadata['name'], $metadata['title'], $metadata['options']);

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementMetadata());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $softCreditTypes = CRM_Utils_Array::value($this->settingNameSoftCreditTypes, $values, array());

    civicrm_api3('Setting', 'create', array(
      $this->settingNameSoftCreditTypes => array_keys($softCreditTypes),
    ));

    CRM_Core_Session::setStatus(E::ts('Settings saved.'), NULL, 'success');

    parent::postProcess();
  }

  /**
   * Wraps api.Setting.getfields. Adds 'options' array element with field
   * options represented as value => label.
   *
   * @return array
   */
  protected function getSettingMetadataSoftCreditTypes() {
    if (empty($this->settingMetadataSoftCreditTypes)) {
      $result = civicrm_api3('Setting', 'getfields', array(
        'filters' => array('name' => $this->settingNameSoftCreditTypes),
      ));
      $this->settingMetadataSoftCreditTypes = $result['values'][$this->settingNameSoftCreditTypes];

      $options = civicrm_api3('ContributionSoft', 'getoptions', array(
        'field' => 'soft_credit_type_id',
      ));
      $this->settingMetadataSoftCreditTypes['options'] = array_flip($options['values']);
    }

    return $this->settingMetadataSoftCreditTypes;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementMetadata() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNameDescription = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $fieldName = $element->getName();
        $metadata = $this->getSettingMetadataSoftCreditTypes();
        $elementNameDescription[$fieldName] = $metadata['description'];
      }
    }
    return $elementNameDescription;
  }

}

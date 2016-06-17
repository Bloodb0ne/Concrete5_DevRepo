<?php

namespace Concrete\Package\IconSelector\Attribute\IconSelector;

use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Core;
use Database;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Log;

class Controller extends AttributeTypeController{

	private $akAllowMultipleValues;


	protected $searchIndexFieldDefinition = array('type' => 'string', 'options' => array('default' => null, 'notnull' => false));

	public function load(){
		//Load options and settings

        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $db = Database::get();
        $row = $db->GetRow('select akAllowMultipleValues from atIconSelectorSettings where akID = ?', array($ak->getAttributeKeyID()));
        $this->akAllowMultipleValues = $row ? $row['akAllowMultipleValues'] : null;

        $this->set('akAllowMultipleValues',$this->akAllowMultipleValues);
	}

    public function type_form()
    {
        $this->set('form', Core::make('helper/form'));
        $this->load();

        if ($this->isPost()) {
            $akSelectValues = $this->getSelectValuesFromPost();
            $this->set('akSelectValues', $akSelectValues);
        } elseif (isset($this->attributeKey)) {
            $options = $this->getOptions();
            $this->set('akSelectValues', $options);
        } else {
            $this->set('akSelectValues', array());
        }
        $this->requireAsset('core/file-manager');
    }

    public function form()
    {
        $this->load();
        $options = $this->getSelectedOptions();
        $selectedOptions = array();
        $selectedOptionValues = array();
        foreach ($options as $opt) {
            $selectedOptions[] = $opt->getIconSelectAttributeOptionID();
            $selectedOptionValues[$opt->getIconSelectAttributeOptionID()] = $opt->getIconSelectAttributeOptionValue();
            // $selectedOptionIcons[$opt->getIconSelectAttributeOptionID()] = \File::getByID($opt->getIconSelectAttributeOptionImageID());
        }
        $this->set('selectedOptionValues', $selectedOptionValues);
        $this->set('selectedOptions', $selectedOptions);
        $this->requireAsset('jquery/ui');
        $this->requireAsset('select2');
    }

    public function search(){

    }

    public function deleteValue(){
    	$db = Database::get();
        $db->Execute('delete from atIconSelectorSelected where avID = ?', array($this->getAttributeValueID()));
    }

    public function deleteKey(){
    	$db = Database::get();
        $db->Execute('delete from atIconSelectorSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
        $r = $db->Execute('select oid from atIconSelectorOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
        while ($row = $r->FetchRow()) {
            $db->Execute('delete from atIconSelectorSelected where optionID = ?', array($row['oid']));
        }
        $db->Execute('delete from atIconSelectorOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
    }

    public function saveValue($value){

    	$db = Database::get();
        $this->load();
        $options = array();

        if (is_array($value) && $this->akAllowMultipleValues) {
            foreach ($value as $v) {
                $opt = Option::getByValue($v, $this->attributeKey);
                if (is_object($opt)) {
                    $options[] = $opt;
                }
            }
        } else {
            if (is_array($value)) {
                $value = $value[0];
            }

            $opt = Option::getByValue($value, $this->attributeKey);

            if (is_object($opt)) {
                $options[] = $opt;
            }
        }

        $db->Execute('delete from atIconSelectorSelected where avID = ?', array($this->getAttributeValueID()));
        if (count($options) > 0) {
            foreach ($options as $opt) {
                $db->Execute('insert into atIconSelectorSelected (avID, optionID) values (?, ?)', array($this->getAttributeValueID(), $opt->getIconSelectAttributeOptionID()));
                if ($this->akAllowMultipleValues == false) {
                    break;
                }
            }
        }
    }

    public function getDisplayValue(){
    	$list = $this->getSelectedOptions();
        $html = '';
        foreach ($list as $l) {
            $html .= $l->getSelectAttributeOptionDisplayValue() . '<br/>';
        }

        return $html;
    }

    public function getDisplaySanitizedValue(){
    	return $this->getDisplayValue();
    }

    public function validateValue(){ return true;//is_object($value = $this->getValue()) && ((string) $value != ''); 
}
    public function validateForm(){

    	$this->load();
        $options = $this->request('atIconSelectOptionID');
        if (!is_array($options)) {
            $options = array();
        }
       	
        if ($this->akAllowMultipleValues) {
            return count($options) > 0;
        } else {
            if ($options[0] != false) {
                return $options[0] > 0;
            }
        }

        return false;
    }
    public function saveForm($data){
    	$db = Database::get();

		$this->load();
		if (is_array($data['atIconSelectOptionID'])) {
		    $data['atIconSelectOptionID'] = array_unique($data['atIconSelectOptionID']);
		}

		$db->Execute('delete from atIconSelectorSelected where avID = ?', array($this->getAttributeValueID()));
		if (is_array($data['atIconSelectOptionID'])) {
		    foreach ($data['atIconSelectOptionID'] as $optID) {
		        if ($optID > 0) {
		            $db->Execute('insert into atIconSelectorSelected (avID, optionID) values (?, ?)', array($this->getAttributeValueID(), $optID));
		            if ($this->akAllowMultipleValues == false) {
		                break;
		            }
		        }
		    }
		}
    }

    public function getValue(){
    	$list = $this->getSelectedOptions();

        return $list;
    }
    
    public function getSearchIndexValue()
    {
        $str = "\n";
        $list = $this->getSelectedOptions();
        foreach ($list as $l) {
            $l = (is_object($l) && method_exists($l, '__toString')) ? $l->__toString() : $l;
            $str .= $l . "\n";
        }
        // remove line break for empty list
        if ($str == "\n") {
            return '';
        }

        return $str;
    }

    public function getSelectedOptions(){
    	$db = Database::get();
    	$options = $db->GetAll("select oid, optIcon,optValue from atIconSelectorSelected selected inner join atIconSelectorOptions opt on selected.optionID = opt.oid where avID = ?", array($this->getAttributeValueID()));

        $db = Database::get();
        $list = new OptionList();
        foreach ($options as $row) {
            $opt = new Option($row['oid'], $row['optValue'],$row['optIcon']);
            $list->add($opt);
        }

        return $list;
    }
    public function getSelectValuesFromPost(){
    	$options = new OptionList();
        $displayOrder = 0;
        foreach ($_POST['option'] as $key => $value) {
            
            $id = $key;
            // now we determine from the post whether this is a new option
            // or an existing. New ones have this value from in the akSelectValueNewOption_ post field
            if ($value['new'] == 1) {
                $opt = new Option(0, $value['optValue'],$value['optIcon']);
                $opt->tempID = $id;
            } else {
                $opt = new Option($id, $value['optValue'],$value['optIcon']);
            }

            if (is_object($opt)) {
                $options->add($opt);
            }
        }

        return $options;
    }

    //Ajax loading of options
    public function action_load_autocomplete_values(){
    	$this->load();
        $values = array();
            // now, if the current instance of the attribute key allows us to do autocomplete, we return all the values
        if ($this->akSelectAllowMultipleValues) {
            $options = $this->getOptions($_GET['term'] . '%');
            foreach ($options as $opt) {
                $values[] = $opt->getSelectAttributeOptionValue(false);
            }
        }
        print json_encode($values);
    }

    public function filterByAttribute(AttributedItemList $list, $value, $comparison = '=')
    {
        if ($value instanceof Option) {
            $option = $value;
        } else {
            $option = Option::getByValue($value);
        }
        if (is_object($option)) {
            $column = 'ak_' . $this->attributeKey->getAttributeKeyHandle();
            $qb = $list->getQueryObject();
            $qb->andWhere(
                $qb->expr()->like($column, ':iconOptionValue')
            );
            $qb->setParameter('iconOptionValue', "%\n" . $option->getIconSelectAttributeOptionValue() . "\n%");
        }
    }

    //Gets All options
    public function getOptions($like = null){
    	
        $this->load();
        
        $db = Database::get();
        if (isset($like) && strlen($like)) {
            $r = $db->Execute('select oid, optValue, optIcon from atIconSelectorOptions where akID = ? AND atIconSelectorOptions.value LIKE ?', array($this->attributeKey->getAttributeKeyID(), $like));
        } else {
            $r = $db->Execute('select oid, optValue, optIcon from atIconSelectorOptions where akID = ? ', array($this->attributeKey->getAttributeKeyID()));
        }
		$options = new OptionList();
        while ($row = $r->FetchRow()) {
            $opt = new Option($row['oid'], $row['optValue'], $row['optIcon']);
            $options->add($opt);
        }

        return $options;
    }
    public function saveKey($data){
    	$ak = $this->getAttributeKey();

        $db = Database::get();

        $initialOptionSet = $this->getOptions();
        $selectedPostValues = $this->getSelectValuesFromPost();
		if (isset($data['akAllowMultipleValues']) && ($data['akAllowMultipleValues'] == 1)) {
		    $akAllowMultipleValues = 1;
		} else {
		    $akSelectAllowMultipleValues = 0;
		}

        $db->Replace('atIconSelectorSettings', array(
            'akID' => $ak->getAttributeKeyID(),
            'akAllowMultipleValues' => $akAllowMultipleValues,
        ), array('akID'), true);

        // Now we add the options
        $newOptionSet = new OptionList();
        $displayOrder = 0;
        foreach ($selectedPostValues as $option) {
            $opt = $option->saveOrCreate($ak);
            
            $newOptionSet->add($opt);
            $displayOrder++;
        }

        // Now we remove all options that appear in the
        // old values list but not in the new
        foreach ($initialOptionSet as $iopt) {
            if (!$newOptionSet->contains($iopt)) {
                $iopt->delete();
            }
        }
    }

    public function getAllowMultipleValues()
    {
        if (is_null($this->akAllowMultipleValues)) {
            $this->load();
        }

        return $this->akAllowMultipleValues;
    }
    public function duplicateKey($newAK){}
    public function exportKey($akey){}
    public function exportValue(\SimpleXMLElement $akn){}
    public function importValue(\SimpleXMLElement $akv){}
    public function importKey($akey){}



}
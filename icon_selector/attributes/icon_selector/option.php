<?php
namespace Concrete\Package\IconSelector\Attribute\IconSelector;
use Gettext\Translations;
use Loader;
use \Concrete\Core\Foundation\Object;

class Option extends Object {

	public function __construct($oid, $optValue, $optIcon) {
		$this->oid = $oid;
		$this->optValue = $optValue;
		$this->optIcon = $optIcon;
		$this->th = Loader::helper('text');
	}
	
	public function getIconSelectAttributeOptionID() {return $this->oid;}
	public function getIconSelectAttributeOptionImageID(){ return $this->optIcon; }
	public function getIconSelectAttributeOptionValue($sanitize = true) {
		if (!$sanitize) {
			return $this->optValue;
		} else {
			return $this->th->specialchars($this->optValue);
		}
	}
	/** Returns the display name for this select option value (localized and escaped accordingly to $format)
	* @param string $format = 'html'
	*	Escape the result in html format (if $format is 'html').
	*	If $format is 'text' or any other value, the display name won't be escaped.
	* @return string
	*/
	public function getSelectAttributeOptionDisplayValue($format = 'html') {
		$value = tc('SelectAttributeValue', $this->getIconSelectAttributeOptionValue(false));
		switch($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}

	public function getSelectAttributeOptionTemporaryID() {return $this->tempID;}
	
	public function __toString() {return $this->optValue;}
	
	public static function add($ak, $optValue, $optIcon) {
		$db = Loader::db();
		$th = Loader::helper('text');
		
		$displayOrder = $db->GetOne('select count(oid) from atIconSelectorOptions where akID = ?', array($ak->getAttributeKeyID()));			

		$v = array($ak->getAttributeKeyID(), $th->sanitize($optValue),$optIcon);
		$db->Execute('insert into atIconSelectorOptions (akID, optValue, optIcon) values (?, ?, ?)', $v);
		
		return Option::getByID($db->Insert_ID());
	}
	
	public function setDisplayOrder($num) {
		$db = Loader::db();
		$db->Execute('update atIconSelectorOptions set displayOrder = ? where oid = ?', array($num, $this->oid));
	}
	
	public static function getByID($id) {
		$db = Loader::db();
		$row = $db->GetRow("select oid,optValue, optIcon from atIconSelectorOptions where oid = ?", array($id));
		if (isset($row['oid'])) {
			$obj = new Option($row['oid'], $row['optValue'], $row['displayOrder']);
			return $obj;
		}
	}
	
	public static function getByValue($optValue, $ak = false) {
		$db = Loader::db();
		if (is_object($ak)) {
			$row = $db->GetRow("select oid, optIcon, optValue from atIconSelectorOptions where optValue = ? and akID = ?", array($optValue, $ak->getAttributeKeyID()));
		} else {
			$row = $db->GetRow("select oid, optIcon, optValue from atIconSelectorOptions where optValue = ?", array($optValue));
		}
		if (isset($row['oid'])) {
			$obj = new Option($row['oid'], $row['optValue'], $row['displayOrder']);
			return $obj;
		}
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from atIconSelectorOptions where oid = ?', array($this->oid));
		$db->Execute('delete from atIconSelectorSelected where optionID = ?', array($this->oid));
	}
	
	public function saveOrCreate($ak) {
		if ($this->tempID != false || $this->oid==0) {
			return Option::add($ak, $this->optValue, $this->optIcon);
		} else {
			$db = Loader::db();
			$th = Loader::helper('text');
			$db->Execute('update atIconSelectorOptions set optValue = ? where oid = ?', array($th->sanitize($this->optValue), $this->oid));
			return Option::getByID($this->oid);
		}
	}

    public static function exportTranslations()
    {
        $translations = new Translations();
        $db = \Database::get();
        $r = $db->Execute('select oid from atIconSelectorOptions order by oid asc');
        while ($row = $r->FetchRow()) {
            $opt = static::getByID($row['oid']);
            $translations->insert('IconSelectAttributeValue', $opt->getIconSelectAttributeOptionValue());
        }
        return $translations;
    }

}
<?php
namespace Application\Block\MixedContent;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;

class Controller extends BlockController
{
    protected $btTable = 'btMixedContent';
    protected $btInterfaceWidth = "600";
    protected $btInterfaceHeight = "465";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btSupportsInlineEdit = true;
    protected $btSupportsInlineAdd = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputLifetime = 0; //until manually updated or cleared

    public function getBlockTypeDescription()
    {
        return t("Mixed Content with images and text.");
    }

    public function getBlockTypeName()
    {
        return t("Mixed Content");
    }

    public function getContent()
    {
        return LinkAbstractor::translateFrom($this->content);
    }

    public function getLeftImage(){
        if($this->image_left)
            return $this->image_left;
        else
            return 0;
    }

    public function getRightImage(){
        if($this->image_right)
            return $this->image_right;
        else
            return 0;
    }

    public function getViewType(){
        return $this->view_type;
    }
    public function getSearchableContent()
    {
        return $this->content;
    }

    public function br2nl($str)
    {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("<br />\n", "\n", $str);

        return $str;
    }


    public function view()
    {
        $this->set('content', $this->getContent());
        $this->set('image_left',$this->getLeftImage());
        $this->set('image_right',$this->getRightImage());
    }

    public function getContentEditMode()
    {
        return LinkAbstractor::translateFromEditMode($this->content);
    }


    public function save($args)
    {
        $args['content'] = LinkAbstractor::translateTo($args['content']);
        //Images are passed as-is
        parent::save($args);
    }
}

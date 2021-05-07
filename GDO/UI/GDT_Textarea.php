<?php
namespace GDO\UI;

/**
 * A textarea is like a GDT_Message without editor. 
 * @author gizmore
 * @version 6.10.2
 */
class GDT_Textarea extends GDT_Message
{
    ##############
    ### Editor ###
    ##############
    public $nowysiwyg = true;
    public function classEditor() { return 'as-is'; }
    
}

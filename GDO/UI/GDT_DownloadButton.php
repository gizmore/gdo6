<?php
namespace GDO\UI;

/**
 * A download button with label and icon.
 * Adds gdt-download class.
 * @author gizmore
 * @version 6.10.1
 * @since 6.10.1
 */
final class GDT_DownloadButton extends GDT_Button
{
    public function defaultLabel() { return $this->label('btn_download'); }
    
    public function name($name=null)
    {
        return $name ? parent::name($name) : $this;
    }
    
    protected function __construct()
    {
        parent::__construct();
        $this->name = "download";
        $this->icon('download');
        $this->addClass('gdt-download-button');
    }
    
}

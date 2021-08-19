<?php
namespace GDO\Core;

use GDO\DB\GDT_Char;

/**
 * GUID Datatype and generator.
 * Optionally set an automatic initial guid.
 *
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.4
 */
final class GDT_GUID extends GDT_Char
{
    const LENGTH = 36;

    public static function create()
    {
        return vsprintf('%s%s-%s-4000-8%.3s-%s%s%s',
            str_split(dechex(microtime(true) * 1000) .
                bin2hex(random_bytes(8)),4));
    }

    ###########
    ### GDT ###
    ###########
    public function defaultLabel() { return $this->label('guid'); }

    protected function __construct()
    {
        parent::__construct();
        $this->length(self::LENGTH);
        $this->ascii();
        $this->caseI();
//         $this->notNull();
//         $this->initial(self::create());
    }

    public function blankData()
    {
        if ($this->initialGUID)
        {
            return [$this->name => self::create()];
        }
    }

    #################
    ### Auto init ###
    #################
    public $initialGUID = false;
    public function initialGUID($initialGUID=true)
    {
        $this->initialGUID = $initialGUID;
        return $this;
    }
}

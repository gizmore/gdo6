<?php
namespace GDO\Core;

use GDO\Util\Common;
use GDO\Table\Module_Table;
use GDO\UI\GDT_SearchField;

/**
 * Generic autocompletion base code.
 * Override 1 methods for self implemented completion.
 *
 * @author gizmore
 * @version 6.10
 * @since 6.03
 *
 * @see GDT_Table
 */
abstract class MethodCompletion extends MethodAjax
{
    public function gdoParameters()
    {
        return [
            GDT_SearchField::make('query')->notNull(),
        ];
    }

    #############
    ### Input ###
    #############
	public function getSearchTerm() { return trim(Common::getRequestString('query'), "\r\n\t "); }
	public function getMaxSuggestions() { return Module_Table::instance()->cfgSuggestionsPerRequest(); }

// 	############
// 	### Exec ###
// 	############
// 	/**
// 	 * The json should return id, text, display.
// 	 */
// 	public abstract function execute();

}

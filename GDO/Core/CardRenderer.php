<?php
namespace GDO\Core;

use GDO\UI\GDT_Card;
use GDO\DB\GDT_CreatedBy;
use GDO\DB\GDT_String;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_DeletedBy;
use GDO\DB\GDT_DeletedAt;
use GDO\DB\GDT_EditedBy;
use GDO\DB\GDT_EditedAt;
use GDO\DB\GDT_CreatedAt;

/**
 * Default renderer approach.
 * First String field is title.
 * Creator subtitle is auto generated.
 * Rest fields are automatically put into the card.
 *
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class CardRenderer
{
    public static function render(GDO $gdo, ...$action)
    {
        return self::build($gdo, ...$action)->render();
    }

    public static function build(GDO $gdo, $withCreateHeader=false, $withEditedfooter=false, ...$action)
    {
        $card = GDT_Card::make('card-'.$gdo->getID())->gdo($gdo);

        $used = [];

        if ($autoIncColumn = $gdo->gdoColumnOf(GDT_AutoInc::class))
        {
            $used[] = $autoIncColumn;
        }

        if ($titleColumn = $gdo->gdoColumnOf(GDT_String::class))
        {
            $card->title($titleColumn);
            $used[] = $titleColumn;
        }

        if ($creatorColumn = $gdo->gdoColumnOf(GDT_CreatedBy::class))
        {
            $card->creatorHeader();
            $used[] = $creatorColumn;
            if ($createdColumn = $gdo->gdoColumnOf(GDT_CreatedAt::class))
            {
                $used[] = $createdColumn;
            }
//             $user = $creatorColumn->getUser();
        }

        if ($withEditedfooter)
        {
            if ($gdo->gdoColumnOf(GDT_EditedBy::class))
            {
                if ($gdo->gdoColumnOf(GDT_EditedAt::class))
                {
                    $card->editorFooter();
                }
            }
        }

        $used[] = GDT_DeletedBy::class;
        $used[] = GDT_DeletedAt::class;
        $used[] = GDT_EditedBy::class;
        $used[] = GDT_EditedAt::class;

        foreach ($gdo->gdoColumnsCache() as $gdt)
        {
            if (!in_array($gdt, $used, true))
            {
                $card->addField($gdt->gdo($gdo));
            }
        }

        $card->actions()->addFields($action);

        return $card;
    }

}

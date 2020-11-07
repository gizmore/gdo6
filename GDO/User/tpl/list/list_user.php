<?php
use GDO\UI\GDT_ListItem;
use GDO\Avatar\GDT_Avatar;
use GDO\User\GDO_User;
use GDO\Profile\GDT_ProfileLink;
use GDO\UI\GDT_Label;
use GDO\User\GDO_UserSetting;
/** @var $user GDO_User **/

$list = GDT_ListItem::make('user-'.$user->getID());

$list->avatar(GDT_Avatar::make()->user($user));
$list->title(GDT_ProfileLink::make()->forUser($user)->withNickname());

$list->subtitle(GDT_Label::make()->label('user_subtitle', [$user->displayRegisterAge(), $user->getLevel()]));

echo $list->render();

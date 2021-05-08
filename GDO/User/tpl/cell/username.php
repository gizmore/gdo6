<?php
use GDO\User\GDO_User;
/**
 * @var $user GDO_User
 */
$id = $user->getID();

# Beautify markup and typography
# As it's a template you might want to add colors for permissions, a link to your profile or whatever.
if ($user->isGhost())
{
	printf('<gdo-user id="%s" nickname="~GHOST~">~GUEST~</gdo-user>', $id, $user->getName());
}
elseif ($realname = $user->getRealName())
{
	printf('<gdo-user id="%s" nickname="%s">\'%s\'</gdo-user>', $id, $user->getName(), htmlspecialchars($realname));
}
elseif ($guestname = $user->getGuestName())
{
	printf('<gdo-user id="%s" nickname="~GUEST~">~%s~</gdo-user>', $id, htmlspecialchars($guestname));
}
else 
{
	printf('<gdo-user id="%s" nickname="%s">%2$s</gdo-user>', $id, $user->getName());
}

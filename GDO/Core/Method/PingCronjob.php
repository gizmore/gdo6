<?php
namespace GDO\Core\Method;

use GDO\Cronjob\MethodCronjob;

/**
 * This cronjob is merely there
 * to create the cronjob permission early in testing
 * and on all sites.
 *
 * @author gizmore
 * @version 6.10.1
 * @since 6.10.1
 */
final class PingCronjob extends MethodCronjob
{
    public function run()
    {
        $this->log("Cronjobs started...");
    }

}

<?php
namespace GDO\Cronjob\Method;

use GDO\Cronjob\MethodCronjob;

/**
 * This cronjob is merely there
 * to create the cronjob permission early in testing
 * and on all sites.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.1
 */
final class Ping extends MethodCronjob
{
    public function run()
    {
        $this->log("Cronjobs started...");
    }

}

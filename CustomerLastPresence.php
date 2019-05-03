<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CustomerLastPresence;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\Finder\Finder;
use Thelia\Install\Database;
use Thelia\Module\BaseModule;

class CustomerLastPresence extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'customerlastpresence';

    /* @var string */
    const UPDATE_PATH = __DIR__ . DS . 'Config' . DS . 'update';

    const SESSION_LAST_CONNECTION_REGISTER_KEY = 'last_connection_register_time';

    /**
     * @param ConnectionInterface $con
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        if (!$this->getConfigValue('is_initialized', false)) {
            $database = new Database($con);
            $database->insertSql(null, [__DIR__ . "/Config/thelia.sql"]);
            $this->setConfigValue('is_initialized', true);
        }
    }

    /**
     * @param string $currentVersion
     * @param string $newVersion
     * @param ConnectionInterface $con
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     * @since 1.2.3
     */
    public function update($currentVersion, $newVersion, ConnectionInterface $con = null)
    {
        $finder = (new Finder())->files()->name('#.*?\.sql#')->sortByName()->in(self::UPDATE_PATH);

        if ($finder->count() === 0) {
            return;
        }

        $database = new Database($con);

        /** @var \Symfony\Component\Finder\SplFileInfo $updateSQLFile */
        foreach ($finder as $updateSQLFile) {
            if (version_compare($currentVersion, str_replace('.sql', '', $updateSQLFile->getFilename()), '<')) {
                $database->insertSql(null, [$updateSQLFile->getPathname()]);
            }
        }
    }
}

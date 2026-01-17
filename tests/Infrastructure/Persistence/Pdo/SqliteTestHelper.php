<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class SqliteTestHelper {

    private static ?PDO $connection = null;

    public static function createConnection(): PDO {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $configArray = require __DIR__ . '/../../../../phinx.php';
        $rootPath = dirname(__DIR__, 4);

        array_walk_recursive($configArray, function (&$value) use ($rootPath) {
            if (is_string($value)) {
                $value = str_replace('%%PHINX_CONFIG_DIR%%', $rootPath, $value);
            }
        });

        $config = new Config($configArray);
        $manager = new Manager($config, new StringInput(''), new NullOutput());
        $manager->migrate('testing');

        $pdo = $manager->getEnvironment('testing')->getAdapter()->getConnection();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::$connection = $pdo;
        return self::$connection;
    }
}
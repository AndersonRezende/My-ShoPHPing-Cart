<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class MysqlTestHelper {

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

        $testEnvConfig = $configArray['environments']['mysql_testing'];
        self::ensureDatabaseExists($testEnvConfig);

        $config = new Config($configArray);
        $manager = new Manager($config, new StringInput(''), new NullOutput());

        $manager->rollback('mysql_testing', 0);
        $manager->migrate('mysql_testing');

        $pdo = $manager->getEnvironment('mysql_testing')->getAdapter()->getConnection();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::$connection = $pdo;
        return self::$connection;
    }

    private static function ensureDatabaseExists(array $dbConfig): void
    {
        try {
            $pdo = new PDO(
                "mysql:host={$dbConfig['host']};port={$dbConfig['port']}",
                $dbConfig['user'],
                $dbConfig['pass']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['name']}` CHARACTER SET {$dbConfig['charset']}");
        } catch (\PDOException $e) {
            throw new \RuntimeException("Could not create test database: " . $e->getMessage(), 0, $e);
        }
    }
}

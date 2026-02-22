#!/bin/sh
set -e

# Função para esperar o banco de dados
wait_for_db() {
    echo "Aguardando banco de dados..."

    # Adicionado -d xdebug.mode=off para não travar o boot no debugger
    until php -d xdebug.mode=off -r "
        \$dbUrl = getenv('DATABASE_URL');
        if (!\$dbUrl) {
            fwrite(STDERR, 'DATABASE_URL não definida' . PHP_EOL);
            exit(1);
        }

        \$parts = parse_url(\$dbUrl);
        \$dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            \$parts['scheme'],
            \$parts['host'],
            \$parts['port'] ?? '3306',
            ltrim(\$parts['path'], '/')
        );

        try {
            \$pdo = new PDO(
                \$dsn,
                \$parts['user'] ?? null,
                \$parts['pass'] ?? null,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            \$pdo->query('SELECT 1');
            exit(0);
        } catch (PDOException \$e) {
            fwrite(STDERR, 'Erro: ' . \$e->getMessage() . PHP_EOL);
            exit(1);
        }
    "; do
        echo "Banco indisponível - aguardando..."
        sleep 2
    done
    echo "Banco de dados está pronto!"
}

# Esperar o banco subir
wait_for_db

# Rodar Migrations (também sem debug)
echo "Rodando migrations..."
php -d xdebug.mode=off vendor/bin/phinx migrate

# Iniciar o comando original (php-fpm)
exec "$@"

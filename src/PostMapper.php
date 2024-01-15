<?php

namespace Jekamars\BlogPhp;

use PDO;

class PostMapper
{
    /**
     * @param PDO $connection
     */
    public function __construct(private PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $urlKey
     * @return array|null
     */
    public function getByUrlKey(string $urlKey): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM post WHERE url_key = :url_key');
        $statement->execute([
            'url_key' => $urlKey,
        ]);
        $result = $statement->fetchAll();

        return array_shift($result);
    }
}
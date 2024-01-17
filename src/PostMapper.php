<?php

namespace Jekamars\BlogPhp;

use Exception;
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

    public function getPosts(string $sort, ?int $page = null, ?int $limit = null): ?array
    {
        if (!in_array($sort, ['DESC', 'ASC'])) {
            throw new Exception('This sort is not supported.');
        }

        $start = ($page - 1) * $limit;

        if ($page != 0) {
            $statement = $this->connection->prepare('SELECT * FROM post ORDER BY published ' . $sort . ' LIMIT ' . $start . ',' . $limit);
        } else {
            $statement = $this->connection->prepare('SELECT * FROM post ORDER BY published ' . $sort);
        }

        $statement->execute();
        return $statement->fetchAll();
    }

    public function getLatestPosts(int $county): ?array
    {
        $posts = $this->getPosts('DESC');
        return array_slice($posts, 0, $county);
    }

    public function getTotalCount(): int
    {
        $statement = $this->connection->prepare('SELECT count(id) as total FROM post');
        $statement->execute();
        return (int)($statement->fetchColumn() ?? 0);
    }
}
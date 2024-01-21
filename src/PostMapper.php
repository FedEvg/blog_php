<?php

namespace Jekamars\BlogPhp;

use Exception;

class PostMapper
{
    private Database $database;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param string $urlKey
     * @return array|null
     */
    public function getByUrlKey(string $urlKey): ?array
    {
        $statement = $this->database->getConnection()->prepare('SELECT * FROM post WHERE url_key = :url_key');
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
            $statement = $this->database->getConnection()->prepare('SELECT * FROM post ORDER BY published ' . $sort . ' LIMIT ' . $start . ',' . $limit);
        } else {
            $statement = $this->database->getConnection()->prepare('SELECT * FROM post ORDER BY published ' . $sort);
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
        $statement = $this->database->getConnection()->prepare('SELECT count(id) as total FROM post');
        $statement->execute();
        return (int)($statement->fetchColumn() ?? 0);
    }
}
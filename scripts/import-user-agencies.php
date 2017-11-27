<?php

require __DIR__ . '/../../../autoload.php';

$conn = new \PDO('mysql:host=argus-db.sr-ver.de;dbname=argus', 'lotus_app', 'DZr4KslG69IcASqYNj7Y');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->query('SET NAMES utf8');

$stmt = $conn->query(<<<SQL
    SELECT
      id,
      name
    FROM user
    WHERE TRUE
      AND `delete` = 0
      AND blocked = 0
      -- limit 10

SQL
);

$stmtAgents = $conn->prepare(<<<SQL
    SELECT agent_id FROM user_agent WHERE user_id = ?
SQL
);

$config = [
    'storage' => 'ApiGateway\Cache\Storage\MongoDb',
    'uri' => 'mongodb://core-web1.sr-ver.de:27017,core-web2.sr-ver.de:27017/api_gateway_cache?replicaSet=core-web',
    #'uri' => 'mongodb://172.16.0.3',
    'database' => 'api_gateway_cache',
];

//$client = new \MongoDB\Client($config['uri']);
$mongoManager = new \MongoDB\Driver\Manager($config['uri']);
$bulk = new \MongoDB\Driver\BulkWrite();

while ($user = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $stmtAgents->execute([
        $user['id'],
    ]);

    $user['agents'] = $stmtAgents->fetchAll(\PDO::FETCH_COLUMN);

    $bulk->insert($user);
}

$mongoManager->executeBulkWrite('api_gateway_cache.user_agent', $bulk);

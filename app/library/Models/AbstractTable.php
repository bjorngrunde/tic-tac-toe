<?php

namespace App\Models;

use App\Core\Database\Database;
use App\Core\Database\DatabaseInterface;

abstract class AbstractTable
{
    private DatabaseInterface $db;

    abstract protected function getTableName(): string;

    public function __construct()
    {
        $this->db = Database::getDatabase();
    }

    /**
     * Added a distinct field so we can count one specific user per grid_size.
     * 
     * @param string[] $whereConditions
     * @param array $bindParameters
     * @return int
     */
    public function getCount(array $whereConditions = [], array $bindParameters = [], string $distinctField = ''): int
    {
        $sql = '';

        if (!empty($distinctField)) {
            $sql = "
                SELECT COUNT(DISTINCT {$distinctField}) AS count
                FROM {$this->getTableName()}
        ";
        } else {

            $sql = "
                SELECT COUNT(*) AS count
                FROM {$this->getTableName()}
            ";
        }

        if ($whereConditions) {
            $sql .= "\n WHERE \n";
            $sql .= implode(' AND ', $whereConditions);
        }

        $result = $this->db->executeSQL($sql, $bindParameters);

        return $result[0]['count'] ?? 0;
    }

    protected function executeSql(string $sql, array $bindParameters = []): array
    {
        return $this->db->executeSQL($sql, $bindParameters);
    }
}

<?php

/**
 * @pacakge Dbmover
 * @subpackage Mysql
 * @subpackage Constraints
 */

namespace Dbmover\Mysql\Constraints;

use Dbmover\Constraints;
use PDO;

class Plugin extends Constraints\Plugin
{
    protected function dropConstraint(string $table, string $constraint, string $type)
    {
        if ($type == 'FOREIGN KEY') {
            $this->addOperation("ALTER TABLE $table DROP FOREIGN KEY $constraint;");
        }
    }

    protected function getPrimaryKeyInfo(string $table) : array
    {
        static $getIndex, $getColumn;
        if (!isset($getIndex, $getColumn)) {
            $getIndex = $this->loader->getPdo()->prepare(
                "SELECT column_name FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = 'PRIMARY'");
            $getColumn = $this->loader->getPdo()->prepare(
                "SELECT column_type FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?");
        }
        $getIndex->execute([$this->loader->getDatabase(), $table]);
        $column = $getIndex->fetch(PDO::FETCH_ASSOC);
        $getColumn->execute([$this->loader->getDatabase(), $table, $column['column_name']]);
        $column['column_type'] = $getColumn->fetchColumn();
        return $column;
    }
}


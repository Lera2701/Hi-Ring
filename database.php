<?php


class Database
{
    private string $servername = "localhost";
    private string $username = "root";
    private string $password = "123Adm1";
    private string $database = "hi-ring";
    protected $connection;

    public function __construct()
    {
        $this->connection = new mysqli($this->servername, $this->username, $this->password, $this->database);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    function getConection() {
        return $this->connection;
    }

    function getTableColumns($table): bool|array
    {
        $columns = [];
        $result = $this->connection->query('SELECT column_name
            FROM information_schema.columns
            WHERE table_name=' . "'" . $table . "'");

        if (!is_bool($result)) {
            while ($row = $result->fetch_row()) {
                foreach ($row as $r) {
                    if ($r !== 'ID')
                        $columns[] = $r;
                }
            }

            if (!empty($columns)) {
                return ($columns);
            }
        }
        return false;
    }

    function PrepareColumns($columns) {
        $result = '';
        if (is_array($columns)) {
            foreach ($columns as $column) {
                $result .= '`' . $column . '`,';
            }
            return substr($result, 0, strlen($result) - 1);
        } else return '`' . $columns . '`';
    }

    function PrepareValues($values) {
        $result = '';
        foreach ($values as $value) {
            $result .= "'" . $value . "',";
        }
        return substr($result, 0, strlen($result) - 1);
    }

    function Insert($table, $values, $columns = null)
    {
        if ($columns === null)
            $columns = $this->getTableColumns($table);
        if (count($columns) === count($values))
            $query = 'INSERT INTO ' . $table . ' (' . $this->PrepareColumns($columns) . ') VALUES (' . $this->PrepareValues($values) . ')';
        //print_r($query);
        if (strlen($query)) {
            $result = $this->connection->query($query);
        }
        return $result;
    }

    function Select($table, $columns = '*', $limit = null, $where = null, $sortColumn = null, $sortOrder = null, $groupBy = null) {
        $res = [];

        $query = 'SELECT ' . ($columns === '*' ? $columns : $this->PrepareColumns($columns)) . ' FROM ' . $table;
        if ($where !== null)
            $query .= ' WHERE ' . $where;
        if ($sortColumn !== null && $sortOrder !== null)
            $query .= ' ORDER BY ' . $this->PrepareColumns($sortColumn) . strtoupper($sortOrder);
        if ($limit !== null)
            $query .= ' LIMIT ' . $limit;

        if (strlen($query)) {
            $result = $this->connection->query($query);

            if (!is_bool($result)) {
                while ($row = $result->fetch_all()) {
                    $res[] = $row;
                }
            }
        }
        if (is_array($res) && !empty($res))
            if ($limit === 1)
                return($res[0][0]);
            else return($res);
        else return false;
    }

    function Delete($table, $where) {
        $query = 'DELETE FROM ' . $table . ' WHERE ' . $where;
        if (strlen($query))
            $result = $this->connection->query($query);
        return $result;
    }

    function Update($table, $values, $where, $columns = null) {
        $values = explode(',', $values);
        $query = 'UPDATE ' . $table . ' SET ';
        $braces = '`';
        if($columns === null) {
            $braces = '';
            $columns = $this->PrepareColumns($this->getTableColumns($table));
            $columns = explode(',', $columns);
        }
        if (count($columns) === count($values)) {
            for ($i = 0; $i < count($columns); $i++) {
                $query .= $braces.$columns[$i].$braces."='" . $values[$i] . "',";
            }
        }
        $query = substr($query, 0, strlen($query) - 1);
        $query .= ' WHERE ' . $where;

        if (strlen($query))
            $result = $this->connection->query($query);
        return $result;
    }

    function Query($query) {
        $res = [];
        if (strlen($query)) {
            $result = $this->connection->query($query);

            if (!is_bool($result)) {
                while ($row = $result->fetch_all()) {
                    $res[] = $row;
                }
            } else return $result;
        }
        if (is_array($res) && !empty($res))
            if (str_contains($query, 'LIMIT 1'))
                return($res[0][0]);
            else return($res);
        else return false;
    }
}

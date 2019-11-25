<?php

class sqlite implements \Countable
{
    /**
     * @var PDO
     */
    private $db = null;

    /**
     * @var string
     */
    private $name = null;

    public function __construct($name, $filename = "data.sqlite3")
    {
        $this->db = new PDO('sqlite:' . $filename);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->name = $name;
        $this->createTable();
    }

    /**
     * @param string $key key
     *
     * @throws InvalidArgumentException
     * @return string|null
     */
    public function get($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Expected string as key');
        }

        $stmt = $this->db->prepare(
            'SELECT value FROM ' . $this->name . ' WHERE key = :key;'
        );
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            return $row->value;
        }

        return null;
    }

    /**
     * @param string $key key
     * @param string $value value
     *
     * @throws InvalidArgumentException
     */
    public function set($key, $value)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Expected string as key');
        }

        $queryString = 'REPLACE INTO ' . $this->name . ' VALUES (:key, :value);';
        $stmt = $this->db->prepare($queryString);
        $stmt->bindParam(':key', $key, \PDO::PARAM_STR);
        $stmt->bindParam(':value', $value, \PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @param string $key key
     *
     * @return null
     */
    public function delete($key)
    {
        $stmt = $this->db->prepare(
            'DELETE FROM ' . $this->name . ' WHERE key = :key;'
        );
        $stmt->bindParam(':key', $key, \PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Delete all values from store
     *
     * @return null
     */
    public function deleteAll()
    {
        $stmt = $this->db->prepare('DELETE FROM ' . $this->name);
        $stmt->execute();
        $this->data = array();
    }

    /**
     * @return int
     */
    public function count()
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM ' . $this->name)->fetchColumn();
    }

    /**
     * Create storage table in database if not exists
     *
     * @return null
     */
    private function createTable()
    {
        $stmt = 'CREATE TABLE IF NOT EXISTS "' . $this->name . '"';
        $stmt.= '(key TEXT PRIMARY KEY, value TEXT);';
        $this->db->exec($stmt);
    }
}

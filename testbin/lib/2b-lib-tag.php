<?php

class Tag {
    /* [DATABASE HELPER FUNCTIONS] */
    protected $pdo = null;
    protected $stmt = null;
    public $lastID = null;

    function __construct() {
        // __construct() : connect to the database
        // PARAM : DB_HOST, DB_CHARSET, DB_NAME, DB_USER, DB_PASSWORD

        // ATTEMPT CONNECT
        try {
            $str = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            if (defined('DB_NAME')) {
                $str .= ";dbname=" . DB_NAME;
            }
            $this->pdo = new PDO(
                $str, DB_USER, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } // ERROR - CRITICAL STOP - THROW ERROR MESSAGE
        catch (Exception $ex) {
            print_r($ex);
            die();
        }
    }

    function __destruct() {
        // __destruct() : close connection when done

        if ($this->stmt !== null) {
            $this->stmt = null;
        }
        if ($this->pdo !== null) {
            $this->pdo = null;
        }
    }

    function exec($sql, $data = null) {
        // exec() : run insert, replace, update, delete query
        // PARAM $sql : SQL query
        //       $data : array of data

        try {
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->lastID = $this->pdo->lastInsertId();
        } catch (Exception $ex) {
            $this->error = $ex;
            return false;
        }
        $this->stmt = null;
        return true;
    }

    function start() {
        // start() : auto-commit off

        $this->pdo->beginTransaction();
    }

    function end($commit = 1) {
        // end() : commit or roll back?

        if ($commit) {
            $this->pdo->commit();
        } else {
            $this->pdo->rollBack();
        }
    }

    function fetchAll($sql, $cond = null, $key = null, $value = null) {
        // fetchAll() : perform select query (multiple rows expected)
        // PARAM $sql : SQL query
        //       $cond : array of conditions
        //       $key : sort in this $key=>data order, optional
        //       $value : $key must be provided. If string provided, sort in $key=>$value order. If function provided, will be a custom sort.

        $result = [];
        try {
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($cond);
            // Sort in given order
            if (isset($key)) {
                if (isset($value)) {
                    if (is_callable($value)) {
                        while ($row = $this->stmt->fetch(PDO::FETCH_NAMED)) {
                            $result[$row[$key]] = $value($row);
                        }
                    } else {
                        while ($row = $this->stmt->fetch(PDO::FETCH_NAMED)) {
                            $result[$row[$key]] = $row[$value];
                        }
                    }
                } else {
                    while ($row = $this->stmt->fetch(PDO::FETCH_NAMED)) {
                        $result[$row[$key]] = $row;
                    }
                }
            } // No key-value sort order
            else {
                $result = $this->stmt->fetchAll();
            }
        } catch (Exception $ex) {
            $this->error = $ex;
            return false;
        }
        // Return result
        $this->stmt = null;
        return count($result) == 0 ? false : $result;
    }

    function fetchCol($sql, $cond = null) {
        // fetchCol() : yet another version of fetch that returns a flat array
        // I.E. Good for one column SELECT `col` FROM `table`

        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($cond);
        $result = $this->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return count($result) == 0 ? false : $result;
    }

    /* [TAG FUNCTIONS] */
    function getAll($id) {
        // get all tags for the given post ID

        $sql = "SELECT * FROM `tags` WHERE `post_id`=?";
        return $this->fetchCol("SELECT `tag` FROM `tags` WHERE `post_id`=?", [$id]);
    }

    function search($tag) {
        // search() : search for posts with the given tag

        return $this->fetchAll(
            "SELECT p.* FROM `tags` t LEFT JOIN posts `p` USING (`post_id`) WHERE t.`tag`=?",
            [$tag],
            "post_id"
        );
    }

    function reTag($id, $tags = null) {
        // reTag() : replace tags for the given post ID
        // PARAM $id : post ID
        //       $tags : array of tags

        // Auto-commit off
        $this->start();

        // Remove old tags first
        $pass = $this->exec(
            "DELETE FROM `tags` WHERE `post_id`=?", [$id]
        );

        // Add new tags - If any
        // Might be a good idea to limit the total number of tags...
        if ($pass && is_array($tags) && count($tags) > 0) {
            $sql = "INSERT INTO `tags` (`post_id`, `tag`) VALUES ";
            $data = [];
            foreach ($tags as $t) {
                $sql .= "(?,?),";
                $data[] = $id;
                $data[] = $t;
            }
            $sql = substr($sql, 0, -1);
            $pass = $this->exec($sql, $data);
        }

        // End - commit or rollback
        $this->end($pass);
        return $pass;
    }
}

?>
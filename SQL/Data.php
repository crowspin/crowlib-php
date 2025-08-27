<?php

namespace crow\SQL;

/** An iterable containing the results of your query. Rows are keyed (0-x), Values per row are keyed BOTH (0-x) AND by column name via reference. */
class Data extends \ArrayObject {

}



/*
    /**
     * The last function error number obtained, `0` meaning no errors.
     /
    public int $mysqli_errno = 0;
    /**
     * The last function error, empty if no error.
     /
    public string $mysqli_error = "";
    /**
     * An array keyed 0-x containing the names of the columns in order.
     /
    public array $headers = array();
    /**
     * `True` if query succeeded, `false` if there was an error or if the query was bad.
     /
    public bool $success = false;
    /**
     * Name of the column that `map` and `k` will use to index the results.
     /
    private string $keyCol = "";
    /**
     * The map of cell values to result indexes.
     /
    private array $keys = [];









    /**
     * Accepts an `xSQL` connection for error checking and the return value of the query for 
     *      formatting. Automatically maps the result set based on a primary key or unique 
     *      key. If the table contains both, then Primary will be used. If the table contains 
     *      neither, the table will not be mapped until you call `map($colName)`.
     * @param xSQL $sql The `xSQL` object on which the query was run.
     * @param mysqli_return $return The server response to the query.
     * @return xSQLData A response object generated from the query. Failure can be detected 
     *      by checking it's `success` member variable.
     /
    function __construct(&$sql, $return){
        parent::__construct();
        $this->mysqli_errno = $sql->errno;
        $this->mysqli_error = $sql->error;

        if ($this->mysqli_errno || !$return) return $this;
        $this->success = true;
        if (is_bool($return)) return $this;
        $headinfo = $return->fetch_fields();

        for ($it = 0; $it < count($headinfo); $it++){
            $this->headers[$it] = $headinfo[$it]->name;
            if (empty($this->keyCol) && $headinfo[$it]->flags & 2) $this->keyCol = $headinfo[$it]->name;
        }
        if (empty($this->keyCol)) foreach ($headinfo as $col) if ($col->flags & 4){
            $this->keyCol = $col->name;
            break;
        }
        for ($it = 0; $it < $return->num_rows; $it++){
            $this[$it] = array();
            $row = $return->fetch_row();
            for ($sit = 0; $sit < count($row); $sit++){
                $this[$it][$sit] = $row[$sit];
                $this[$it][$this->headers[$sit]] = &$this[$it][$sit];
                if (!empty($this->keyCol) && $this->headers[$sit] == $this->keyCol) $this->keys[$row[$sit]] = $it;
            }
        }
    }
    /**
     * Fetches the row in your `xSQLData` object whose mapped column cell matches the 
     *      supplied value.
     * @param mixed $id The value to be searched.
     * @return false|array Returns false if the `xSQLData` object is not mapped, or if 
     *      the supplied `$id` is not found. Otherwise, returns a result row.
     /
    function k($id){
        if (!isset($this->keyCol) || !isset($this->keys[$id])) return false;
        return $this[$this->keys[$id]];
    }
    /**
     * Manually sets the column you want to use to map your `xSQLData` object.
     * @param mixed $colName the column name you want to use as an index.
     * @return boolean Returns false if the supplied value `!isset`, or if it isn't 
     *      found in the list of column names (xSQLData->headers). Otherwise, maps the 
     *      results and returns true.
     /
    function map($colName){
        if (!isset($colName)) return false;

        $test = false;
        foreach ($this->headers as &$v) if ($colName == $v) {
            $test = true;
            break;
        }
        if (!$test) return false;

        $this->keyCol = $colName;
        foreach ($this as $k=>&$v) $this->keys[$v[$colName]] = $k;
        return true;
    }
*/
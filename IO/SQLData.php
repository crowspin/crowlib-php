<?php

namespace crow\IO;

/** An iterable containing the results of your query. Rows are keyed (0-x), Values per row are keyed BOTH (0-x) AND by column name via reference. */
class SQLData extends \ArrayObject {
    /** `True` if query succeeded, `false` if there was an error or if the query was bad. */
    public bool $success = false;

    /** The last function error number obtained, `0` meaning no errors. */
    public int $errno = 0;

    /** The last function error, empty if no error. */
    public string $error = "";

    /** An array keyed 0-x containing the names of the columns in order. */
    public array $headers = array();

    /** Name of the column we've currently mapped the array to. Held to prevent unnecessary map_to_column operations. */
    private string $mapped_column = "";

    /**
     * Accepts a mysqli_result object and aims to make traversal and search just a little simpler. Holds any errors produced from the execution of the query.
     * @param \mysqli_result $return_value The result object from your query. Will be parsed and restructured.
     * @param int $mysqli_errno The errno produced from the query, stored here for reference because mysqli class overwrites it.
     * @param string $mysqli_error The error string produced from the query. Likely empty, but kept for reference.
     * @return void
     */
    function __construct($return_value, $mysqli_errno, $mysqli_error){
        parent::__construct();
        $this->errno = $mysqli_errno;
        $this->error = $mysqli_error;

        if ($this->errno || !$return_value) return;
        $this->success = true;
        if (is_bool($return_value)) return;
        $header_info = $return_value->fetch_fields();
        for ($i = 0; $i < count($header_info); $i++){
            $this->headers[$i] = $header_info[$i]->name;
        }

        for ($i = 0; $i < $return_value->num_rows; $i++){
            $this[$i] = array();
            $row = $return_value->fetch_row();
            for ($j = 0; $j < count($row); $j++){
                $this[$i][$this->headers[$j]] = $row[$j];
            }
        }
    }

    /**
     * Before this function is called, the data in the array is keyed by index. First item in the mysqli_return is at `$this[0]`.  
     * After this function is called the array will no longer be keyed by index, but instead by the values in the specified column.
     * So if you had a query call for a row from a user table, you might `map_to_column("username")`, and then you might use that for a login process like `$email = $this[$username]["email"]`.
     * The intent here is that there isn't much value in looking at the table of returned rows based on it's order from the server. This process doesn't change that order either,
     * just makes looking up which row in the return you want to work with easier.  
     * Call this function with an empty string or without an argument to undo the mapping.  
     * Decision was made to remove automatic mapping to primary_key or unique_key columns because that was causing confusion. Better to have it called in sequence so you can see what column is mapped while you're reading the code.
     * @param string $columnName Must either be an empty string (default) or a valid column name.
     * @return bool False if the column name provided is invalid, otherwise true.
     */
    public function map_to_column($columnName = "") : bool {
        if ($this->mapped_column == $columnName) return true;

        if (!empty($columnName)){
            $test = false;
            foreach ($this->headers as &$v) if ($columnName == $v) {
                $test = true;
                break;
            }
            if (!$test) return false;
        }

        $this->mapped_column = $columnName;

        $newArray = [];
        foreach ($this as $v){
            if (!empty($columnName)) $newArray[$v[$columnName]] = $v;
            else $newArray[] = $v;
        }
        $this->exchangeArray($newArray);

        return true;
    }
}

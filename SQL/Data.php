<?php

namespace crow\SQL;

/** An iterable containing the results of your query. Rows are keyed (0-x), Values per row are keyed BOTH (0-x) AND by column name via reference. */
class Data extends \ArrayObject {
    /** `True` if query succeeded, `false` if there was an error or if the query was bad. */
    public bool $success = false;

    /** The last function error number obtained, `0` meaning no errors. */
    public int $errno = 0;

    /** The last function error, empty if no error. */
    public string $error = "";

    /** An array keyed 0-x containing the names of the columns in order. */
    public array $headers = array();

    function __construct($return_value, $mysqli_errno, $mysqli_error){
        parent::__construct();
        $this->errno = $mysqli_errno;
        $this->error = $mysqli_error;

        if ($this->mysqli_errno || !$return_value) return;
        $this->success = true;
        if (is_bool($return_value)) return;
        $header_info = $return_value->fetch_fields();

        //$primary_key_column = "";
        //$unique_key_column = "";

        for ($i = 0; $i < count($header_info); $i++){
            $this->headers[$i] = $header_info[$i]->name;
            //if ($header_info[$i]->flags & 2 && empty($primary_key_column)) $primary_key_column = $header_info[$i]->name;
            //if ($header_info[$i]->flags & 4 && empty($unique_key_column)) $unique_key_column = $header_info[$i]->name;
        }

        //$this->key_column = (!empty($primary_key_column))?$primary_key_column:$unique_key_column;

        for ($i = 0; $i < $return_value->num_rows; $i++){
            $this[$i] = array();
            $row = $return_value->fetch_row();
            for ($j = 0; $j < count($row); $j++){
                $this[$i][$this->headers[$j]] = $row[$j];
            }
        }
    }
}



/*

Actively struggling to figure out the use of the key/map/k system I have below here
I'm *pretty* sure that it's about ease of search?
So if we fetch a table of users, it'll probably be indexed by id#
but id# doesn't relate to username
so if we map to username, we can do the map operation once and then search the results more quickly?
So then the idea is that the keys and intrinsic arrays both are collections of rows, but the intrinsic array is keyed in the order recieved from the server (so in an unreliable order), and the
keys array (I hate the old name scheme) is searchable by values. So like keys[xer01ne][email] instead of foreach keys -> if username == xer01ne -> access [email]

Think I want to hold the original copy in a private array and affect change on the accessible (intrinsic) array? Think about that more tommorrow.

Brain still on vacation. Very worried for first deployment attempt, nothing has been pushed to an actual webserver yet.
My worry about modifying the intrinsic array was that iteration could have been more complicated, but then if I needed to use a standard for loop I could just foreach array_keys, and the biggest issue would be the need to sync $keyCol before a layered operation.
In truth, the only value the key in the existing intrinsic array has is indicating the order in which the rows were recieved form the server, and that could still be useful, but not as useful as direct lookup from a primary-key column that retains full iterability.

So we'll recieve the value table from the server, store it in order in a private array, and immediately map it IF a primary or unique key exists. If it doesn't we'll work it as-is.
Map should allow no-arg to indicate server order, where it'll just key with integers.
Most of our operations should be returning very few rows though. Is a sort operation really worth while? We did a whole module on sort algorithms. Though, I'm not actually going to sort anything, just affect access keys.. All operations should be n for that reason.
I don't think I'll need the underlying original copy. And I'm not convinced it's worth the potential confusion to key the array automatically. I ought to just make a one-line call to the map function after getting the results if only for the sake of explicitness.
Always catching myself trying to optimizse my write speed and sacrificing my own ability to read wtf I'm doing later on. Biggest problem with this refactor. I've been afraid to touch this monolith for ages because it's so integral and also so dense and overcomplicated.

I don't think there's enough value to keeping the integer-access for column identification apart from having a dangerous shorthand. Users should know the names of the columns in the table, and be aware that table structure and organization can change even just with a tweak to the query string.
I was thinking about having the integers and keyed-column cell values simultaneously as I had done with the columnname accesses in the note right above this, but then iteration over collections of rows would be doubled pointlessly....
Identification of keyed column in contructor is only really valuable for automatic mapping. If we're requiring users to know the table and manually call for a mapping, then we don't need that. Or really to store the $key_column/$keyCol either (though that could be used still to prevent wasted cycles...)
Want to read about mysqli_fetch_fields. Worried I'm stripping something valuable in current contructor implementation.

    
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
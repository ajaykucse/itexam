<?php
if (file_exists("security.php")) include_once "security.php";
if (file_exists("../security.php")) include_once "../security.php";

class Database {
 
    private $host;
    private $user;
    private $pass;
    private $database_name;
    private $link;
    private $error;
    private $errno;
    private $query;
 
    function __construct($host, $user, $pass, $database_name = "") {
        $this -> host = $host;
        $this -> user = $user;
        $this -> pass = $pass;
        if (!empty($database_name)) $this -> name = $database_name;      
        $this -> connect();
    }
 
    function __destruct() {
        @mysqli_close($this->link);
    }
 
    public function connect() {
        if ($this -> link = mysqli_connect($this -> host, $this -> user, $this -> pass)) {
            if (!empty($this -> name)) {
                if (!mysqli_select_db($this -> link, $this -> name)) $this -> exception("Could not connect to the database!");
            }
        } else {
            $this -> exception("Could not create database connection!");
						exit();
        }
    }
 
    public function close() {
        @mysqli_close($this->link);
    }
 
    public function query($sql) {
		$this->query = @mysqli_query($this->link,"SET NAMES utf8;");		//change this when your database is UTF-8;
        if ($this->query = @mysqli_query($this->link,$sql)) {
            return $this->query;
        } else {
            $this->exception("Could not query database!");
            return false;
        }
    }

    public function multi_query($sql) {
		$this->query = @mysqli_query($this->link,"SET NAMES utf8;");		//change this when your database is UTF-8;
        if ($this->query = @mysqli_multi_query($this->link,$sql)) {
            return $this->query;
        } else {
            $this->exception("Could not query database!");
            return false;
        }
    }

    public function begin() {
//        if ($this->query = @mysqli_query($this->link,"BEGIN;"))		// BEGIN Transaction
		if ($this->query = @mysqli_autocommit($this->link,FALSE))
		{
					return $this->query;
		}
		else
		{
				$this->exception("Could not query BEGIN!");
				return false;
			}
    }

    public function commit() {
//        if ($this->query = @mysqli_query($this->link,"COMMIT;"))			// Permanent Change into the database after BEGIN Transaction
//			if ($this->query = @mysqli_commit($this->link))
if ($this->query = @mysqli_commit($this->link))

		{
            return $this->query;
						@mysqli_autocommit($this->link,TRUE);
        } else
		{
            $this->exception("Could not query COMMIT!");
            return false;
        } 
    }

    public function rollback() {
//        if ($this->query = @mysqli_query($this->link,"ROLLBACK;")) 		// Rollback Transaction
//			if ($this->query = @mysqli_rollback($this->link))
if ($this->query = @mysqli_rollback($this->link))
		{
            return $this->query;
						@mysqli_autocommit($this->link,FALSE);
        } else
		{
            $this->exception("Could not query ROLLBACK!");
            return false;
        }
    }

    public function affected_rows() {
        if ($this->query = @mysqli_affected_rows($this->link)) 		// Affected Rows
		{
            return $this->query;
        } else
		{
            $this->exception("Could not get Affected Rows!");
            return false;
        }
    }

    public function num_rows($qid) {
        if (empty($qid)) {         
            $this->exception("Could not get number of rows because no query id was supplied!");
            return false;
        } else {
            return mysqli_num_rows($qid);
        }
    }

    public function fetch_array($qid) {
        if (empty($qid)) {
            $this->exception("Could not fetch array because no query id was supplied!");
            return false;
        } else {
            $data = mysqli_fetch_array($qid);
        }
        return $data;
    }

    public function fetch_fields($qid) {
        if (empty($qid)) {
            $this->exception("Could not fetch array because no query id was supplied!");
            return false;
        } else {
            $data = mysqli_fetch_fields($qid);
        }
        return $data;
    }

 
    public function fetch_array_assoc($qid) {
        if (empty($qid)) {
            $this->exception("Could not fetch array assoc because no query id was supplied!");
            return false;
        } else {
            $data = mysqli_fetch_array($qid, MYSQLI_ASSOC);
        }
        return $data;
    }

    public function fetch_object($qid) {
       if (empty($qid)) {
            $this->exception("Could not fetch object because no query id was supplied!");
            return false;
        } else {
            $data = mysqli_fetch_object($qid);
        }
        return $data;
    }

    public function free($qid) {
       if (empty($qid)) {
            $this->exception("Could not free the result because no query id was supplied!");
            return false;
        } else {
            $data = mysqli_free_result($qid);
        }
        return $data;
    }


    public function fetch_all_array($sql, $assoc = true) {
        $data = array();
        if ($qid = $this->query($sql)) {
            if ($assoc) {
                while ($row = $this->fetch_array_assoc($qid)) {
                    $data[] = $row;
                }
            } else {
                while ($row = $this->fetch_array($qid)) {
                    $data[] = $row;
                }
            }
        } else {
            return false;
        }
        return $data;
    }
 
    public function last_id() {
        if ($id = mysqli_insert_id()) {
            return $id;
        } else {
            return false;
        }
    }
 
    private function exception($message) {
        if ($this->link) {
            $this->error = mysqli_error($this->link);
            $this->errno = mysqli_errno($this->link);
        } else {
            $this->error = mysqli_error();
            $this->errno = mysqli_errno();
        }

        if (PHP_SAPI !== 'cli') 
		{
            $text='';
						$text.= "Message: $message<br>";
						if (strlen($this->error) > 0): 
						 $text .= $this->error;
					 endif; 
					 $text .= "Script: " . @$_SERVER['REQUEST_URI'] . "<br>";
					 if (strlen(@$_SERVER['HTTP_REFERER']) > 0):
					 	$text .= "Referrer: " .@$_SERVER['HTTP_REFERER'] . "<br>";
					 endif;
//					notify_big("Database Error",$text,NULL );

        } else {
//                    echo "MYSQL ERROR: " . ((isset($this->error) && !empty($this->error)) ? $this->error:'') . "\n";
        };
    }
 
}

/*

EXAMPLES
=========

// start database connection

include "includes/db.php";
$db = new Database('localhost', 'username', 'password', 'database_name');

// query database
$q = "SELECT * FROM articles ORDER BY id DESC";
$r = $db->query($q);

// if we have a result loop over the result
if ($db->num_rows($r) > 0) {
  while ($a = $db->fetch_array_assoc($r)) {
    echo "{$a['author']} wrote {$a['title']}\n";
  }
}

// if we have a result loop over the result
if ($db->num_rows($r) > 0) {
  while ($a = $db->fetch_object($r)) {
    echo "{$a->author']} wrote {$a->title']}\n";
  }
}

 
// fetch array of articles with less code
$q = "SELECT * FROM articles ORDER BY id DESC";
$a = $db->fetch_all_array($q);
if (!empty($a)) {
  foreach ($a as $k => $v) {
    echo "{$v['author']} wrote {$v['title']}\n";
  }
} */
?>
<?php
class DbFunctions {
	private $conn;
	public function __construct($host, $username, $password, $database) {
		try {
    		$this->conn = new PDO("mysql:host=".$host.";dbname=".$database, $username, $password);
    		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
	}
	public function __destruct() {
		unset($this->conn);
	}
	public function getConn() {
	    return $this->conn;
	}
	public function query($query, $param=array()) {
	    try {
    	    $stmt = $this->conn->prepare($query);
            $stmt->execute($param);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    	} catch(PDOException $e) {
	        return array();
        }
	}
	public function countRows($tableName, $optCond = null) {
	    $query = "SELECT COUNT(1) AS CNT FROM ".$tableName;
	    $cond = array();
	    $condVal = array();
	    
	    if ($optCond !== null)
	    foreach ($optCond as $key=>$val) {
	        $cond[] = $key."=?";
	        $condVal[] = $val;
	    }
	    
	    if (count($cond) > 0)
	        $query .= " WHERE ".implode(" AND ", $cond);
	       
	    $count = 0;
	    try {
    	    $stmt = $this->conn->prepare($query);
            $stmt->execute($condVal);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $vl)
                $count = $vl["CNT"];
	    } catch(PDOException $e) {
	        $count = 0;
        }
        return $count;
	}
	public function countRowsNew($tableName) {
	    $query = $tableName;
	    $cond = array();
	    $condVal = array();
	    
	   
	    $count = 0;
	    try {
    	    $stmt = $this->conn->prepare($query);
            $stmt->execute($condVal);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $vl)
                $count = $vl["CNT"];
	    } catch(PDOException $e) {
	        $count = 0;
        }
        return $count;
	}
	public function fetchSingle($tableName, $optCond = null, $optOrder = null, $optColumns = null) {
	    $query = "SELECT ".(($optColumns===null)?"*":$optColumns)." FROM ".$tableName;
	    $cond = array();
	    $condVal = array();
	    
	    if ($optCond !== null) {
			if ($this->isAssoc($optCond)) {
				foreach ($optCond as $key=>$val) {
					$cond[] = $key."=?";
					$condVal[] = $val;
				}
			} else if (isset($optCond[0]) && $optCond[0] instanceof Condition) {
				foreach ($optCond as $cnd) {
					$cond[] = $cnd->getColumn()." ".($cnd->getOperator() === null?"=":$cnd->getOperator())." ?";
					$condVal[] = $cnd->getValue();
				}
			} else {
				return array();
			}
		}
	    
	    if (count($cond) > 0)
	        $query .= " WHERE ".implode(" AND ", $cond);
	        
	    if ($optOrder !== null)
	        $query .= " ORDER BY ".$optOrder;

	    try {
	        //echo "<br>Query: ".$query;
    	    $stmt = $this->conn->prepare($query);
            $stmt->execute($condVal);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) > 0)
                return $rows[0];
            else
                return array();
        } catch(Exception $e) {
            return array();
        }
	}
	public function fetch($tableName, $optCond = null, $optOrder = null, $optColumns = null) {
	    $query = "SELECT ".(($optColumns===null)?"*":$optColumns)." FROM ".$tableName;
	    $cond = array();
	    $condVal = array();
	    
	    if ($optCond !== null) {
			if ($this->isAssoc($optCond)) {
				foreach ($optCond as $key=>$val) {
					$cond[] = $key."=?";
					$condVal[] = $val;
				}
			} else if (isset($optCond[0]) && $optCond[0] instanceof Condition) {
				foreach ($optCond as $cnd) {
					$cond[] = $cnd->getColumn()." ".($cnd->getOperator() === null?"=":$cnd->getOperator())." ?";
					$condVal[] = $cnd->getValue();
				}
			} else {
				return array();
			}
		}
	    
	    if (count($cond) > 0)
	        $query .= " WHERE ".implode(" AND ", $cond);
	        
	    if ($optOrder !== null)
	        $query .= " ORDER BY ".$optOrder;

	    try {
	        //echo "<br>Query: ".$query;
    	    $stmt = $this->conn->prepare($query);
            $stmt->execute($condVal);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            return array();
        }
	}
	public function insert($tableName, $values) {
	    if (count($values) == 0)
	        return 0;
	    foreach ($values as $key=>$val) {
            $cols[] = $key;
            $vals[] = "?";
            $parVal[] = $val;
        }
        $query = "INSERT INTO ".$tableName." (".implode(",", $cols).") VALUES (".implode(",", $vals).")";
        try {
            //echo "<br>Query: ".$query;
    	    $stmt = $this->conn->prepare($query);
            $this->conn->beginTransaction();
            $stmt->execute($parVal);
            $id = $this->conn->lastInsertId();
            $this->conn->commit();
            return $id;
        } catch(Exception $e) {
            $this->conn->rollback();
            error_log($e);
            return -1;
        }
	}
	public function upsert($tableName, $values) {
	    if (count($values) == 0)
	        return 0;
	    foreach ($values as $key=>$val) {
            $cols[] = $key;
            $vals[] = "?";
            $parVal[] = $val;
            $updVal[] = $key."=?";
        }
        $query = "INSERT INTO ".$tableName." (".implode(",", $cols).") VALUES (".implode(",", $vals).") ON DUPLICATE KEY UPDATE ".implode(",", $updVal);
        try {
    	    $stmt = $this->conn->prepare($query);
            $this->conn->beginTransaction();
            $stmt->execute(array_merge($parVal, $parVal));
            $id = $this->conn->lastInsertId();
            $this->conn->commit();
            return $id;
        } catch(Exception $e) {
            $this->conn->rollback();
            return -1;
        }
	}
	public function update($tableName, $values, $optCondition=null) {
	    if (count($values) == 0)
	        return 0;
	    foreach ($values as $key=>$val) {
            $parVal[] = $val;
            $updVal[] = $key."=?";
        }
        $query = "UPDATE ".$tableName." SET ".implode(",", $updVal);
        if ($optCondition !== null)
        foreach ($optCondition as $key=>$val) {
	        $cond[] = $key."=?";
	        $parVal[] = $val;
	    }
	    
	    if (count($cond) > 0)
	        $query .= " WHERE ".implode(" AND ", $cond);
        
        try {
           // echo "<br>Query: ".$query;
    	    $stmt = $this->conn->prepare($query);
            $this->conn->beginTransaction();
            $stmt->execute($parVal);
            $this->conn->commit();
            return 0;
        } catch(Exception $e) {
            $this->conn->rollback();
            return -1;
        }
	}
	public function delete($tableName, $optCondition=null) {
	    $query = "DELETE FROM ".$tableName;
        if ($optCondition !== null)
        foreach ($optCondition as $key=>$val) {
	        $cond[] = $key."=?";
	        $parVal[] = $val;
	    }
	    
	    if (count($cond) > 0)
	        $query .= " WHERE ".implode(" AND ", $cond);
        
        try {
    	    $stmt = $this->conn->prepare($query);
            $this->conn->beginTransaction();
            $stmt->execute($parVal);
            $this->conn->commit();
            return 0;
        } catch(Exception $e) {
            $this->conn->rollback();
            return -1;
        }
	}
	private function isAssoc($array) {
        if(!is_array($array))
            return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
class Condition {
	private $column, $value, $operator;
	public function __construct($col, $val, $opr) {
		$this->column = $col;
		$this->value = $val;
		$this->operator = $opr;
	}
	public function __destruct() {
		unset($this->column);
		unset($this->value);
		unset($this->operator);
	}
	public function getColumn() {
		return $this->column;
	}
	public function getValue() {
		return $this->value;
	}
	public function getOperator() {
		return $this->operator;
	}
}
function dbSQLCondition($column, $value, $operator = null) {
	return new Condition($column, $value, $operator);
}
?>
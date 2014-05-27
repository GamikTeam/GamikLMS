<?php
/*
 * Klasa generująca zapytania i zrwacająca ich wyniki jako tabele
 */
class DbMySQL {
    private $table;
    private $query;
    private $result;
    private $num_row;
    private $mysqli;
    private $db_contract;
    private $connected;
    
    public function __construct(DbContract $db_contract) {
        $this->db_contract = $db_contract;
        $this->table = null;
        $this->query = null;
        $this->result = null;
        $this->num_row = null;
        $this->connected = null;
        
        //connect
        $config_db = $this->db_contract->getConfig();
        $this->mysqli = new mysqli($config_db['host'], $config_db['user'], $config_db['password'], $config_db['database']);
        if($this->mysqli->connect_errno > 0){
            //die('Problem z połączeniem do bazy [' . $this->mysqli->connect_error . ']');
            $this->connected = false;
        } else {
            $this->connected = true;
        }
        $this->mysqli->set_charset('utf8');
        //$this->mysqli->query("SET NAMES utf8");
        //$this->mysqli->query("SET CHARACTER SET UTF8");
    }
    
    public function __destruct() {
        $this->mysqli->close();
    }
    
        
    /*
     * wykonanie przekazanego zapytania
     * zwraca liczbe zmienionych wierszy
     */
    public function exec($query){
        $this->query = $query;
        $this->result = $this->mysqli->query($this->query);
        return $this->result;
    }
    
    /*
     * wykonanie przekazanego zapytania
     * zwraca wynik jako tabele
     */
    public function query($query){
        $this->query = $query;
        $this->result = $this->mysqli->query($this->query);
        $this->table = $this->resultToArray($this->result);
        return $this->table;
    }
    
    /*
     * generure zapytanie SQL według przekazanych parametrów
     * wej:
     * table - nazwa tabeli
     * wheres[] - warunki - array(array('type'=>'...', 'column'=>'...', 'value'=>'...', ...),...)
     * joins[] - łączenia tabel - array(array('type'=>'...', 'table_right'=>'...', ...),...)
     * sort - sortowanie - array('column'=>'...', 'reverse'=>'...', 'table'=>'...')
     * limit - ilość rekordów - int
     * *wszystkie potrzebne parametry genereuje klasa DbCriteria
     * wyj:
     * wynik zapytania jako tabele
     */
    public function getArray($table, $wheres = null, $joins = null, $sort = null, $limit = null){
        $this->query = "SELECT * FROM `" . $table . "`";
        if($joins != null){
            foreach ($joins as $join) {
                $this->query .= " " . $join['type'] . " `" . $join['table_right'] . 
                        "` ON `" . $join['table_left'] . "`.`" . $join['left_field'] .
                        "`=`" . $join['table_right'] . "`.`" . $join['right_field'] . "`";
            }
        }
        if($wheres != null){
            $this->query .= " WHERE ";
            $operator = false;
            foreach ($wheres as $where) {
                if(!$operator){
                    $operator = true;
                } else {
                    $this->query .= " " . $where['logic_operator'];
                }
                $this->query .= " `" . $where['table'] . "`.`" . $where['column'] . "`";
                switch($where['type']){
                    case 'equation':
                        $this->query .= $where['math_operator'] . "'" . $where['value'] ."'";
                        break;
                    case 'between':
                        $this->query .= " BETWEEN '" . $where['from'] . "' AND '" . $where['to'] ."'";
                        break;
                    case 'like':
                         $this->query .= " LIKE '" . $where['value'] ."'";
                        break;
                    case 'in':
                         $this->query .= " IN (" . implode(',', $where['array']) .")";
                        break;
                }
                
            }
        }
        if($sort != null){
            $this->query .= " ORDER BY  `" . $sort['table'] . "`.`" . $sort['column'] . "` ";
            if($sort['reverse']) {
                $this->query .= "DESC ";
            } else {
                $this->query .= "ASC ";
            }
        }
        if($limit != null){
             $this->query .= " LIMIT 0 , " . $limit;
        }
        $this->result = $this->mysqli->query($this->query);
        $this->table = $this->resultToArray($this->result);
        return $this->table;
    }
    
    /*
     * pobiera dane z bazy danych według podanych parametrów:
     * table_name - nazwa tabeli
     * where - warunek - array('kolumna' => 'wartosc', ...)
     * zwraca tabele wyników zapytania
     */
    public function getArrayWhere($table_name, Array $where) {
        $this->query = "SELECT * FROM `" . $table_name . "` WHERE ";
        $operator = "";
        foreach ($where as $key => $value) {
            $this->query = $this->query . $operator .  "`" . $key . "` = '" . $value ."'";
            $operator = " AND ";
        }
        $this->result = $this->mysqli->query($this->query) or die($this->mysqli->error);
        return $this->resultToArray($this->result);
    }
    
    /* 
     * Dodaje rekord do bazy danych
     * wej:
     * table_name - nazwa tabeli
     * values - wartosci - array('kolumna' => 'wartosc',...)
     * wyj:
     * liczba zaktualizowanych rekordów
     */
    public function insertRow($table_name, Array $values){
        try {
            foreach ($values as $key => $value) {
                $keys[] = "`".$key."`";
                $vals[] = "'".$value."'";
            }
            $this->query = "INSERT INTO `". $table_name . "` (" . implode(',', $keys) . ") VALUES (" . implode(',', $vals) . ")" ;
            $this->result = $this->mysqli->query($this->query) or die($this->mysqli->error);
            return $this->mysqli->affected_rows;
        } catch (Excepiton $e) {
            echo "Problem z argumentami" . EOL;
            echo $e->getLine() . " : " . $e->getMessage();
            return false;
        }
    }
    
    /*
     * dodaje wiele rekordów do bazy danych
     * wej:
     * table_name - nazwa tabeli
     * fields[] - tabela nazw kolumn
     * values[] - tabela wartosci
     * wyj:
     * liczba zaktualizowanych rekordów
     */
    public function insertMore($table_name, Array $field, Array $values){
        try {
            foreach ($field as $f){
                $temp_val[] = "`".$f."`";
            }
            $field = $temp_val;
            unset($temp_val);
            foreach ($values as $value){
                foreach ($value as $val){
                    $temp_val[] = "'".$val."'";
                }
                $values_string[] = "(" . implode(",", $temp_val) . ")";
                unset($temp_val);
            }
            $this->query = "INSERT INTO `". $table_name . "` (" . implode(',', $field) . ") VALUES " . implode(",", $values_string);
            $this->result = $this->mysqli->query($this->query) or die ($this->mysqli->error);
            return $this->mysqli->affected_rows;
        } catch (Excepiton $e) {
            echo "Problem z argumentami" . EOL;
            echo $e->getLine() . " : " . $e->getMessage();
            return false;
        }
    }
    
    /*
     * Aktualizacja pól w rekordzie
     * wej:
     * table_name - nazwa tabeli
     * values - tablica z wartościami
     * where - tablica z warunkami - array('kolumna'=>'wartosc',...)
     * wyj:
     * liczba zaktualizowanych rekordów
     */
    public function updateRow($table_name, Array $values, Array $where){ 
        try {
            foreach ($values as $key => $value) {
                $keys[] = "`".$key."`";
                $vals[] = "'".$value."'";
            }
            $this->query = "UPDATE `" . $table_name . "` SET ";
            $comma_sep = "";
            for ($i = 0; $i < count($keys); $i++){
                $this->query = $this->query . $comma_sep . $keys[$i] . "=" .$vals[$i];
                $comma_sep = ",";
            }
            
            $keys = null; $vals = null;
            foreach ($where as $key => $value) {
                $keys[] = "`".$key."`";
                $vals[] = "'".$value."'";
            }
            $this->query = $this->query . " WHERE ";
            $comma_sep = "";
            for ($i = 0; $i < count($keys); $i++){
                $this->query = $this->query . $comma_sep . $keys[$i] . "=" .$vals[$i];
                $comma_sep = ",";
            }
            $this->result = $this->mysqli->query($this->query) or die($this->mysqli->error);
            return $this->mysqli->affected_rows;
        } catch (Exception $e) {
            echo "Problem z argumentami" . EOL;
            echo $e->getLine() . " : " . $e->getMessage();
        }
    }
    
    /*
     * usuwanie rekordu z bazy danych
     * wej:
     * table_name - nazwa tabeli
     * where - warunek - array('kolumna'=>'wartosc',...)
     * wyj:
     * liczba zaktualizowanych rekordów
     */
    public function deleteRow($table_name, Array $where){
        try {
            $this->query = "DELETE FROM `" . $table_name . "` WHERE ";
            //while ( list($key, $value) = each($where)){
            foreach ($where as $key => $value) {
                $keys[] = "`".$key."`";
                $vals[] = "'".$value."'";
            }
            $operator = "";
            for ($i = 0; $i < count($keys); $i++){
                $this->query = $this->query . $operator . $keys[$i] . "=" .$vals[$i];
                $operator = ' AND ';
            }
            $this->result = $this->mysqli->query($this->query) or die($this->mysqli->error);
            return $this->mysqli->affected_rows;
        } catch (Exception $e) {
            echo "Problem z argumentami" . EOL;
            echo $e->getLine() . " : " . $e->getMessage();
        }
    }
    
    /*
     * usuwanie wielu rekordów z bazy danych
     * wej:
     * table_name - nazwa tabeli
     * where - warunek - array(array('kolumna'=>'wartosc',...),...)
     * wyj:
     * liczba zaktualizowanych rekordów
     */
    public function deleteMore($table_name, Array $wheres){
        try {
            $this->query = "DELETE FROM `" . $table_name . "` WHERE ";
            $operator = "";
            foreach ($wheres as $where) {
                foreach ($where as $key => $value) {
                    $keys[] = "`".$key."`";
                    $vals[] = "'".$value."'";
                }
                for ($i = 0; $i < count($keys); $i++){
                    $this->query = $this->query . $operator . $keys[$i] . "=" .$vals[$i];
                    $operator = ' OR ';
                }
                unset($keys);
                unset($vals);
            }
            $this->result = $this->mysqli->query($this->query) or die($this->mysqli->error);
            return $this->mysqli->affected_rows;
        } catch (Exception $e) {
            echo "Problem z argumentami" . EOL;
            echo $e->getLine() . " : " . $e->getMessage();
        }
    }
    
    //szukanie
    public function find($table_name, $phrase, $where){
        //uzupelnic - nie znam wymagań
    }
    
    /*
     * pobranie największego id z danej tabeli
     * wej: nazwa tabeli
     * wyj: id(int)
     */
    public function getLastId($table){
        $idclass = 'id' . strtolower($table);
        $this->query = "SELECT `" . $idclass . "` FROM `" . lcfirst($table) . "` ORDER BY `" . $idclass . "` DESC";
        $this->result = $this->mysqli->query($this->query) or die($this->mysqli->error);
        $this->resultToArray($this->result);
        return $this->table[0][$idclass];
    }
    
    /*
     * konwersja rezultatu zapytania SQL do tablicy dwuwymiarowej
     */
    public function resultToArray($result) {
        if($result === false) {
            return array();
        } else {
            while ($row = $result->fetch_assoc()){ $table[] = $row; }
            if (empty($table)) $table = array();
            $this->table = $table;
            return $table;
        }
    }
    
    /*
     * Pobieranie aktualnych danych obietku
     */
    public function getTable(){
        return $this->table;
    }
    
    public function getQuery(){
        return $this->query;
    }
    
    public function getResult(){
        return $this->result;
    }
     
    public function getMysqli() {
        return $this->mysqli;
    }
    
    public function getCurrentRow() {
        return $this->num_row;
    }

    public function getContract() {
        return $this->db_contract;
    }

    public function getConnected() {
        return $this->connected;
    }

    public function getAffectedRows(){
        return $this->mysqli->affected_rows;
    }    
    
    public function getNumRows(){
        return $this->result->num_rows;
    }

    public function getError(){
        return $this->mysqli->error;
    }

    /*
     * inny sposób odczytu wyniku
     */
    public function moveToFirstRow() {
        if(count($this->table) > 0) {
            $this->num_row = 0;
        }
    }
    
    public function nextRow(){
        if ($this->num_row < count($this->table)) {
            $this->num_row++;
            return true;
        } else {
            return false;
        }
    }
    
    public function read($col) {
        return $this->readRow()[$col];
    }
    
    public function readRow(){
        if ($this->table != null && $this->num_row != null){
            if (count($this->table) > 0) { 
                return $this->table[$this->num_row]; 
            }
        } else {
            return false;
        }
    }
    
    /*
     * dodatkowe funkcje pomocnicze
     */
    public function displayTable(){
        if($this->table != null && count($this->table) > 0) {
            $s = "<table border='1' class='database_table'>";
            foreach ($this->table as $row) {
                $s .= "<tr>";
                foreach ($row as $kom) {
                    $s .= "<td>" . $kom . "</td>";
                }
                $s .= "</tr>";
            }
            $s .= "</table>";
            echo $s;
        } else {
            return false;
        }
    }
}

?>
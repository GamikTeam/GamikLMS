<?php
/*
 * Klasa przechowywujaca informacje o strukturze bazy danych
 */

class DbContract {
    private $tables;
    private $structure;
    private $host;
    private $user;
    private $password;
    private $db;
    private $unique_list;
    
    
    function __construct($url_xml) {
        $db_contract = simplexml_load_file($url_xml);
        $this->db = $db_contract['name'];
        $this->host = $db_contract['host'];
        $this->user = $db_contract['user'];
        $this->password = $db_contract['password'];
        $this->unique_list = null;
        $this->structure = null;
        $this->tables = null;
        
        $this->xmlToArray($db_contract);
    }
    
    /*
     * pobiera informacje o bazie nadych z pliku .xml
     */
    private function xmlToArray(SimpleXMLElement $db_contract)
    {
        try {
            foreach ($db_contract->table as $table){
                $table_name = strtolower($table['name']->__toString());
                foreach ($table as $column) {
                    $temp_table[] = $column['name']->__toString();
                    if (isset($column['unique']) && $column['unique']->__toString() === "true"){
                        $temp_unique[] = $column['name']->__toString();
                    }
                }
                $this->tables[] = $table_name;
                
                if(empty($temp_table)) {
                    $temp_table = array();
                }
                $this->structure["$table_name"] = $temp_table;
                unset($temp_table);
                
                if(isset($temp_unique)) {
                    $this->unique_list["$table_name"] = $temp_unique;
                }
                unset($temp_unique);
            }
        } catch (Exception $e) {
            echo "Problem ze składnią xml" . EOL;
            echo $e->getLine() . " : " . $e->getMessage();
        }
    }

    /*
     * zwraca tablice z ustawieniami dostępu do bazy danych
     */
    public function getConfig(){
        return array(
            'host' => $this->host, 
            'user' => $this->user, 
            'password' => $this->password, 
            'database' => $this->db
          );
    }
    
    /*
     * sprawdza czy dana tablica istnieje w bazie
     * na podstawie podanego schematu w .xml
     * wej: nazwa tabeli
     * wyj: bool
     */
    public function isTable($name){
        return in_array($name, $this->tables);
    }
    
    /*
     * zwraca tablice z nazwami tabel
     */
    public function getTables() {
        return $this->tables;
    }

    /*
     * zwraca tablice z nazwami kolumn podanej tablicy
     */
    public function getColumns($name) {
        $name = strtolower($name);
        if(isset($this->structure["$name"])) {
            return $this->structure["$name"];
        } else {
            return false;
        }
    }
    
    /*
     * sprawdza czy podana kolumna istnieje w podanej tabeli
     * wej: kolumna, nazwa tabeli
     * wyj: bool
     */
    public function isColumnInTable($column, $table){
        $cols = $this->getColumns($table);
        if ($cols) {
            if(!in_array($column, $cols)) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
    
    /*
     * zwraca tablice o schemacie:
     * array( tabela_1 => array(columna_1,...,columna_n), ... , tabela_n)
     */
    public function getStructure() {
        return $this->structure;
    }

    /*
     * pobranie danych do łączenia z bazą danych
     */
    public function getHost() {
        return $this->host;
    }

    public function getUser() {
        return $this->user;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getDb() {
        return $this->db;
    }

    /*
     * zwraca tablice nazw unikalnych pól z danej tabeli
     */
    public function getUniqueList($table){
        if(isset($this->unique_list["$table"])){
            return $this->unique_list["$table"];
        } else {
            return false;
        }
    }
}

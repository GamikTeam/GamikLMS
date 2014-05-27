<?php
/*
 * Klasa odpowiedzialna za przekazanie informacji do obiektu DbHelper
 * potrzebnych do pobrania określonych danych
 */
class DbCriteria {
    private $table;
    private $wheres;
    private $sort;
    private $contract;
    private $limit;
    private $join;
    
    const INNER_JOIN = 'INNER JOIN';
    const LEFT_JOIN = 'LEFT JOIN';
    const RIGHT_JOIN = 'RIGHT JOIN';
    const FULL_JOIN = 'FULL JOIN';
    
    const _OR = 'OR';
    const _AND = 'AND';
    
    const EQUAL = "=";
    const UNEQUAL = "~=";
    const MORE = ">";
    const LESS = "<";
    const MORE_EQUALS = ">=";
    const LESS_EQUALS = "<=";
    
    
    private $join_array = array('INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN');
    private $logic_operators_array = array('OR', 'AND');
    private $math_operators_array = array('=', '~=', '>', '<', '<=', '>=');
            
    /*
     * wej:
     * class - clasa reprezentujaca daną tabele z bazy danych
     * db_contract - obiekt DbContract
     */
    function __construct($class, DbContract $db_contract) {
        if(!is_string($class)){
            //throw new ExceptionDb("Problem z nazwą klasy");
        }
        $this->table = strtolower($class);
        if(!$db_contract->isTable($this->table)){
            //throw new ExceptionDb("Podana klasa nie istnieje w bazie danych");
        }
        $this->value = null;
        $this->sort =  null;
        $this->contract = $db_contract;
        $this->limit = null;
        $this->join = null;
    }
    
    /*
     * dodanie warunku równości, nierówności
     * wej:
     * field - nazwa kolumny
     * value - wartosc
     * table - nazwa tabeli; domyślnie podana w konstruktorze
     * math_operator - operator >,<,=,>=,<=; domyślnie '='
     * logig_operator - logiczny operator łączący warunki (AND, OR); domyślnie 'AND'
     */
    public function addWhereCompare($field, $value, $table = null, $math_operator = null, $logic_operator = null){
        if($math_operator == null){
            $math_operator = "=";
        }
        if(!in_array($math_operator, $this->math_operators_array)){
            throw new ExceptionDb("Zły operator logiczny");
        }
        if($logic_operator == null){
            $logic_operator = 'AND';
        }
        if(!in_array($logic_operator, $this->logic_operators_array)){
            throw new ExceptionDb("Zły operator logiczny");
        }
        if($table == null){
            $table = $this->table;
        }
        if(!$this->contract->isTable($table)){
            throw new ExceptionDb("Podana klasa nie istnieje w bazie danych");
        }
        if(!$this->contract->isColumnInTable($field, $table)){
            throw new ExceptionDb("Tabela " . $this->table . " nie posiada podanej kolumny: " . $field);
        }
        $temp_where = array(
            'type' => 'equation',
            'math_operator' => $math_operator,
            'logic_operator'=> $logic_operator,
            'table' => $table,
            'column' => $field,
            'value' => $value
        );
        $this->wheres[] = $temp_where;
        return true;
    }
    
    /*
     * dodanie warunku z przedziału
     * wej:
     * field - nazwa kolumny
     * from - przedzial od
     * to - przedzial do
     * table - nazwa tabeli; domyślnie podana w konstruktorze
     * logic_operator - logiczny operator łączący warunki (AND, OR); domyslnie 'AND'
     */
    public function addWhereBetween($field, $from, $to, $table = null, $logic_operator = null){
        if($logic_operator == null){
            $logic_operator = 'AND';
        }
        if(!in_array($logic_operator, $this->logic_operators_array)){
            throw new ExceptionDb("Zły operator logiczny");
        }
        if($table == null){
            $table = $this->table;
        }
        if(!$this->contract->isTable($table)){
            throw new ExceptionDb("Podana klasa nie istnieje w bazie danych");
        }
        if(!$this->contract->isColumnInTable($field, $table)){
            throw new ExceptionDb("Tabela " . $this->table . " nie posiada podanej kolumny: " . $field);
        }
        $temp_where = array(
            'type' => 'between',
            'logic_operator'=> $logic_operator,
            'table' => $table,
            'column' => $field,
            'from' => $from,
            'to' => $to
        );
        $this->wheres[] = $temp_where;
        return true;
    }
    
    /*
     * dodanie warunku z porównaniem
     * wej:
     * field - nazwa kolumny
     * value - wartosc do porownania
     * table - nazwa tabeli; domyślnie podana w konstruktorze
     * logic_operator - logiczny operator łączący warunki (AND, OR); domyslnie 'AND'
     */
    public function addWhereLike($field, $value, $table = null, $logic_operator = null){
        if($logic_operator == null){
            $logic_operator = 'AND';
        }
        if(!in_array($logic_operator, $this->logic_operators_array)){
            throw new ExceptionDb("Zły operator logiczny");
        }
        if($table == null){
            $table = $this->table;
        }
        if(!$this->contract->isTable($table)){
            throw new ExceptionDb("Podana klasa nie istnieje w bazie danych");
        }
        if(!$this->contract->isColumnInTable($field, $table)){
            throw new ExceptionDb("Tabela " . $this->table . " nie posiada podanej kolumny: " . $field);
        }
        $temp_where = array(
            'type' => 'like',
            'logic_operator'=> $logic_operator,
            'table' => $table,
            'column' => $field,
            'value' => $value
        );
        $this->wheres[] = $temp_where;
        return true;
    }
    
    /*
     * dodanie warunku zawierania
     * wej:
     * field - nazwa kolumny
     * array - tablica wartosci
     * table - nazwa tabeli; domyślnie podana w konstruktorze
     * logic_operator - logiczny operator łączący warunki (AND, OR); domyslnie 'AND'
     */
    public function addWhereIn($field, $array, $table = null, $logic_operator = null){
        if(!is_array($array)){
            throw new ExceptionDb("Argument 2 nie jest tablicą");
        }
        if($logic_operator == null){
            $logic_operator = 'AND';
        }
        if(!in_array($logic_operator, $this->logic_operators_array)){
            throw new ExceptionDb("Zły operator logiczny");
        }
        if($table == null){
            $table = $this->table;
        }
        if(!$this->contract->isTable($table)){
            throw new ExceptionDb("Podana klasa nie istnieje w bazie danych");
        }
        if(!$this->contract->isColumnInTable($field, $table)){
            throw new ExceptionDb("Tabela " . $this->table . " nie posiada podanej kolumny: " . $field);
        }
        $temp_where = array(
            'type' => 'in',
            'logic_operator'=> $logic_operator,
            'table' => $table,
            'column' => $field,
            'array' => $array
        );
        $this->wheres[] = $temp_where;
        return true;
    }
    
    /*
     * dodanie łączenia tabel
     * wej:
     * type - rodzaj łączenia, np INNER JOIN
     * right_field - nazwa kolumny tabeli dołączanej
     * right_table - nazwa tabeli dołączanej
     * left_field - nazwa kolumny tabeli podstawowej
     * left_table - nazwa tabeli podstawowej; domyślnie podana w konstruktorze
     */
    public function addJoin($type, $right_field, $right_table, $left_field, $left_table = null){
        if($left_table == null){
            $left_table = $this->table;
        }
        if(!$this->contract->isTable($left_table)){
            throw new ExceptionDb("Podana tabela: " . $left_table . " nie istnieje w bazie danych");
        }
        if(!$this->contract->isTable($right_table)){
            throw new ExceptionDb("Podana tabela: " . $right_table . " nie istnieje w bazie danych");
        }
        if(!in_array($type, $this->join_array)){
            throw new ExceptionDb("Typ łączenia nie istnieje");
        }
        if(!$this->contract->isColumnInTable($left_field, $left_table)){
            throw new ExceptionDb("Tabela " . $this->table . " nie posiada podanej kolumny: " . $left_field);
        }
        if(!$this->contract->isColumnInTable($right_field, $right_table)){
            throw new ExceptionDb("Tabela " . $right_table . " nie posiada podanej kolumny: " . $right_field);
        }
        $temp_join = array(
            'type' => $type,
            'table_right' => $right_table,
            'left_field' => $left_field,
            'right_field' => $right_field,
            'table_left' => $left_table
        );
        $this->join[] = $temp_join;
        return true;
    }
    
    /*
     * ustawienie sortowania
     * wej:
     * field - nazwa kolumny według, której ma odbyć się sortowanie
     * rewerse - czy od największej do najmniejszej (bool); domyślnie false
     * table - nazwa tabeli; domyślnie podana w kontruktorze
     */
    public function setSort($field, $reverse = false, $table = null){
        if($table == null){
            $table = $this->table;
        }
        if(!$this->contract->isTable($table)){
            throw new ExceptionDb("Podana tabela nie istnieje w bazie danych");
        }
        if(!$this->contract->isColumnInTable($field, $table)){
            throw new ExceptionDb("Tabela " . $this->table . " nie posiada podanej kolumny: " . $table);
        }
        if(!is_bool($reverse)){
            $reverse = false;
        }
        $this->sort['column'] = $field;
        $this->sort['reverse'] = $reverse;
        $this->sort['table'] = $table;
        return true;
    }
    
    /*
     * ustawienie limitu
     * wej: ilosc rekordow
     */
    public function setLimit($number){
        if(!is_int($number) && $number <= 0){
            return false;
        }
        $this->limit = $number;
        return true;
    }
    
    /*
     * ustawienie limitu na 1
     */
    public function setOne() {
        $this->limit = 1;
    }

    
    /*
     * pobranie danych wcześniej wprowadzonych
     */
    public function getClass() {
        return $this->table;
    }

    public function getValue() {
        return $this->value;
    }
    
    public function getWheres() {
        return $this->wheres;
    }
    
    public function getSort(){
        return $this->sort;
    }
    
    public function getJoins(){
        return $this->join;
    }
    
    public function getLimit(){
        return $this->limit;
    }

}

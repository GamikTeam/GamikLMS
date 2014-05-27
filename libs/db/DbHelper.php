<?php
/*
 * Klasa będąca pośrednikiem pomiędzy zapytaniami SQL
 * a obiektami reprezentującymi daną tabelę bazy danych
 */
class DbHelper extends DbMySQL{
    
    /*
     * Dodaje obiekt do bazy danych
     * wej: objekt reprezentujący daną tabele
      wyj: liczba zmienionych wierszy lub false
     */
    public function add($object){
        if(!$this->isExistCopy($object) && $values = $this->prepareValues($object)) {
            $type = strtolower(get_class($object));
            return $this->insertRow($type, $values);
        } else {
            return false;
        }
        
    }
    
    /*
     * Pobiera dane z bazy
     * wej: obj DbCriteria
     * wyj: objekt reprezentujący daną tabele lub tablica objektów
     */
    public function get(DbCriteria $db_criteria){
        $table = $db_criteria->getClass();
        $wheres = $db_criteria->getWheres();
        $joins = $db_criteria->getJoins();
        $sort = $db_criteria->getSort();
        $limit = $db_criteria->getLimit();
        
        $result_table = $this->getArray($table, $wheres, $joins, $sort, $limit);
        if (count($result_table)>0) {
            if (count($result_table)<2) {
                return $this->buildObject($table, $result_table);
            } else {
                return $this->buildObjects ($table, $result_table);
            }
        } else {
            return false;
        }
    }
    
    /*
     * aktualizacja danych o objekcie w bazie danych
     * wej: objekt reprezentujący daną tabele
     * wyj: liczba zmienionych wierszy lub false
     */
    public function update($object){
        if($values = $this->prepareValues($object)) {
            $class = get_class($object); 
            $type = strtolower(get_class($object));
            if($id = $this->getIdObject($object)) {
                $where = array('id'.$class => $id);
                return $this->updateRow($type, $values, $where);
            } else {
                return false;
            }
        } else {
            return false;
        }
        
    }
    
    /*
     * usunięcie wiersza, reprezentujacego dany objekt, z bazy danych na podstawie id
     * wej: obiekt reprezentujący dany rekord
     */
    public function delete($object){
        if($id = $this->getIdObject($object)) {
            $class = get_class($object); 
            $type = strtolower(get_class($object));
            $where = array('id'.$class => $id);
            return $this->deleteRow($type, $where);
        } else {
            return false;
        }
    }
    
    /*
     * sprawdzenie czy jest juz wpis tego obiektu w bazie wedlug unikalnych pól
     * wej: obiekt reprezentujący dany rekord
     * wyj: bool
     */
    private function isExistCopy($object){
        if($this->existObject($object)) {
            $type = strtolower(get_class($object));
            $unique = $this->getContract()->getUniqueList($type);
            if ($unique) {
                foreach ($unique as $field) {
                    $method = "get" . ucfirst($field);
                    $where["$field"] = $object->$method();
                }
                if($this->getArrayWhere($type, $where) == array()){
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }
    
    /*
     * zwraca tablice z nazwami kolumn na podstawie podanego obiektu
     * wej: obiekt reprezentujący dany rekord
     * wyj: tablica z nazwami kolumn danej tabeli
     */
    private function getColumnsObject($object){
        if($this->existObject($object)){
            $type = strtolower(get_class($object));
            $columns = $this->getContract()->getColumns($type);
            return $columns;
        } else {
            return false;
        }
    }
    
    /*
     * sprawdza czy objekt jest reprezentacją tabeli w bazie danych
     * wej: obiekt
     * wyj: bool
     */
    private function existObject($object) {
        $type = strtolower(get_class($object));
        if($this->getContract()->isTable($type)) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * zwraca obiekt danej klasy z danymi
     * przekazanymi z bazy danych jako tablica
     * wej:
     * class - nazwa klasy/tabeli
     * result - tabela wyników zapytania SQL
     * wyj:
     *  obiekt reprezentujący dany rekord
     */
    private function buildObject($class, $result){
        $row = $result[0];
        foreach ($row as $col) {
            $params[] = $col;
        }
        $temp = $params[0];
        unset($params[0]);
        $params[] = $temp;
        
        $objReflection = new ReflectionClass($class);
        return $objReflection->newInstanceArgs($params);
    }
    
    /*
     * zwraca tablice obiektów danej klasy z danymi
     * przekazanymi z bazy danych jako tablica
     * wej:
     * class - nazwa klasy/tabeli
     * result - tabela wyników zapytania SQL
     * wyj:
     * tablica obiektów
     */
    private function buildObjects($class, $result) {
        $cols = $this->getContract()->getColumns($class);
        $temp_row = $result[0];
        foreach ($cols as $col){
            if(!isset($temp_row[$col])){
                return false;
            }
        }
        foreach ($result as $row) {
            foreach ($cols as $col) {
                $params[] = $row[$col];
            }
            $temp = $params[0];
            unset($params[0]);
            $params[] = $temp;

            $objReflection = new ReflectionClass($class);
            $objects[] = $objReflection->newInstanceArgs($params);
            unset($params);
        }
        return $objects;
    }
    
    /*
     * zwraca tablice z danymi o obiekcje
     * wej: objekt reprezentujący daną tabele 
     * wyj: array("zmienna" => "wartość", ...);
     */
    private function prepareValues($object){
        if($columns = $this->getColumnsObject($object)) {
            foreach ($columns as $column){
                $method = "get" . ucfirst($column);
                $values["$column"] = $object->$method();
            }
            return $values;
        } else {
            throw new ExceptionDb('Nie istnieje tabela dla tego obiektu');
        }
    }
    
    /*
     * zwraca id podanego obiektu
     */
    public function getIdObject($object){
        $class = get_class($object);
        $method = 'getId' . $class;
        return $object->$method();
    }
    
    /*
     * zwraca ostatnie id z podanej tabeli
     */
    public function getLastId($table){
        if($this->getContract()->isTable(strtolower($table))){
            return floatval(parent::getLastId($table));
        } else {
            throw new ExceptionDb("Nie istnieje tabela dla tego obiektu");
        }
    }
}

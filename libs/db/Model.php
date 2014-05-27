<?php
/*
 * Klasa którą dziedziczą klasy reprezentujące dane tabele bazy danych.
 * Umożliwia proste zapisanie, aktualizacje, usuwanie
 * rekordu odpowiedzialnego za dany obiekt
 */
class Model {
    /*
     * Dodanie rekordu danego obiektu do bazy danych,
     * w przypadku kiedy istnieje jego wpis oraz posiada id
     * rekord zostaje zaktualizowany
     */
    public function save(){
        $db = $this->getDbManager();
        $class = get_class($this);
        if($id = $db->db_helper->getIdObject($this)){
            if(!$db->update($this)){
                return false;
            }
        } else {
            if($db->add($this)){
                $id = $db->db_helper->getLastId($class);
            } else {
                return false;
            }
        }
        $object = $db->getObjectById($class, $id);
        $cols = $db->db_helper->getContract()->getColumns($class);
        
        foreach ($cols as $col) {
            $set = 'set' . ucfirst($col);
            $get = 'get' . ucfirst($col);
            $this->$set($object->$get());
        }
        return true;
    }
    
    /*
     * usunięcie rekordu z bazy danych danego obiektu
     */
    public function delete(){
        $db = $this->getDbManager();
        if($db->delete($this)){
            $this->__destruct();
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * sprawdzenie czy istnieje globalny obiekt DbManager potrzebny do wykonania
     * powyższych metod
     */
    private function getDbManager() {
        if(!isset($GLOBALS['dbManager']) || !is_a($GLOBALS['dbManager'], 'DbManager')){
            throw new ExceptionDb('Obiekt globalny dbManager nie istnieje');//$GLOBALS['dbManager'] = new DbManager();
        } else {
            return $GLOBALS['dbManager'];
        }
    }
    
    /*
     * czyszczenie danych obiektu
     */
    function __destruct() {
        $db = $this->getDbManager();
        $class = get_class($this);
        $cols = $db->db_helper->getContract()->getColumns($class);
        foreach ($cols as $col) {
            $set = 'set'.ucfirst($col);
            $this->$set(NULL);
        }
    }
}

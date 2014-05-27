<?php

class Group extends Model implements GroupFields{
    private $idGroup;
    private $groupName;
    
    function __construct($groupName, $id = null) {
        $this->idGroup = $id;
        $this->groupName = $groupName;
    }

    public function getIdGroup() {
        return $this->idGroup;
    }
    
    public function setIdGroup($id) {
        $this->idGroup = $id;
    }

    public function getGroupName() {
        return $this->groupName;
    }

    public function setGroupName($groupName) {
        $this->groupName = $groupName;
    }


}

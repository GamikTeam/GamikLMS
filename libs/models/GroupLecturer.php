<?php

class GroupLecturer  extends Model implements GroupLecturerFields{
    private $idGroupLecturer;
    private $roleId;
    private $userId;
    
    function __construct($roleId, $userId, $idGroupLecturer = null) {
        $this->idGroupLecturer = $idGroupLecturer;
        $this->roleId = $roleId;
        $this->userId = $userId;
    }

    public function getIdGroupLecturer() {
        return $this->idGroupLecturer;
    }

    public function getRoleId() {
        return $this->roleId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setIdGroupLecturer($idGroupLecturer) {
        $this->idGroupLecturer = $idGroupLecturer;
    }

    public function setRoleId($roleId) {
        $this->roleId = $roleId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }


}

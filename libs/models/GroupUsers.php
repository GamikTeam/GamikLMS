<?php

class GroupUsers  extends Model implements GroupUsersFields{
    private $idGroupUsers;
    private $userId;
    private $groupId;
    
    function __construct($userId, $groupId, $idGroupUsers = null) {
        $this->idGroupUsers = $idGroupUsers;
        $this->userId = $userId;
        $this->groupId = $groupId;
    }

    public function getIdGroupUsers() {
        return $this->idGroupUsers;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getGroupId() {
        return $this->groupId;
    }

    public function setIdGroupUsers($idGroupUsers) {
        $this->idGroupUsers = $idGroupUsers;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }


}

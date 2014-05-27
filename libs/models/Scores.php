<?php

class Scores  extends Model implements ScoresFields{
    private $idScores;
    private $points;
    private $awards;
    private $achieve;
    private $userId;
    private $groupCourseId;
    
    function __construct($points, $awards, $achieve, $idUsers, $idGroupCourse, $idScores = null) {
        $this->idScores = $idScores;
        $this->points = $points;
        $this->awards = $awards;
        $this->achieve = $achieve;
        $this->userId = $idUsers;
        $this->groupCourseId = $idGroupCourse;
    }

    public function getIdScores() {
        return $this->idScores;
    }

    public function getPoints() {
        return $this->points;
    }

    public function getAwards() {
        return $this->awards;
    }

    public function getAchieve() {
        return $this->achieve;
    }

    public function setIdScores($idScores) {
        $this->idScores = $idScores;
    }

    public function setPoints($points) {
        $this->points = $points;
    }

    public function setAwards($awards) {
        $this->awards = $awards;
    }

    public function setAchieve($achieve) {
        $this->achieve = $achieve;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getGroupCourseId() {
        return $this->groupCourseId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setGroupCourseId($groupCourseId) {
        $this->groupCourseId = $groupCourseId;
    }
}

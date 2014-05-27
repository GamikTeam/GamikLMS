<?php

class GroupCourse  extends Model implements GroupCourseFields{
    private $idGroupCourse;
    private $dataStart;
    private $dataStop;
    private $groupLecturerId;
    private $courseId;
    private $groupId;
    
    function __construct($dataStart, $dataStop, $groupLecturerId, $courseId, $groupId, $idGroupCourse = null) {
        $this->idGroupCourse = $idGroupCourse;
        $this->dataStart = $dataStart;
        $this->dataStop = $dataStop;
        $this->groupLecturerId = $groupLecturerId;
        $this->courseId = $courseId;
        $this->groupId = $groupId;
    }

    public function getIdGroupCourse() {
        return $this->idGroupCourse;
    }

    public function getDataStart() {
        return $this->dataStart;
    }

    public function getDataStop() {
        return $this->dataStop;
    }

    public function getGroupLecturerId() {
        return $this->groupLecturerId;
    }

    public function getCourseId() {
        return $this->courseId;
    }

    public function getGroupId() {
        return $this->groupId;
    }

    public function setIdGroupCourse($idGroupCourse) {
        $this->idGroupCourse = $idGroupCourse;
    }

    public function setDataStart($dataStart) {
        $this->dataStart = $dataStart;
    }

    public function setDataStop($dataStop) {
        $this->dataStop = $dataStop;
    }

    public function setGroupLecturerId($groupLecturerId) {
        $this->groupLecturerId = $groupLecturerId;
    }

    public function setCourseId($courseId) {
        $this->courseId = $courseId;
    }

    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }


}

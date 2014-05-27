<?php

class Lesson  extends Model implements LessonFields{
    private $idLesson;
    private $nr;
    private $filePath;
    private $date;
    private $courseId;
    
    function __construct($nr, $filePath, $date, $courseId, $idLesson) {
        $this->idLesson = $idLesson;
        $this->nr = $nr;
        $this->filePath = $filePath;
        $this->date = $date;
        $this->courseId = $courseId;
    }

    public function getIdLesson() {
        return $this->idLesson;
    }

    public function getNr() {
        return $this->nr;
    }

    public function getFilePath() {
        return $this->filePath;
    }

    public function getDate() {
        return $this->date;
    }

    public function getCourseId() {
        return $this->courseId;
    }

    public function setIdLesson($idLesson) {
        $this->idLesson = $idLesson;
    }

    public function setNr($nr) {
        $this->nr = $nr;
    }

    public function setFilePath($filePath) {
        $this->filePath = $filePath;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setCourseId($courseId) {
        $this->courseId = $courseId;
    }


}

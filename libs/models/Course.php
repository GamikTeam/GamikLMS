<?php

class Course  extends Model implements CourseFields{
    private $idCourse;
    private $descripton;
    private $folderPath;
    private $templatePath;
    
    function __construct($descripton, $folderPath, $templatePath, $idCourse = null) {
        $this->idCourse = $idCourse;
        $this->descripton = $descripton;
        $this->folderPath = $folderPath;
        $this->templatePath = $templatePath;
    }

    public function getIdCourse() {
        return $this->idCourse;
    }

    public function getDescription() {
        return $this->descripton;
    }

    public function getFolderPath() {
        return $this->folderPath;
    }

    public function getTemplatePath() {
        return $this->templatePath;
    }

    public function setIdCourse($idCourse) {
        $this->idCourse = $idCourse;
    }

    public function setDescription($descripton) {
        $this->descripton = $descripton;
    }

    public function setFolderPath($folderPath) {
        $this->folderPath = $folderPath;
    }

    public function setTemplatePath($templatePath) {
        $this->templatePath = $templatePath;
    }


}

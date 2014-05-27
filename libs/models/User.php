<?php
class User extends Model implements UserFields{
    
    private $idUser;
    private $login;
    private $password;
    private $title;
    private $name;
    private $surname;
    private $email;
    private $createCourse;
    
    function __construct($login, $password, $title, $name, $surname, $email, $createCourse, $idUser = null) {
        $this->idUser = $idUser;
        $this->login = $login;
        $this->password = $password;
        $this->title = $title;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->createCourse = $createCourse;
    }

    public function getIdUser() {
        return $this->idUser;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getCreateCourse() {
        return $this->createCourse;
    }
    
    public function getFullName(){
        return $this->title . " " . $this->name . " " . $this->surname;
    }

    public function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setCreateCourse($createCourse) {
        $this->createCourse = $createCourse;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function setPassword($password) {
        $this->password = $password;
    }


}

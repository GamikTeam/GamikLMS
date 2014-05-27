<?php

/* 
 * Klasa do postawowych operacji na bazie danych
 */

class DbManager{
    public $db_helper;
    private $db_criteria;
    
    /*
     * wej: sciezka do pliku .xml ze schematem bazy danych
     */
    function __construct($db_contract = 'config/db_contract.xml') {
        $this->db_criteria = new DbContract($db_contract);
        $this->db_helper = new DbHelper($this->db_criteria);
    }

    public function createCriteria($class){
        return new DbCriteria($class, $this->db_criteria);
    }
    
    /*
     * dodanie obiektu do bazy danych
     * wej: obiekt reprezentujący tabele z bazy danych
     * wyj: ilość zaktualizowanych rekordów
     *  false gdy wpis obiektu już istnieje
     *  (o tym decydują kolumny unikalne oznaczone w pliku xml)
     */
    public function add($object){
        return $this->db_helper->add($object);
    }
    
    /*
     * zaktualizowanie danych o obiekcie w bazie danych
     * wej: obiekt reprezentujący tabele z bazy danych
     * wyj: ilość zaktualizowanych rekordów
     *  false gdy obiekt nie ma reprezentacji tabeli w bazie danych lub nie posiada id
     */
    public function update($object){
        return $this->db_helper->update($object);
    }
    
    /*
     * usunięcie obiektu z bazy danych
     * wej: obiekt reprezentujący tabele z bazy danych
     * wyj: ilość zaktualizowanych rekordów
     *  false gdy obiekt nie posiada id
     */
    public function delete($object){
        return $this->db_helper->delete($object);
    }
    
    /*
     * pobranie obiektu z bazy danych
     * wej: class - wybrana klasa, id obiektu
     * wyj: obiekt reprezentujący tabele z bazy danych
     */
    public function getObjectById($class, $id){
        $criteria = $this->createCriteria($class);
        $criteria->addWhereCompare('id'.ucfirst($class), $id);
        return $this->db_helper->get($criteria);
    }
    
    /*
     * pobranie obiektów z bazy danych
     * wej: class - wybrana klasa
     * wyj: tablica obiektów reprezentujących tabele z bazy danych
     */
    public function getAllObjects($class){
        $criteria = $this->createCriteria($class);
        return $this->db_helper->get($criteria);
    }
    
    
    //-----------------------------------------
    //metody ułatwiające działanie na obiektach 
    //tworzą kryteria i zwracają obiekty lub tablice obiektów
    //(nie wiem czy przewidziałem wszystkie potrzebne opcje,
    //ich nazwy mówią o ich przeznaczeniu)
    
    /*
     * Users
     */
    public function getUserById($id){
        return $this->getObjectById(User::_CLASS, $id);
    }
    
    public function getUserByLogin($login){
        $criteria = $this->createCriteria(User::_CLASS);
        $criteria->addWhereCompare(User::LOGIN, $login);
        return $this->db_helper->get($criteria);
    }
    
    public function getUserByLoginAndPassword($login, $password){
        $criteria = $this->createCriteria(User::_CLASS);
        $criteria->addWhereCompare(User::LOGIN, $login);
        $criteria->addWhereCompare(User::PASSWORD, $password);
        return $this->db_helper->get($criteria);
    }
    
    public function getAllUser(){
        return $this->getAllObjects(User::_CLASS);
    }
    
    public function getUsersByGroup(Group $group){
        $criteria = $this->createCriteria(User::_CLASS);
        $criteria->addJoin(DbCriteria::INNER_JOIN, Groupusers::USER_ID, Groupusers::TABLE_GROUPUSERS, User::ID);
        $criteria->addJoin(DbCriteria::INNER_JOIN, Group::ID, Group::TABLE_GROUP, Groupusers::GROUP_ID, Groupusers::TABLE_GROUPUSERS);
        $criteria->addWhereCompare(Group::ID, $group->getIdGroup(), Group::TABLE_GROUP);
        return $this->db_helper->get($criteria);
    }
    
    public function addUserToGroup(User $user, Group $group){
        $group_user = new Groupusers($user->getIdUser(), $group->getIdGroup());
        return $this->db_helper->add($group_user);
    }
    
    public function deleteUserFromGroup(User $user, Group $group){
        $criteria = $this->createCriteria(Groupusers::_CLASS);
        $criteria->addWhereCompare(Groupusers::GROUP_ID, $group->getIdGroup());
        $criteria->addWhereCompare(Groupusers::USER_ID, $user->getIdUser());
        $group_user = $this->db_helper->get($criteria);
        return $this->db_helper->delete($group_user);
    }
    
    /*
     * Group
     */
    public function getGroupById($id){
        return $this->getObjectById(Group::_CLASS, $id);
    }
    
    public function getGroupByName($name) {
        $criteria = $this->createCriteria(Group::_CLASS);
        $criteria->addWhereCompare(Group::NAME, $name);
        return $this->db_helper->get($criteria);
    }
    
    public function getGroupsByUser(User $user){
        $criteria = $this->createCriteria(Group::_CLASS);
        $criteria->addJoin(DbCriteria::INNER_JOIN, Groupusers::GROUP_ID, Groupusers::TABLE_GROUPUSERS, Group::ID);
        $criteria->addJoin(DbCriteria::INNER_JOIN, User::ID, User::TABLE_USER, Groupusers::USER_ID, Groupusers::TABLE_GROUPUSERS);
        $criteria->addWhereCompare(User::ID, $user->getIdUser(), User::TABLE_USER);
        return $this->db_helper->get($criteria);
    }
    
    public function getAllGroups(){
        return $this->getAllObjects(Group::_CLASS);
    }
    
    /*
     * Scores
     */
    public function getScoresById($id) {
        return $this->getObjectById(Scores::_CLASS, $id);
    }
    
    public function getScoresByUser(User $user){
        $criteria = $this->createCriteria(Scores::_CLASS);
        $criteria->addWhereCompare(Scores::USER_ID, $user->getIdUser());
        return $this->db_helper->get($criteria);
    }
    
    public function getScoresByUserAndGroupCourse(User $user, Groupcourse $group_course){
        $criteria = $this->createCriteria(Scores::_CLASS);
        $criteria->addWhereCompare(Scores::USER_ID, $user->getIdUser());
        $criteria->addWhereCompare(Scores::GROUP_COURSE_ID, $group_course->getIdGroupCourse());
        return $this->db_helper->get($criteria);
    }
    
    public function getScoresByUserAndCourse(User $user, Course $course){
        $criteria = $this->createCriteria(Scores::_CLASS);
        $criteria->addJoin(DbCriteria::INNER_JOIN, Groupcourse::ID, Groupcourse::TABLE_GROUPCOURSE, Scores::GROUP_COURSE_ID);
        $criteria->addJoin(DbCriteria::INNER_JOIN, Course::ID, Course::TABLE_COURSE, Groupcourse::COURSE_ID, Groupcourse::TABLE_GROUPCOURSE);
        $criteria->addWhereCompare(Scores::USER_ID, $user->getIdUser());
        $criteria->addWhereCompare(Course::ID, $course->getIdCourse(), Course::TABLE_COURSE);
        return $this->db_helper->get($criteria);
    }
    
    /*
     * Lesson
     */
    
    public function getLessonById($id) {
        return $this->getObjectById(Lesson::_CLASS, $id);
    }
    
    public function getLessonsByCourse(Course $course) {
        $criteria = $this->createCriteria(Lesson::_CLASS);
        $criteria->addWhereCompare(Lesson::COURSE_ID, $course->getIdCourse());
        return $this->db_helper->get($criteria);
    }
    
    public function getLessonsByNrInCourse($nr, Course $course) {
        $criteria = $this->createCriteria(Lesson::_CLASS);
        $criteria->addWhereCompare(Lesson::COURSE_ID, $course->getIdCourse());
        $criteria->addWhereCompare(Lesson::NR, $nr);
        return $this->db_helper->get($criteria);
    }
    
    public function getAllLessons(){
        return $this->getAllObjects(Lesson::_CLASS);
    }


    /*
     * Course
     */
    public function getCourseById($id) {
        return $this->getObjectById(Course::_CLASS, $id);
    }
    
    public function getAllCourse() {
        return $this->getAllObjects(Course::_CLASS);
    }
    
    public function getCourseByGroupCourse(Groupcourse $group_course){
        $criteria = $this->createCriteria(Course::_CLASS);
        $criteria->addWhereCompare(Course::ID, $group_course->getCourseId());
    }
    
    /*
     * GroupLecturer
     */
    public function getGroupLecturerById($id){
        return $this->getObjectById(Grouplecturer::_CLASS, $id);
    }
    
    public function getAllGroupLecturer(){
        return $this->getAllObjects(Grouplecturer::_CLASS);
    }

    public function getGroupLecturerByUser(User $user) {
        $criteria = $this->createCriteria(Grouplecturer::_CLASS);
        $criteria->addWhereCompare(Grouplecturer::USER_ID, $user->getIdUser());
        return $this->db_helper->get($criteria);
    }
    
    public function getGroupLecturerByGroupCourse(Groupcourse $group_course) {
        $criteria = $this->createCriteria(Grouplecturer::_CLASS);
        $criteria->addWhereCompare(Grouplecturer::ID, $group_course->getGroupLecturerId());
        return $this->db_helper->get($criteria);
    }

    /*
     * Contents
     */
    public function getContentById($id){
        return $this->getObjectById(Content::_CLASS, $id);
    }
    
    public function getAllContents(){
        return $this->getAllObjects(Content::_CLASS);
    }
    
    public function getContentsByLang($id_lang){
        $criteria = $this->createCriteria(Content::_CLASS);
        $criteria->addWhereCompare(Contents::LANG_ID, $id_lang);
        return $this->db_helper->get($criteria);
    }
    
    /*
     * GroupCourse
     */
    public function getGroupCourseById($id){
        return $this->getObjectById(GroupCourse::_CLASS, $id);
    }
    

}
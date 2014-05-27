<?php

require_once 'config/autoload.php';
spl_autoload_register('loader');

$dbManager = new DbManager();



//------dodawanie obiektow-----------
// /*
$user = new User('lukasz', 'haslo', '', 'Łukasz', 'Biaduń', 'email', 0);
$user->save();

echo "<pre>";
var_dump($user);

$group = new Group('users');
$group->save();

var_dump($group);

//jezeli wywolamy drugi raz to nie doda poniewaz istnieja

// */



//-----pobieranie obiektow
 /*
$user = $dbManager->getUserByLogin('lukasz');

echo "<pre>";
var_dump($user);

$group = $dbManager->getGroupByName('users');

var_dump($group);

//-----przyklad dodania uzytkownika do grupy
$dbManager->addUserToGroup($user, $group);
$groups = $dbManager->getGroupsByUser($user);

var_dump($groups);

 */



//-----aktualizacja obiektow
 /*
$user = $dbManager->getUserByLogin('lukasz');
$user->setName('tomek');
$user->setSurname('nowak');

var_dump( $user->save() );

 */



//-----usuwanie obiektow
 /*
$user = $dbManager->getUserByLogin('lukasz');
$group = $dbManager->getGroupByName('users');

$dbManager->deleteUserFromGroup($user, $group); //usuniecie zaleznosci

var_dump( $user->delete() ); //usuniecie uzytkownika

 */




?>

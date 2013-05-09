<?php

namespace lib\users;
use lib\users\User;
use lib\db\SQL;

class UserInscription extends User {
  
  private $ip;
  private $date;
    
  public function __construct($id = null) {
    if ($id != null) {
      $query = SQL::sql()->prepare('SELECT login, ip, date FROM fsc_users_inscription WHERE id = ?');
      $query->execute(array($id+0));
      $data = $query->fetch();
      $this->id = $id;
      $this->login = $data['login'];
      $this->ip = $data['ip'];
      $this->date = $data['date'];
      $query->closeCursor();
      $this->created = true;
    }
    else
      $created = false;
  }
  
  public function create() {
    if (! $this->created) {
      $query = SQL::sql()->prepare('INSERT INTO fsc_users_inscription(login, password, ip) VALUES(:login, :password, :ip)');
      $prepare = array(
        'login' => addslashes($this->login),
        'password' => $this->password,
        'ip' => '0.0.0.0'
        );
      $rep = $query->execute($prepare);
      $query->closeCursor();
      $query = SQL::sql()->prepare('SELECT id FROM fsc_users_inscription WHERE login = ?');
      $query->execute(array($this->login));
      $data = $query->fetch();
      $this->id = $data['id'];
      $query->closeCursor();
      $this->created = true;
      return true;
    }
    return false;
  }
  
    public static function getID($login) {
    $query = SQL::sql()->prepare('SELECT id FROM fsc_users_inscription WHERE login = ?');
    $query->execute(array(htmlspecialchars($login)));
    $data = $query->fetch();
    return $data['id'];
  }
  
  public static function isAuthorizedUser($login, $pwd) {
    $query = SQL::sql()->query('SELECT login, password FROM fsc_users_inscription');
    $logins = array();
    $passwords = array();
    while ($data = $query->fetch()) {
      $logins[] = $data['login'];
      $passwords[] = $data['password'];
    }
    $query->closeCursor();
    return in_array(htmlspecialchars($login), $logins) && in_array(User::hash_password($pwd, htmlspecialchars($login)), $passwords);
  }
  
  protected function getLogins() {
    $query = SQL::sql()->query('SELECT login FROM fsc_users_inscription');
    $logins = array();
    while ($data = $query->fetch())
      $logins[] = $data['login'];
    return $logins;
  }
  
  public function historize($ip) {
    if ($this->created) {
      $query = SQL::sql()->prepare('UPDATE fsc_users_inscription SET ip = :ip, date = NOW() WHERE id = :id');
      $query->execute(array(
        'ip' => $ip,
        'id' => $this->id
      ));
      $query->closeCursor();
    }
  }
  
}

?>
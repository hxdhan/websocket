<?php

require_once('./websockets.php');

class LoginServer extends WebSocketServer {
  //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.
  protected $tickets = array();
  
  protected function process ($user, $message) {
    //var_dump($this->users);
    //var_dump($user);
    if(preg_match('/^ticket:([0-9a-z]*)$/',$message,$match)) {
      $this->tickets[$match[1]] = $user;
     // var_dump($this->tickets);
    }
    if(preg_match('/^login_ok:([0-9a-z]*)$/',$message,$match)) {
      //var_dump($this->tickets);
      $u = $this->tickets[$match[1]];
      //var_dump($u);
      $m = "登录成功!，请点击<a href='http://yc.lanyoga.com/webadm/index.php'>登录</a>";
      $this->send($u,$m);
    }

    //$this->send($user,$message);
    $this->stdout($message);
  }
  
  protected function connected ($user) {
    // Do nothing: This is just an echo server, there's no need to track the user.
    // However, if we did care about the users, we would probably have a cookie to
    // parse at this step, would be looking them up in permanent storage, etc.
  }
  
  protected function closed ($user) {
    // Do nothing: This is where cleanup would go, in case the user had any sort of
    // open files or other objects associated with them.  This runs after the socket 
    // has been closed, so there is no need to clean up the socket itself here.
  }
}

$login = new LoginServer("0.0.0.0","8090");

try {
  $login->run();
}
catch (Exception $e) {
  $login->stdout($e->getMessage());
}


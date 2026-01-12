<?php

namespace Elmasry\Support;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Mark old flash messages for removal
        $flashMessages = $_SESSION['flash_message'] ?? [];
        foreach($flashMessages as $key => &$flashMessage){
            $flashMessage['remove'] = true;
        }
        $_SESSION['flash_message'] = $flashMessages;
    }
    
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }
    
    public function remove($key)
    {
        if($this->has($key)){
            unset($_SESSION[$key]);
        }
    }
    
    public function setFlash($key, $message) 
    {
        $_SESSION['flash_message'][$key] = [
            'remove' => false,
            'content' => $message
        ];
    }
    
    public function getFlash($key)
    {
        return $_SESSION['flash_message'][$key]['content'] ?? null;
    }
    
    public function hasFlash($key)
    {
        return isset($_SESSION['flash_message'][$key]);
    }
    
    public function __destruct() 
    {
        $this->removeFlashMessages();
    }
    
    private function removeFlashMessages()
    {
        $flashMessages = $_SESSION['flash_message'] ?? [];
        foreach ($flashMessages as $key => $flashMessage){
            if($flashMessage['remove']){
                unset($_SESSION['flash_message'][$key]);
            }
        }
    }
}
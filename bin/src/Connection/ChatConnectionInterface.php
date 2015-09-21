<?php

namespace Chat\Connection;

interface ChatConnectionInterface
{
    public function getConnection();

    public function getName();
    
    public function getId();
    
    public function getHistory();

    public function setName($name);
    
    public function setId($id);

    public function sendMsg($sender,$id, $msg);
}

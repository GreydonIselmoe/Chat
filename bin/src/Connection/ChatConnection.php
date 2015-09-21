<?php

namespace Chat\Connection;

use Chat\Repository\ChatRepositoryInterface;
use Ratchet\ConnectionInterface;

class ChatConnection implements ChatConnectionInterface
{
    /**
     * The ConnectionInterface instance
     *
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * The username of this connection
     *
     * @var string
     */
    private $name;

    private $id;
    /**
     * The ChatRepositoryInterface instance
     *
     * @var ChatRepositoryInterface
     */
    private $repository;

    /**
     * ChatConnection Constructor
     *
     * @param ConnectionInterface     $conn
     * @param ChatRepositoryInterface $repository
     * @param string                  $name
     */
    public function __construct(ConnectionInterface $conn, ChatRepositoryInterface $repository, $name = "",$id = "")
    {
        $this->connection = $conn;
        $this->name = $name;
        $this->id = $id;
        
        $this->repository = $repository;
    }



    public function getHistory(){
         $mysqli = new \mysqli("127.0.0.1", "root", "ur2slow", "dice");
         $currID = $mysqli->query("SELECT `id` FROM `chat` ORDER BY `id` DESC LIMIT 1");
        $currID = $currID->fetch_assoc();
         $threshold = $currID['id'] - 30;
         $messages = $mysqli->query("SELECT `id`,`sender`,`content`,`alias`,`time` FROM `chat` WHERE `id`>'$threshold' ORDER BY `id` LIMIT 200");
          
        
            echo ' boom boom bing ';
            while ($message = $messages->fetch_assoc()){
        if ($message['content'] !== ""){
            $this->send([
            'action'   => 'historymessage',
            'username' => $message['alias'],
            'id'       => $message['sender'],
            'msg'      => $message['content'],
            'time'     => substr($message['time'],10,9),
        ]);
        
        }
        
            }
    }
    
    /**
     * Sends a message through the socket
     *
     * @param string $sender
     * @param string $msg
     * @return void
     */
     
     
    public function sendMsg($sender,$id, $msg)
    {
        $this->send([
            'action'   => 'message',
            'username' => $sender,
            'id'       => $id,
            'msg'      => $msg
        ]);
    }

    /**
     * Get the connection instance
     *
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the name for this connection
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        if ($name === "")
            return;
    echo '  entering setName ChatConnection via ChatConnectionInterface';
        // Check if the name exists already
        if ($this->repository->getClientByName($name) !== null)
        { echo ' name exists: '.$name;
            $this->send([
                'action'   => 'setname',
                'success'  => false,
                'username' => $this->name
            ]);

            return;
        }

        $this->name = $name;
echo 'this-> name set to: '.$name;
        $this->send([
            'action'   => 'setname',
            'success'  => true,
            'username' => $this->name
        ]);
    }
    
     public function setId($id)
    {
        if ($id === "")
            return;
    echo '  entering setId ChatConnection via ChatConnectionInterface';
        // Check if the name exists already
    

        $this->id = $id;
echo 'this-> id set to: '.$id;
        $this->send([
            'action'   => 'setid',
            'success'  => true,
            'id' => $this->id
        ]);
    }

    /**
     * Get the username of the connection
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
public function getId()
    {
        return $this->id;
    }
    /**
     * Send data through the socket
     *
     * @param  array  $data
     * @return void
     */
    private function send(array $data)
    {
        $this->connection->send(json_encode($data));
    }
}

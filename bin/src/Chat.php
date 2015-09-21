<?php





namespace Chat;

use Chat\Repository\ChatRepository;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
   
    /**
     * The chat repository
     *
     * @var ChatRepository
     */
    var $mysqli;
    protected $repository;

    /**
     * Chat Constructor
     */
    public function __construct()
    {
        $this->repository = new ChatRepository;
        
      
        
   
    }

    /**
     * Called when a connection is opened
     *
     * @param ConnectionInterface $conn
     * @return void
     */
  
    public function onOpen(ConnectionInterface $conn)
    {
        $this->repository->addClient($conn);
      
        $currClient = $this->repository->getClientByConnection($conn);
        
       
    }

    /**
     * Called when a message is sent through the socket
     *
     * @param ConnectionInterface $conn
     * @param string              $msg
     * @return void
     */
    public function onMessage(ConnectionInterface $conn, $msg)
    {
        // Parse the json
        $data = $this->parseMessage($msg);
        $currClient = $this->repository->getClientByConnection($conn);

        // Distinguish between the actions
        if ($data->action === "setname")
        {
          
             $mysqli = new \mysqli("127.0.0.1", "root", "ur2slow", "dice");
             $hash = $data->uniquehash;
             echo $data->uniquehash;
           $player = $mysqli->query("SELECT * FROM `players` WHERE `hash`='$hash' LIMIT 1");
          
            $player = $player->fetch_assoc();
                 echo $player['alias'];
        $currClient->setName($player['alias']);
        $currClient->setId($player['id']);
        }
        else if ($data->action === "message")
        {
            // We don't want to handle messages if the name isn't set
            if ($currClient->getName() === "")
                return;
         
            foreach ($this->repository->getClients() as $client)
            {
                // Send the message to the clients if, except for the client who sent the message originally
               // if ($currClient->getName() !== $client->getName())
                    $client->sendMsg($currClient->getName(),$currClient->getId(),  filter_var($data->msg, FILTER_SANITIZE_STRING));
                        
                        
            }
              // if (){
               $mysqli = new \mysqli("127.0.0.1", "root", "ur2slow", "dice");
                  $player = $mysqli->query("SELECT * FROM `players` WHERE `hash`='$' LIMIT 1");
          $test =  $mysqli->query("INSERT INTO `chat` (`sender`,`content`,`alias`,`isglobal`) VALUES ('".$currClient->getId()."','".filter_var($data->msg, FILTER_SANITIZE_STRING)."','".$currClient->getName()."',1)");
          //  }
            echo $test;
        }
        
        else if ($data->action ==="getHistory") {
            
            $currClient->getHistory();
        }
    }

    /**
     * Parse raw string data
     *
     * @param string $msg
     * @return stdClass
     */
    private function parseMessage($msg)
    {
        return json_decode($msg);
    }

    /**
     * Called when a connection is closed
     *
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->repository->removeClient($conn);
    }

    /**
     * Called when an error occurs on a connection
     *
     * @param ConnectionInterface $conn
     * @param Exception           $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "The following error occured: " . $e->getMessage();

        $client = $this->repository->getClientByConnection($conn);

        // We want to fully close the connection
        if ($client !== null)
        {
            $client->getConnection()->close();
            $this->repository->removeClient($conn);
        }
    }
}

<?php
/**
 * PHPStatic's Server interface.
 * 
 * @package PHPStatic
 * @author Sebastiano Racca
 */

namespace PHPStatic;

class Server {
    public string $address;
    public int $port;

    /**
     * Server's constructor.
     * 
     * @param string $addrees    Server's ip address.
     * @param int|null $port     Server's port number, pass null to let the class decide an open port.
     * 
     * @throws UnavailablePort   If the provided port is already in use.
     */
    public function __construct(string $address, ?int $port) {
        $this->address = $address;

        if(isset($port)){
            
            if(!$this->isPortOpen($port)){
                throw new Exceptions\UnavailablePort("Port $port is already in use.");
            }

        } else {
            $port = $this->findOpenPort();
        }

        $this->port = $port;
    }

    /**
     * Finds an open port.
     * 
     * @param int $start                  The first port to check.
     * @param int $end                    The last port to check.
     * 
     * @return int                        The first open port that was found.
     * 
     * @throws NoAvailablePortException   If there are no available ports in the specified range.
     */
    private function findOpenPort(int $start = 8000, int $end = 9000): int{
        for ($port = $start; $port <= $end; $port++) {
            if ($this->isPortOpen($port)) {
                return $port;
            }
        }
    
        throw new Exceptions\NoAvailablePortException("No available port found.");
    }
    
    /**
     * Checks if a port is open
     * 
     * @param int $port   The port to be checked.
     * 
     * @return bool       Wheter the port is open or not.
     */
    private function isPortOpen(int $port): bool{
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        if ($socket === false) {
            return false;
        }
        
        $result = @socket_bind($socket, '127.0.0.1', $port);
        @socket_close($socket);
    
        return $result;
    }

    /**
     * Starts the web server.
     * 
     * @param string $directory   The directory of the files for the web server.
     * 
     * @throws ServerNotStarted   If there was an error.
     */
    public function start(string $directory): void{
        exec("php -S $this->address:$this->port -t $directory -q", $output, $returnCode);

        if($returnCode !== 0){
            throw new Exceptions\ServerNotStarted("Couldn't start web server [$returnCode]: " . json_encode($output));
        }
    }
    
}
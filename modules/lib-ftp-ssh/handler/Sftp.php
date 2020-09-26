<?php
/**
 * SSH2-SFTP
 * @package lib-ftp-ssh
 * @version 0.0.1
 */

namespace LibFtpSsh\Handler;

use \Mim\Library\Fs;

class Sftp implements \LibFtp\Iface\Handler
{
    private $conn;
    private $conn_sftp;
    private $error;

    private function _bool($cmd): bool{
        $res = $this->_exec($cmd);
        return !!$res;
    }
    private function _exec($cmd): ?string{
        $cmd = escapeshellcmd($cmd);
        $stream_main = ssh2_exec($this->conn, $cmd);
        $stream_error = ssh2_fetch_stream($stream_main, SSH2_STREAM_STDERR);

        stream_set_blocking($stream_error, true);
        stream_set_blocking($stream_main, true);

        $c_main  = stream_get_contents($stream_main, -1);
        $c_error = stream_get_contents($stream_error, -1);

        fclose($stream_error);
        fclose($stream_main);

        $c_error = trim($c_error);
        if($c_error){
            $this->error = $c_error;
            return '';
        }
        $c_main = trim($c_main);
        if(!$c_main)
            $c_main = 'success';
        return $c_main;
    }
    
    public function __construct(array $opts){
        $server = $opts['server'];

        $this->conn = ssh2_connect($server['host'], $server['port']);
        if(!$this->conn){
            $this->error = 'Unable to connect to sftp server';
            return;
        }

        if(!isset($opts['user']))
            return;

        $user = $opts['user'];

        // connect with username and password
        if(isset($user['name']) && isset($user['password'])){
            if(!@ssh2_auth_password($this->conn, $user['name'], $user['password'])){
                $this->error = 'Unable to login to the sftp server';
                return;
            }
        }

        $this->conn_sftp = ssh2_sftp($this->conn);
    }

    public function close(): void{
        $this->conn = null;
        $this->conn_sftp = null;
    }
    
    public function copy(string $source, string $target, string $type='text'): bool{
        $cmd = 'cp -v "' . $source . '" "' . $target . '"';
        return $this->_bool($cmd);
    }

    public function download(string $source, string $target, string $type='text', int $pos=0): bool{
        return ssh2_scp_recv($this->conn, $source, $target);
    }
    
    public function exists(string $path): bool{
        return $this->_bool('ls -d "' . $path . '"');
    }
    
    public function getConn(){
        return $this->conn;
    }
    
    public function getError(): ?string{
        return $this->error;
    }
    
    public function isDir(string $path): bool{
        $path = chop($path, '/') . '/';
        return $this->_bool('ls -d "' . $path . '"');
    }
    
    public function mkdir(string $path): bool{
        return $this->_bool('mkdir -p -v "' . $path . '"');
    }
    
    public function read(string $path, string $type='text', int $pos=0): ?string{
        $tmp = tempnam(sys_get_temp_dir(), 'mim-ftp-');
        if(!ssh2_scp_recv($this->conn, $path, $tmp))
            return null;
        $content = file_get_contents($tmp);
        unlink($tmp);
        return $content;
    }
    
    public function rename(string $source, string $target): bool{
        $cmd = 'mv -v "' . $source . '" "' . $target . '"';
        return $this->_bool($cmd);
    }
    
    public function rmdir(string $path): bool{
        $path = chop($path, '/') . '/';
        return $this->_bool('rm -Rv "' . $path . '"');
    }
    
    public function scan(string $path): array{
        $content = $this->_exec('ls "' . $path . '"');
        if(!$content)
            return [];
        $files = explode(PHP_EOL, trim($content));
        return $files;
    }
    
    public function unlink(string $path): bool{
        return $this->_bool('rm -v "' . $path . '"');
    }

    public function upload(string $path, string $source, string $type='text', int $pos=0): bool{
        $parent = dirname($path);
        if(!$this->mkdir($parent))
            return false;

        return ssh2_scp_send($this->conn, $source, $path);
    }
    
    public function write(string $path, $content, string $type='text', int $pos=0): bool{
        $parent = dirname($path);
        if(!$this->mkdir($parent))
            return false;

        $tmp = tempnam(sys_get_temp_dir(), 'mim-ftp-');
        Fs::write($tmp, $content);

        $result = ssh2_scp_send($this->conn, $tmp, $path);

        unlink($tmp);
        return $result;
    }
    
}
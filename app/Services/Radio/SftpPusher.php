<?php
// app/Services/Radio/SftpPusher.php (optional stub)
namespace App\Services\Radio;

class SftpPusher
{
    public function push(string $localPath): void
    {
        $cfg = config('radio.sftp');
        if (!$cfg['enabled']) return;
        // Implement with Flysystem SFTP adapter or phpseclib
        // Example (pseudocode):
        // $client = new SftpClient($cfg['host'],$cfg['port'],$cfg['username'],$cfg['password']);
        // $client->upload(storage_path('app/'.$localPath), $cfg['remote_dir'].'/'.basename($localPath));
    }
}

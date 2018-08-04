# lib-ftp-ssh

Adalah module yang memungkinan library `lib-ftp` terhubung menggunakan
sftp atau ssh. Module ini membutuhkan ekstensi
[openssl](http://php.net/manual/en/book.openssl.php) dan 
[ssh2](http://php.net/manual/en/book.ssh2.php) terpasang.

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install lib-ftp-ssh
```

## Penggunaan

Karena module ini pada dasarnya adalah module tambahan untuk
lib-ftp, maka penggunaannya sama persis dengan lib-ftp, dengan
koneksi `type` di set menjadi `sftp`.

```php
$opts = [
    'type' => 'sftp',
    'server' => [
        'host'      => 'ftp.host.ext',
        'port'      => 22
    ],
    'user' => [
        'name'      => 'user',
        'password'  => '/secret/'
    ]
];
$ftp = new LibFtp\Library\Connect($opts);
if($ftp->getError())
    deb($ftp->getError());
```
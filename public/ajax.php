<?php
error_reporting(E_ALL);

/* Get the port for the WWW service. */
$service_port = 10001;

/* Get the IP address for the target host. */
$address = '127.0.0.1';

/* Create a TCP/IP socket. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
} else {
    echo "OK.\n";
}

echo "Attempting to connect to '$address' on port '$service_port'...";
$result = socket_connect($socket, $address, $service_port);
if ($result === false) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
} else {
    echo "OK.\n";
}

$in = "4:0\n";

$in = @$_GET['do'];
if ( ! $in ) {
	$in = @$argv[1];
}
$in = "$in\n";
$out = '';

socket_write($socket, $in, strlen($in));
echo "OK.\n";
sleep( 1 );

echo "Reading response:\n\n";
while ($out = socket_read($socket, 2048)) {
    echo $out;
    if ( false !== strpos( $out , "\n" ) ) {
      break;
    }
}

echo "Closing socket...";
socket_close($socket);
echo "OK.\n\n";


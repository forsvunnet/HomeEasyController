<?php
error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$address = '127.0.0.1';
$port = 10001;

if ( get_current_user() != 'root' ) {
  die( 'Please run as root' );
}

/**
 * Create socket stuff
 */
if ( ( $sock = socket_create( AF_INET, SOCK_STREAM, SOL_TCP ) ) === false ) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}
if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}
if (socket_listen($sock, 5) === false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

/**
 * Forever loop to listen to the socket
 */
while ( 1 ) {
    $accept = $msgsock = socket_accept($sock);
    if ( $accept === false) {
        echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    /* Send instructions. */
    $msg = "Server started\n";
    socket_write($msgsock, $msg, strlen($msg));

    while ( true ) {
        $buffer = @socket_read( $msgsock, 2048, PHP_NORMAL_READ );
        if ( false === $buffer ) {
            $error = socket_last_error( $msgsock );
            if ( 104 !== $error ) {
                echo "socket_read() failed: reason: ($error) " . socket_strerror( $error ) . "\n";
                break 2;
            } else {
                // echo "Socket disconnected";
                break 1;
            }
        }
        if (!$buffer = trim($buffer)) {
            continue;
        }
        // $talkback = "PHP: You said '$buffer'.\n";
        // socket_write( $msgsock, $talkback, strlen($talkback) );
        // echo "$buffer\n";
        if ( ! preg_match('/^(\d+):(\d+)$/', $buffer, $matches) ) {
            echo "Invalid command sent: $buffer\n";
            continue;
        }
        $light = intval( $matches[1] );
        $toggle = intval( $matches[2] ) ? 'on' : 'off';
        echo "Sending to TTY: $buffer\n";
        $command = "piHomeEasy 0 22063970 %s %s";
        shell_exec( sprintf( $command, $light, $toggle ) );

    }
    socket_close($msgsock);
}
socket_close($sock);

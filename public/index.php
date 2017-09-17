<?php


$max_lights = 5;

function label_for( $num ) {
  $map = [
    0 => 'Dinner table',
    1 => 'Windows',
    2 => 'Sofa',
    3 => 'Floor lamp',
  ];

  if ( ! isset( $map[$num] ) ) {
    return 'Light '. ($num+1);
  }
  return $map[$num];
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="HandheldFriendly" content="True">
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="manifest" href="/manifest.json">
  <meta name="MobileOptimized" content="320">
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Lights</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <script type="text/javascript" src="jquery.min.js"></script>
  <style type="text/css">
    .buttons {
      transition: all 0.25s;
    }
    .buttons.clicked {
      opacity: 0.5;
    }
    .btn {
      font-size: 10px;
      display: inline-block;
      margin-right: 2.5em;
      width: 40%;
    }
    body {
      background-color: #5d8592;
    }
    body:before {
      background-image: url(https://unsplash.it/640/1280/?random);
      background-size: cover;
      content: ' ';
      display: block;
      position: fixed;
      left: 0;
      top: 0;
      right: 0;
      bottom: 0;
      opacity: 0.2;
    }
  </style>
</head>
<body>
  <div class="buttons">
    <div class="container">
      <?php for ( $i = 0; $i < $max_lights+1; $i++ ): ?>
        <div class="row">
          <a href="#" data-light_id="<?= $i; ?>" data-light_state="1" class="btn btn-5"><span><?= label_for( $i ); ?>: On</span></a>
          <a href="#" data-light_id="<?= $i; ?>" data-light_state="0" class="btn btn-5"><span><?= label_for( $i ); ?>: Off</span></a>
        </div>
      <?php endfor; ?>
      <div class="row">
        <a href="#" data-light_id="all" data-light_state="1" class="btn btn-5"><span>All: On</span></a>
        <a href="#" data-light_id="all" data-light_state="0" class="btn btn-5"><span>All: Off</span></a>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    var clicked = false;
    $( '.btn' ).click( function( e ) {
      e.preventDefault();
      if ( clicked ) {
        return;
      }
      clicked = true;
      $('.buttons').addClass( 'clicked' );

      var id = $( this ).data( 'light_id' );
      var state = $( this ).data( 'light_state' );
      var done = function() {
        clicked = false;
        $('.buttons').removeClass( 'clicked' );
      };

      if ( 'all' != id ) {
        light( id, state, done );
        return;
      }

      for ( var i = 0; i < <?= $max_lights+1; ?>; i++ ) {
        setTimeout( function(i) { return function() {
          var callback;
          if ( i == <?= $max_lights; ?> ) {
            callback = done;
          }
          console.log( i + ':' + state );
          light( i, state, callback );
        } }(i), 1500 * i );
      }
    } );

    function light( id, state, callback ) {
      var command = (1+id) + ":" + state;

      $.get( 'ajax.php', {
        do: command
      }, function() {
        if ( callback ) {
          setTimeout( callback, 1000 );
        }
      } );
    };
  </script>
</body>
</html>


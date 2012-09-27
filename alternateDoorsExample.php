<?php
session_start();

$THISPAGE = 'alternateDoorsExample';
$RESTARTLINK = $THISPAGE . '?restart=true';

class Treasure {
	public function __construct( $val = False ) {
		$this->val = $val;
		return null;
	}
	public function isEmpty() { return ! $this->val; }
}

class Door {
	public function __construct( $id, $treasure = False ) {
		$this->open = False;
		$this->id = $id;
		$this->treasure = new Treasure( $treasure );
		return null;
	}

	public function isOpen() { return $this->open; }

	public function open() {
		$this->open = True;
		return True;
	}

	public function close() {
		$this->open = False;
		return False;
	}

	public function getContent() { return $this->treasure; }
}

class Room {
	public function __construct( $nrOfDoors, $treasurePos ){
		$this->doors = array();
		$this->nrOfDoors = $nrOfDoors;

		for ( $i = 0; $i < $nrOfDoors; $i++ ) {
			$this->doors[ $i ] = new Door( $i, $i == $treasurePos );
		}

		return null;
	}

	public function getDoor( $doorNr ) {
		if ( $doorNr > $this->nrOfDoors ) { return False; }
		return $this->doors[ $doorNr ];
	}

	// Make this class act as an iterator.
	public function rewind() { $this->curPos = 0; }
	public function current() { return $this->doors[ $this->curPos ]; }
	public function next() { $this->curPos += 1; }
	public function valid() { return $this->curPos < $this->nrOfDoors; }
}

class Game {
	public function __construct( $nrOfDoors ) {
		$treasurePos = rand( 0, $nrOfDoors-1 );
		$this->room = new Room( $nrOfDoors, $treasurePos );
		$this->won = False;

		return null;
	}

	public function openDoor( $doorNr ) {
		$door  = $this->room->getDoor( $doorNr );
		if ( ! $door->isOpen() ) { $door->open(); }
		$treasure = $door->getContent();
		if ( ! $treasure->isEmpty() ) { $this->won = True; }

		return null;
	}

	public function getRoom() { return $this->room; }
	public function hasWon() { return $this->won; }
}


// Set up variables for this page.
if ( isset($_SESSION['game']) && ! isset($_GET['restart']) ) {
	$game = $_SESSION['game'];
}
else {
	unset( $_SESSION['game'] );
	$game = new Game( 7 );
	$_SESSION['game'] = $game;
}
$room = $game->getRoom();

if ( isset($_GET['openDoor']) ) {
	$game->openDoor( $_GET['openDoor'] );
}
?>

<html>
	<head><title>Halle</title></head>
	<body>
	<?php if ( $game->hasWon() ) { ?>
		<h1>You have won!</h1>
		<div>
			Click <a href="<?php print( $RESTARTLINK ) ?>">here</a> to restart the game.
		</div>
	<?php }
	else { ?>
		<h1>Kies een deur</h1>
	<?php
	} 
	for ( $room->rewind(); $room->valid(); $room->next() ) {
		$door = $room->current();

		print( '<a href="' . $THISPAGE . '?openDoor=' . $door->id . '">' );

		if ( $door->isOpen() ) {
			$treasure = $door->getContent();
			if ( ! $treasure->isEmpty() ) { print( '<img src="/images/doorTreasure.jpg" />'); }
			else { print('<img src="/images/doorOpen.jpg" />'); }
		}
		else { print('<img src="/images/doorClosed.jpg" /> '); }
		print( '</a>' );
	
	}
?>
	</body>
</html>

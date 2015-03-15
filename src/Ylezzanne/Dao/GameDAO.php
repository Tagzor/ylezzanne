<?php

namespace Ylezzanne\Dao;

use Herrera\Pdo\Pdo;
use Ylezzanne\Dao\Game;

/**
* Game DAO
*/
class GameDAO implements RepositoryInterface {
	
	/**
     * @var Pdo
     */
    private $pdo;
   
	
	public function __construct(Pdo $pdo) {
		$this->pdo  = $pdo;
	}


	/**
	 * Returns the total number of games.
	 *
	 * @return integer The total number of games.
	 *        
	 */
	public function getCount() {
		$st = $this->pdo->prepare('SELECT COUNT(*) FROM games');
		$st->execute();
		
		$count = $st->rowCount();
		
		return $count;
	}
	
	/**
	 * Returns a game matching the supplied id.
	 *
	 * @param integer $id        	
	 *
	 * @return \Ylezzanne\Dao\Game false entity object if found, false otherwise.
	 *        
	 */
	public function find($id) {
		$stmt = $this->pdo->prepare("SELECT g.* FROM games g WHERE g.id = :id");
		$stmt->execute(array(':id' => $id));
		
		$gameData = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
 			$gameData = $row;
		}
		
		return new Game($gameData['id'], $gameData['name'], $gameData['description'], $gameData['image']);
		
	}
	
	/**
	 * Returns array of games.
	 *
	 *
	 * @return array \Ylezzanne\Dao\Game.
	 *
	 */
	public function findAll() {
		
		$stmt = $app ['pdo']->prepare ( "SELECT g.* FROM games g" );
		
		$gamesData = array ();
		while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
			$game = new Game ( $row ['id'], $row ['name'], $row ['description'], $row ['image'] );
			// $app ['monolog']->addDebug ( ' ' );
			array_push ( $gamesData, $game );
		}
		
		return $gamesData;
	}
	
}

?>

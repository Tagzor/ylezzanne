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
		
		$stmt = $this->pdo->prepare ( "SELECT g.* FROM games g" );
		$stmt->execute();
		
		$gamesData = array ();
		while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
			$game = new Game ( $row ['id'], $row ['name'], $row ['description'], $row ['image'] );
			array_push ( $gamesData, $game );
		}
		
		return $gamesData;
	}
	
	/**
	 * Returns a game topScorers matching the supplied id.
	 *
	 * @param integer $id game id
	 *
	 *
	 */
	public function getTopScores($id) {
		$stmt = $this->pdo->prepare ( "SELECT st.created_at as time, u.username, st.score as score
		FROM users u INNER JOIN statistics st
		ON u.id = st.user_id JOIN games g
		ON st.game_id = g.id
		WHERE g.id = :id
		ORDER BY score DESC" );
		
		$stmt->execute(array(':id' => $id));
		
		$topScores = array ();
		while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
			$topScores[] =  $row;
		}
		
		return $topScores;
	}

	/**
	 * Returns a game TOP.
	 *
	 * @param integer $id game id
	 *
	 *
	 */
	public function topGames() {
		$stmt = $this->pdo->prepare ( "SELECT games.id, games.name, count(statistics.score) as total  
				FROM statistics INNER JOIN games ON statistics.game_id=games.id GROUP BY games.name ORDER BY count(statistics.score) DESC" );
		
		$stmt->execute ();
		
		$topGameData = array ();
		while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
			$topGameData [] = $row;
		}
		
		return $topGameDataData;
	}
	
	
	/**
	 * Returns a game statistics matching the supplied id.
	 *
	 * @param integer $id game id
	 *
	 *
	 */
	public function getStatistics($id, $username) {
		$stmt = $this->pdo->prepare ( "SELECT st.created_at AS time, st.score AS score 
		FROM users u INNER JOIN statistics st
		   ON u.id = st.user_id JOIN games g
		   ON st.game_id = g.id
		WHERE g.id = :id AND u.username= :username 
		ORDER BY time ASC");
				
		$stmt->execute(array(':id' => $id, ':username' => $username));
	
		$statisticsRows = array ();
		while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
			$statisticsRows[] =  $row;
		}
	
		return $statisticsRows;
	}
	
}

?>

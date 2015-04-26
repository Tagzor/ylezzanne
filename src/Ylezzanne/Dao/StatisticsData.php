<?php

namespace Ylezzanne\Dao;

class StatisticsData{
	
	/**
	 * Row id.
	 *
	 * @var integer
	 */
	protected $id;

	/**
	 * Game id.
	 *
	 * @var integer
	 */
	protected $gameId;

	/**
	 * User id.
	 *
	 * @var integer
	 */
	protected $userId;
	
	/**
	 * When the statistics row was created.
	 *
	 * @var DateTime
	 */
	protected $createdAt;
	
	/**
	 * Score.
	 *
	 * @var integer
	 */
	protected $score;
	
	public function __construct($id, $userId, $gameId, $createdAt, $score) {
		$this->id  = $id;
		$this->userId = $userId;
		$this->gameId = $gameId; 
		if (empty($createdAt)) {
			$this->createdAt = time();
		} else {
			$this->createdAt = $createdAt;
		}
		$this->score = $score;
	}
	
	public function getId()
	{
		return $this->id;
	}
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getUserId()
	{
		return $this->userId;
	}
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	
	public function getGameId()
	{
		return $this->gameId;
	}
	public function setGameId($gameId)
	{
		$this->gameId = $gameId;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	public function setCreatedAt(\DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
	}
	
	public function getScore()
	{
		return $this->score;
	}
	public function setScore($score)
	{
		$this->score = $score;
	}
	
}

?>
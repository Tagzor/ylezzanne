<?php

namespace Ylezzanne\Dao;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Game{
	
	/**
	 * Game id.
	 *
	 * @var integer
	 */
	protected $id;

	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 * Description.
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 * The filename of the game image.
	 *
	 * @var string
	 */
	protected $image;
	
	/**
	 * The filename of source.
	 *
	 * @var string
	 */
	protected $source;
	
	
	/**
	 * The temporary uploaded file.
	 *
	 * $this->image stores the filename after the file gets moved to "images/games/".
	 *
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile
	 */
	protected $file;
	
	
	public function __construct($id, $name, $description, $image, $source) {
		$this->id  = $id;
		$this->name = $name;
		$this->description = $description; 
		if (empty($image)) {
			$this->image = 'game_placeholder.jpg';
		} else {
			$this->image = $image;
		}
		$this->source = $source;
	}
	
	
	public function getId()
	{
		return $this->id;
	}
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getImage() {
		// Make sure the image is never empty.
		if (empty($this->image)) {
			$this->image = 'game_placeholder.jpg';
		}
		return $this->image;
	}
	public function setImage($image) {
		$this->image = $image;
	}
	
	public function getFile() {
		return $this->file;
	}
	public function setFile(UploadedFile $file = null)
	{
		$this->file = $file;
	}
	
	public function getSource()
	{
		return $this->source;
	}
	public function setSource($source)
	{
		$this->source = $source;
	}
	
}

?>
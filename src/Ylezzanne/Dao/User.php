<?php

namespace Ylezzanne\Dao;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
	/**
	 * User id.
	 *
	 * @var integer
	 */
	protected $id;

	/**
	 * Username.
	 *
	 * @var string
	 */
	protected $username;
	
	/**
	 * Salt.
	 *
	 * @var string
	 */
	protected $salt;
	/**
	 * Password.
	 *
	 * @var integer
	 */
	protected $password;
	/**
	 * Email.
	 *
	 * @var string
	 */
	protected $mail;

	/**
	 * Role.
	 *
	 * ROLE_USER or ROLE_ADMIN.
	 *
	 * @var string
	 */
	protected $role;
	
	/**
	 * The filename of the user image.
	 *
	 * @var string
	 */
	protected $image;
	
	/**
	 * When the user entity was created.
	 *
	 * @var DateTime
	 */
	protected $createdAt;
	
	/**
	 * The temporary uploaded file.
	 *
	 * $this->image stores the filename after the file gets moved to "images/users/".
	 *
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile
	 */
	protected $file;
	
	public function getId()
	{
		return $this->id;
	}
	public function setId($id)
	{
		$this->id = $id;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUsername()
	{
		return $this->username;
	}
	public function setUsername($username)
	{
		$this->username = $username;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getSalt()
	{
		return $this->salt;
	}
	public function setSalt($salt)
	{
		$this->salt = $salt;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getPassword()
	{
		return $this->password;
	}
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	public function getMail()
	{
		return $this->mail;
	}
	public function setMail($mail)
	{
		$this->mail = $mail;
	}
	public function getImage() {
		// Make sure the image is never empty.
		if (empty($this->image)) {
			$this->image = 'profile_placeholder.jpg';
		}
		return $this->image;
	}
	public function setImage($image) {
		$this->image = $image;
	}
	
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	public function setCreatedAt(\DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
	}
	
	public function getFile() {
		return $this->file;
	}
	public function setFile(UploadedFile $file = null)
	{
		$this->file = $file;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getRoles()
	{
		return array($this->getRole());
	}
	public function getRole()
	{
		return $this->role;
	}
	public function setRole($role) {
		$this->role = $role;
	}
	
	/**
	 * @inheritDoc
	 */
	public function eraseCredentials()
	{
	}
}

?>
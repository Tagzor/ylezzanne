<?php

namespace Ylezzanne\Dao;

use Herrera\Pdo\Pdo;
use Ylezzanne\Dao\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
* User DAO
*/
class UserDAO implements RepositoryInterface, UserProviderInterface {
	
	/**
     * @var Pdo
     */
    private $pdo;
    
	/**
	 *
	 * @var \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder
	 *
	 */
	protected $encoder;
	
	public function __construct(Pdo $pdo, $encoder) {
		$this->pdo  = $pdo;
		$this->encoder = $encoder;
	}


	/**
	 * Saves the user to the database.
	 *
	 * @param \Ylezzanne\DAO\User $user
	 */
	public function save($user)
	{
		$userData = array(
				'username' => $user->getUsername(),
				'password' => $user->getPassword(),
				'mail' => $user->getMail(),
				'role' => 'ROLE_ADMIN',
				
		);
		// If the password was changed, re-encrypt it.
		if (strlen($user->getPassword()) != 88) {
			$userData['salt'] = uniqid(mt_rand());
			$userData['password'] = $this->encoder->encodePassword($user->getPassword(), $userData['salt']);
		}
		
		if ($user->getId()) {
			// If a new image was uploaded, make sure the filename gets set.
// 			$newFile = $this->handleFileUpload($user);
// 			if ($newFile) {
// 				$userData['image'] = $user->getImage();
// 			}
// 			$this->pdo->update('users', $userData, array('id' => $user->getId()));
		} else {
			// The user is new, note the creation timestamp.
			$userData['created_at'] = time();
			
			$stmt = $this->pdo->prepare("INSERT INTO USERS (id, username, salt, password, role, mail, created_at) VALUES (nextval('id_seq'), :username, :salt, :password, :role, :mail, :created_at)");
			
			$stmt->execute(
					array(':username' => $userData['username'],
							':salt' => $userData['salt'],
							':password' => $userData['password'],
							':role' => $userData['role'],
							':mail' => $userData['mail'],
							':created_at' => $userData['created_at'])
			);
			
			// Get the id of the newly created user and set it on the entity.
			$id = $this->pdo->lastInsertId();
			$user->setId($id);
			// If a new image was uploaded, update the user with the new
			// filename.
// 			$newFile = $this->handleFileUpload($user);
// 			if ($newFile) {
// 				$newData = array('image' => $user->getImage());
// 				$this->pdo->update('users', $newData, array('id' => $id));
// 			}
		}
	}
	
	/**
	 * Handles the upload of a user image.
	 *
	 * @param \Ylezzanne\DAO\User $user
	 *
	 * @param boolean TRUE if a new user image was uploaded, FALSE otherwise.
	 */
	protected function handleFileUpload($user) {
		// If a temporary file is present, move it to the correct directory
		// and set the filename on the user.
		$file = $user->getFile();
		if ($file) {
			$newFilename = $user->getUsername() . '.' . $file->guessExtension();
			$file->move(YLEZZANNE_PUBLIC_ROOT . '/images/users', $newFilename);
			$user->setFile(null);
			$user->setImage($newFilename);
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Returns the total number of users.
	 *
	 * @return integer The total number of users.
	 *        
	 */
	public function getCount() {
		$st = $this->pdo->prepare('SELECT COUNT(*) FROM users');
		$st->execute();
		
		$count = $st->rowCount();
		
		return $count;
	}
	
	/**
	 * Returns a user matching the supplied id.
	 *
	 * @param integer $id        	
	 *
	 * @return \Ylezzanne\Dao\User false entity object if found, false otherwise.
	 *        
	 */
	public function find($id) {
		$stmt = $this->pdo->prepare("SELECT u.* FROM users u WHERE u.id = :id");
		$stmt->execute(array(':id' => $id));
		
		$userData = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
 			$userData = $row;
		}
		return $userData ? $this->buildUser($userData) : FALSE;
	}

	/**
	 * Returns array of users.
	 *
	 *
	 * @return array \Ylezzanne\Dao\User.
	 *
	 */
	public function findAll() {
	
		$stmt = $app ['pdo']->prepare ( "SELECT u.* FROM users u" );
	
		$usersData = $stmt->fetchAll();
		
		$users = array();
		foreach ($usersData as $userData) {
			$userId = $userData['id'];
			$users[$userId] = $this->buildUser($userData);
		}
		
		return $users;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function loadUserByUsername($username) {
		$stmt = $this->pdo->prepare("SELECT u.* FROM users u WHERE u.username = :username");
		$stmt->execute(array(':username' => $username));
		
		$usersData = $stmt->fetchAll();
		if (empty ( $usersData )) {
			throw new UsernameNotFoundException ( sprintf ( 'User "%s" not found.', $username ) );
		} 
		
		$user = $this->buildUser($usersData[0]);
		return $user;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function refreshUser(UserInterface $user) {
		$class = get_class ( $user );
		if (! $this->supportsClass ( $class )) {
			throw new UnsupportedUserException ( sprintf ( 'Instances of "%s" are not supported.', $class ) );
		}
		$id = $user->getId ();
		$refreshedUser = $this->find ( $id );
		if (false === $refreshedUser) {
			throw new UsernameNotFoundException ( sprintf ( 'User with id %s not found', json_encode ( $id ) ) );
		}
		return $refreshedUser;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function supportsClass($class) {
		return 'Ylezzanne\Dao\User' === $class;
	}
	
	/**
	 * Instantiates a user entity and sets its properties using pdo data.
	 *
	 * @param array $userData
	 *   The array of pdo data.
	 *
	 * @return \Ylezzanne\Dao\User
	 */
	protected function buildUser($userData)
	{
		$user = new User();
		$user->setId($userData['id']);
		$user->setUsername($userData['username']);
		$user->setSalt($userData['salt']);
		$user->setPassword($userData['password']);
		$user->setMail($userData['mail']);
		$user->setRole($userData['role']);
		$createdAt = new \DateTime('@' . $userData['created_at']);
		$user->setCreatedAt($createdAt);
		return $user;
	}
}

?>

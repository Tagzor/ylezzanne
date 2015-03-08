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
class UserDAO implements UserProviderInterface {
	/**
	 *
	 * @var \Herrera\Pdo\Pdo;
	 *
	 */
	protected $pdo;
	/**
	 *
	 * @var \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder
	 *
	 */
	protected $encoder;
	
	public function __construct(Pdo $pdo, $encoder) {
		$this->pdo = $pdo;
		$this->encoder = $encoder;
	}


	/**
	 * Returns the total number of users.
	 *
	 * @return integer The total number of users.
	 *        
	 */
	public function getCount() {
		$st = $pdo->prepare('SELECT COUNT(user_id) FROM users');
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
		$st = $pdo->prepare('SELECT * FROM users WHERE user_id = ?', array (	$id	) );
		$st->execute();
		
		$userData = array();
		while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
 			$userData = $row;
		}
		
		return $userData ? $this->buildUser ( $userData ) : FALSE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadUserByUsername($username) {
		$st = $pdo->prepare('SELECT u.*  FROM users u WHERE ( u.username = ? OR u.mail = ? );', array (	$username, 	$username) );
		$st->execute();
				
		$usersData = $st->fetchAll ();
		if (empty ( $usersData )) {
			throw new UsernameNotFoundException ( sprintf ( 'User "%s" not found.', $username ) );
		}
		$user = $this->buildUser ( $usersData [0] );
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
	 *        	The array of pdo data.
	 *        	
	 * @return \Ylezzanne\Dao\User
	 *
	 */
	protected function buildUser($userData) {
		$user = new User ();
		$user->setId ( $userData ['user_id'] );
		$user->setUsername ( $userData ['username'] );
		$user->setSalt ( $userData ['salt'] );
		$user->setPassword ( $userData ['password'] );
		$user->setMail ( $userData ['mail'] );
		$user->setRole ( $userData ['role'] );
		$createdAt = new \DateTime ( '@' . $userData ['created_at'] );
		$user->setCreatedAt ( $createdAt );
		return $user;
	}
}
?>

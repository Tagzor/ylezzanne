<?php

namespace Ylezzanne\Dao;

use Herrera\Pdo\Pdo;
use Symfony\Component\Security\Core\User\User;
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
		
		return new User($usersData['username'], $usersData['password'], explode(',', $usersData['role']), true, true, true, true);
		
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
	
		$usersData = array ();
		while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
			$user = new User($usersData['username'], $usersData['password'], explode(',', $usersData->$usersData['role']), true, true, true, true);
			$app ['monolog']->addDebug ( $user );
			array_push ( $usersData, $user );
		}
		
		return $usersData;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function loadUserByUsername($username) {
		$stmt = $this->pdo->prepare("SELECT u.* FROM users u WHERE u.username = :username");
		$stmt->execute(array(':username' => $username));
		
		$usersData = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usersData = $row;
		}
		
		if (empty ( $usersData )) {
			throw new UsernameNotFoundException ( sprintf ( 'User "%s" not found.', $username ) );
		} else {
			sprintf ( 'User with "%s" found.', $username );
			echo $usersData['username'];
			echo $usersData['password'];
			$app ['monolog']->addDebug ( $user );
		}
		
		return new User($usersData['username'], $usersData['password'], explode(',', $usersData->$usersData['role']), true, true, true, true);
		
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
		return 'Symfony\Component\Security\Core\User\User' === $class;
	}
	
}
?>

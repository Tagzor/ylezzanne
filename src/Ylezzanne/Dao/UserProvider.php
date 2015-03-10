<?php

namespace Ylezzanne\Dao;
 
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Herrera\Pdo\Pdo;
 
class UserProvider implements UserProviderInterface
{
    /**
	 *
	 * @var Pdo;
	 *
	 */
	protected $pdo;
	
	
	public function __construct(Pdo $pdo) {
		$this->pdo = $pdo;
	}
 
    public function loadUserByUsername($username)
    {
    	echo "load by username: ". $username;
    	 
    	$st = $this->pdo->prepare('SELECT u.*  FROM users u WHERE ( u.username = ? OR u.mail = ? );', array (	$username, 	$username) );
    	$st->execute();
    	
    	$usersData = $st->fetchAll ();
    	if (empty ( $usersData )) {
    		throw new UsernameNotFoundException ( sprintf ( 'User "%s" not found.', $username ) );
    	}
    	
    	echo "dbusername:" . $usersData [0]->getUsername();
    	
    	//$user = $this->buildUser ( $usersData [0] );
    	$user = new User($usersData [0]->getUsername(), $usersData [0]->getPassword(), explode(',', $user['roles']), true, true, true, true);
    	echo "loaed by username: ". $user;
    	return $user;
    	
        
    }
 
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
 
        return $this->loadUserByUsername($user->getUsername());
    }
 
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
?>
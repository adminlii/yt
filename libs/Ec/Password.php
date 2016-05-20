<?php
 class Ec_Password{
	public static function getPasswordSalt()
	{
	    return substr( str_pad( dechex( mt_rand() ), 8, '0',
	                                           STR_PAD_LEFT ), -8 );
	}
	
	// calculate the hash from a salt and a password
	public static function getPasswordHash( $salt, $password )
	{
	    return $salt . ( hash( 'whirlpool', $salt . $password ) );
	}
	
	// compare a password to a hash or md5
	public static function comparePassword( $password, $hash )
	{
        $salt = substr($hash, 0, 8);
        return $hash == Ec_Password::getPasswordHash($salt, $password) || $hash == md5($password);
    }
 	public static function getHash($password)
 	{
 		return Ec_Password::getPasswordHash(Ec_Password::getPasswordSalt(),$password);
 	}
 }
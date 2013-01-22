<?php
/* + + + + + + + + + + + + + + + + + + + + + 
	* creado			20/12/2011
	* modificado  20/12/2011
	* autor			  I.S.C. Alejandro Diaz Garcia
	* functions.php
+ + + + + + + + + + + + + + + + + + + + + */

	function mosMakePassword($length=8) {
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$makepass = '';
		mt_srand(10000000*(double)microtime());
		for ($i = 0; $i < $length; $i++)
			$makepass .= $salt[mt_rand(0,61)];
			
		return $makepass;
	}	
	
	function josHashPassword($pass)
	{
		$salt = mosMakePassword(24); 
		$crypt = md5($pass.$salt);
		$hash = $crypt.':'.$salt;
		
		return $hash;
	}
	
	function obtenerunPassword($pass,$pass_encryp)
	{
		
		$array = explode(':',$pass_encryp);
		$salt = $array[1];
		$crypt = md5($pass.$salt);
		$hash = $crypt.':'.$salt;
		
		return $hash;
	}

	function MakePassword($length=8) {
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789?_*/+-%()!¡{}[];:.,#";
		$makepass = '';
		mt_srand(10000000*(double)microtime());
		for ($i = 0; $i < $length; $i++)
			$makepass .= $salt[mt_rand(0,61)];
			
		return $makepass;
	}

	function MakeKey($pass)
	{
		$salt = mosMakePassword(24); 
		$crypt = md5($pass.$salt);
		$hash = $crypt;
		
		return $hash;
	}
?>
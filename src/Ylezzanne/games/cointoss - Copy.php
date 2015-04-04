<?PHP 
//kutsutakse cointoss välja kui nuppu vajutatakse ja skooriks vaadatakse mis failis/databaasis on
if ($_POST['valik']) { 
    cointoss($_POST['valik'], (int)file_get_contents(__DIR__.'cointoss.txt'));
} 
//lisab skoorile +1 kui õigesti ja paneb nulli kui valesti
function cointoss($sisse, $skoor){ 
	$result = Rand (1,2);	        
	if ($result ==1 and $sisse=='Kull'){ 
		$skoor = (int)file_get_contents(__DIR__.'cointoss.txt') + 1;
		file_put_contents(__DIR__.'cointoss.txt',(string)$skoor);
		
	}elseif ($result==2 and $sisse=='Kiri'){ 
		$skoor = (int)file_get_contents(__DIR__.'cointoss.txt') + 1;
		return file_put_contents(__DIR__.'cointoss.txt',(string)$skoor);
			
    }else {	
		$skoor = 0;	
		return file_put_contents(__DIR__.'cointoss.txt',(string)$skoor);
	}
}
?>
<title>Coin Toss</title>

<html>  
<head>
</head>
<body>   
<form name = "vise" method = "post" action="<?PHP echo $_SERVER['PHP_SELF']; ?>" >  
	<input type = "submit" name="valik" value="Kull"/> 
	<input type = "submit" name="valik" value="Kiri"/>
</form>  
<?php Echo "Sinu skoor on: ". (int)file_get_contents(__DIR__.'cointoss.txt');?>

</body> 
</html>
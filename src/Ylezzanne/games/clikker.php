<?PHP 
//kutsutakse cointoss välja kui nuppu vajutatakse ja skooriks vaadatakse mis failis/databaasis on
if ($_POST['valik']) { 
    cointoss();
} elseif ($_POST['salvesta']){
	salvesta();
}
//lisab vajutmiste arvule +1
function cointoss(){ 	        
	$skoor= (int)file_get_contents(__DIR__.'vajutamine.txt');
	$tulemus= $skoor +1;
	file_put_contents(__DIR__.'vajutamine.txt', $tulemus);
}
function salvesta(){
	$skoor= (int)file_get_contents(__DIR__.'vajutamine.txt');
	if ($skoor > (int)file_get_contents(__DIR__.'salvestamine.txt')){
		file_put_contents(__DIR__.'salvestamine.txt', $skoor);
		file_put_contents(__DIR__.'vajutamine.txt', 0);
	}else{
		file_put_contents(__DIR__.'vajutamine.txt', 0);
	}
}
?>
<title>Clicker</title>

<html>  
<head>
</head>
<body>   
<form name = "vise" method = "post" action="<?PHP echo $_SERVER['PHP_SELF']; ?>" >  
	<input type = "submit" name="vajuta" value="Vajuta"/> 
	<input type = "submit" name="salvesta" value="Salvesta"/>
</form>  
<?php Echo "Sinu vajutamiste arv on: ". (int)file_get_contents(__DIR__.'vajutamine.txt'). '<br>'.
"Sinu maksimum tulemus on: ". (int)file_get_contents(__DIR__.'salvestamine.txt');
?>

</body> 
</html>
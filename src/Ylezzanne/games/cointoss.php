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
//näidatakse skoori
Echo "Sinu skoor on: ". (int)file_get_contents(__DIR__.'cointoss.txt');?>

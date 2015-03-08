<?php
ini_set('display_errors', 'stderr');
mb_internal_encoding("UTF-8");
error_reporting(E_ALL);// | E_STRICT

require_once("autentimine/openid.ee-authentication.php");


header('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--<link href="bootstrap.css" rel="stylesheet">-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link href="css/valikuleht.css" rel="stylesheet">
	<title>Sisselogimine</title>
</head>

<body>
<!--<div id="login">
<form action="#" method="post" enctype="multipart/form-data">
Kasutajanimi:<br>
<input type="text">
<br>
Parool:<br>
<input type="password" name="lastname">
<br><br>
<input type="submit" value="Logi sisse">
</form>
</div>-->
	<nav class="navbar navbar-default navbar-fixed-top">
	  <div class ="container">
		<div class="navbar-header">
          <a class="navbar-brand" href="#">ylezzanne</a>
		</div>
	  </div>
	</nav>
	
	<div class="container">

      <form class="form-signin">
        <h2 class="form-signin-heading">Sisselogimine</h2>
        <label for="inputUsername" class="sr-only">Kasutajanimi</label>
        <input type="text" id="inputUsername" class="form-control" placeholder="Kasutajanimi" required autofocus>
        <label for="inputPassword" class="sr-only">Parool</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Parool" required>
        <!-- Teeme mingi küpsiste pulli ka?
		<div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>-->
        <button class="btn btn-lg btn-primary btn-block" type="submit">Logi sisse</button>
		<a href="?action=eid-login"><button type="button" class="btn btn-info btn-block"><img src="images/id-kaart.png" alt="idkaart">Sisene ID.kaardiga</button></a>
		<a href="?action=mid-login"><button type="button" class="btn btn-info btn-block"><img src="images/mobiil-id.png" alt="mobiilid">Sisene Mobiil-IDga</button></a>
      </form>
<li>Sessiooni <a href="?action=logout">lõpetamine</a></li>

    </div>
<!--<div id="login">
  <h1>Sisselogimine</h1>
  <form action="/valikuleht.html">
    <input type="text" placeholder="Kasutajanimi" />
    <input type="password" placeholder="Parool" />
    <input type="submit" value="Logi sisse"/>
  </form>

</div>
<div>
	<p align="center"><a id="id_button" title="Sisene ID-kaardiga" href="siia tuleb mingi variant kuidas id-kaardiga siseneda">Sisene ID kaardiga</a	></p>
</div>-->
</body>
</html>

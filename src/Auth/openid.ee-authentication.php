<?php

// OpenID teek...
require_once __DIR__.'/OpenID/Consumer.php';
require_once __DIR__.'/OpenID/FileStore.php';
require_once __DIR__.'/OpenID/SReg.php';
require_once __DIR__.'/OpenID/PAPE.php';


// Kasutame PHP sessiooni
session_start();
function getSiteURL() {
	// Muuda ära vastavalt enda veebisaidile, kui on vajadus.
	// Võib tagastada ka näiteks stringi "http://mart.randala.pri.ee/openid-php/"
	return "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]) . "eid";
} 
function getReturnTo() { return getSiteURL() . "?action=finishAuth"; }
function getTrustRoot() { return getSiteURL(); }
function displayError($message) { $error = $message; include 'index.php'; die(); }
function escape($thing) { return htmlentities($thing); }

function getConsumer() { $store = getStore(); $consumer = new Auth_OpenID_Consumer($store); return $consumer; }

function &getStore() {
    $store_path = "/tmp/_php_consumer_test";
    if (!file_exists($store_path) && !mkdir($store_path)) {
        print "Could not create the FileStore directory '$store_path'. Please check the effective permissions.";
        exit(0);
    }
    $tmp = new Auth_OpenID_FileStore($store_path);
    return $tmp;
}

// Sessiooni alustamine
function OpenIDeeAuth($method = "e") { // "e" = ID-kaart, "m" = Mobiil-ID
	$auth_request = getConsumer()->begin("https://openid.ee/server/xrds/".$method."id"); // :10443 for debugging
	//required väljad, valikulised väljad
	$sreg_request = Auth_OpenID_SRegRequest::build( array('fullname','email','dob'), array('gender'));
	$auth_request->addExtension($sreg_request);
	$pape_request = new Auth_OpenID_PAPE_Request(array('http://schemas.openid.net/pape/policies/2007/06/multi-factor-physical'));
	$auth_request->addExtension($pape_request); 
	$form_id = 'openid_message';
	$form_html = $auth_request->htmlMarkup(getTrustRoot(), getReturnTo(), false, array('id' => $form_id));
	print $form_html; die();
}

// Väljalogides kustutame sessiooni...
if(isset($_GET["action"])){
if($_GET["action"] == "logout") { session_destroy(); header("Location: " . getTrustRoot() );}
// Sisselogimise lõpul korjame vajalikud andmed välja ja paneme PHP sessioonimuutujatesse
if($_GET["action"] == "finishAuth") {
	$consumer = getConsumer();
	$return_to = getReturnTo();
	$response = $consumer->complete($return_to);
	
	if ($response->status == Auth_OpenID_CANCEL) $msg = 'Verification cancelled.';
	else if ($response->status == Auth_OpenID_FAILURE) $msg = "OpenID authentication failed: " . $response->message;
	else if ($response->status == Auth_OpenID_SUCCESS) {
		$openid = $response->getDisplayIdentifier();
		$esc_identity = escape($openid);

		$success = sprintf('You have successfully verified <a href="%s">%s</a> as your identity.', $esc_identity, $esc_identity);
		$_SESSION['openid'] = $esc_identity;

		if ($response->endpoint->canonicalID) {
			$escaped_canonicalID = escape($response->endpoint->canonicalID);
			$success .= '  (XRI CanonicalID: '.$escaped_canonicalID.') ';
		}

		$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
		$sreg = $sreg_resp->contents();
		if (@$sreg['email']) $_SESSION['email'] = escape($sreg['email']);
		if (@$sreg['fullname']) $_SESSION['fullname'] = escape($sreg['fullname']);
		if (@$sreg['gender']) $_SESSION['gender'] = escape($sreg['gender']);
		if (@$sreg['dob']) $_SESSION['dob'] = escape($sreg['dob']);

		$pape_resp = Auth_OpenID_PAPE_Response::fromSuccessResponse($response);

		if ($pape_resp) {
			if ($pape_resp->nist_auth_level) {
				$auth_level = escape($pape_resp->nist_auth_level);
				$success .= "<p>The NIST auth level returned by the server is: <tt>".$auth_level."</tt></p>";
			}
		} else $success .= "<p>No PAPE response was sent by the provider.</p>";
	}
}
// Siin kutsume välja autentimis-transaktsiooni
if($_GET["action"] == "mid-login") OpenIDeeAuth("m"); //Mobiil-ID autentimine
else if($_GET["action"] == "eid-login") OpenIDeeAuth("e"); //ID-kaardi autentimine


}
?>
<?php
// This file allows psim.us to use the cookies and localStorage
// from play.pokemonshowdown.com.

// Never cache.
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

require '../pokemonshowdown.com/lib/ntbb-session.lib.php';

$challengeresponse = intval(@$_REQUEST['challengeresponse']);
$upkeep = $users->getUpkeep($challengeresponse);
$prefix = strval(@$_REQUEST['prefix']);
// Need to check the form of $prefix to avoid some vulnerabilities.
// This check should be robust enough for now.
if (!preg_match('/^[a-zA-Z0-9-_\.]*$/', $prefix)) {
	die('Invalid prefix');
}
$origin = 'http://' . $prefix . '.psim.us';
$username = isset($_COOKIE['showdown_username']) ? $_COOKIE['showdown_username'] : '';
$sid = isset($_COOKIE['sid']) ? $_COOKIE['sid'] : '';
?>
<!DOCTYPE html>
<script src="/js/jquery-1.9.0.min.js"></script>
<script src="/js/jquery-cookie.js"></script>
<script>
(function() {
	var origin = <?php echo json_encode($origin) ?>;
	$(window).on('message', function($e) {
		var e = $e.originalEvent;
		if (e.origin !== origin) return;
		if (e.data.username) {
			$.cookie('showdown_username', e.data.username, {expires: 14});
		}
		if (e.data.sid) {
			$.cookie('sid', e.data.username, {expires: 14});
		}
		if (e.data.teams) {
			localStorage.setItem('showdown_teams', e.data.teams);
		}
		if (e.data.prefs) {
			localStorage.setItem('showdown_prefs', e.data.prefs);
		}
	});
	var message = {
		upkeep: <?php echo json_encode($upkeep) ?>,
		username: <?php echo json_encode($username) ?>,
		sid: <?php echo json_encode($sid) ?>
	};
	if (window.localStorage) {
		message.teams = localStorage.getItem('showdown_teams');
		message.prefs = localStorage.getItem('showdown_prefs');
	}
	window.parent.postMessage(message, origin);
})();
</script>

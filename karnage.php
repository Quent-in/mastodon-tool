<!doctype html>
<html>
<head>
<style>*{font-family:Open Sans, sans-serif;}#wrapper{width:600px;margin:auto;padding: 1em 0 1em;}p{text-align:justify;font-size: 12pt;}#logo{display:block;margin:auto;padding: 1em 0 1.5em;}footer p{text-align:center;}hr{margin:2em auto 2em;width:25%;}form{width:100%;}label{margin:1em 0 .5em;display:block;}label.inline{display:inline;margin-right:50px;}input, textarea, select{padding:3px;border:1px solid #888;width:100%;}input[type=submit]{width:auto;box-shadow:1px 1px 1px #888;cursor:pointer;margin-top:1em;}</style>
</head>
<body>
<?php
if(!file_exists('Mastodon_api.php')) {file_put_contents('Mastodon_api.php', file_get_contents('https://raw.githubusercontent.com/yks118/Mastodon-api-php/master/Mastodon_api.php'));} else {include 'Mastodon_api.php';}
// Fonction de comparaison
function cmp($a, $b) {
    if ($a['name'] == $b['name']) {
        return 0;
    }
    return ($a['name'] < $b['name']) ? -1 : 1;
}
?>
<h1>Vider votre compte</h1>
<p>Pour l’instant, il semble impossible de supprimer un compte. Mais on peut le vider de toute substance !</p>
<form action="karnage.php" method="post">
	<label for="email">Le courriel de votre compte : <input name="email" id="email" type="email"></label>
	<label for="password">Le mot de passe de votre compte : <input name="password" id="password" type="password"></label>
	<label for="instance">Votre instance : 
	<select id="instance" name="instance">
	<?php
	$instances = json_decode(file_get_contents('https://instances.mastodon.xyz/instances.json'),true);
	uasort($instances, 'cmp');
	foreach($instances as $i) {
		echo '<option name="'.$i['name'].'">'.$i['name'].'</option>';
	}
	?>
	</select>
	</label>
	<label for="unfollow"><input type="checkbox" name="unfollow" id="unfollow" value="true">Je veux supprimer mes abonnements !</label>
	<label for="status"><input type="checkbox" name="status" id="status" value="true">Je veux supprimer mes toots !</label>
	<label for="notification"><input type="checkbox" name="notification" id="notification" value="true">Je veux supprimer mes notifications !</label>
	<label for="block"><input type="checkbox" name="block" id="block" value="true">Je veux débloquer les personnes bloquées !</label>
	<label for="unmute"><input type="checkbox" name="unmute" id="unmute" value="true">Je veux redonner la parole aux muets !</label>
	<label for="account"><input type="checkbox" name="account" id="account" value="true">Je veux perdre la personalisation de mon compte !</label>
	<input type="submit" value="Envoyer" name="send" id="send"/>
</form>

<?php
if(isset($_POST['send'])) {
	$mastodon = new Mastodon_api();
	$mastodon->set_url('https://'.$_POST['instance'].'/');
	$create_app = $mastodon->create_app('Karnage');
	$mastodon->set_client($create_app['html']['client_id'],$create_app['html']['client_secret']);
	$login = $mastodon->login($_POST['email'], $_POST['password']);
	$mastodon->set_token($login['html']['access_token'],$login['html']['token_type']);
	$id = $mastodon->accounts_verify_credentials()['html']['id'];
	if($_POST['unfollow'] == true) {
		// Unfollow everybody
		foreach($mastodon->accounts_following($id)['html'] as $follower) {
			$mastodon->accounts_unfollow($follower['id']);
		}
		echo '<p>✓ Vos abonnements ont été supprimés.</p>';
	}
	if($_POST['status'] == true) {
		// delete all statues
		foreach($mastodon->accounts_statuses($id)['html'] as $status) {
			$mastodon->delete_statuses($status['id']);
		}
		echo '<p>✓ Vos toots ont été supprimés.</p>';
	}
	if($_POST['notification'] == true) {
		// delete notification
		$mastodon->notifications_clear();
		echo '<p>✓ Vos notifications ont été supprimées.</p>';
	}
	if($_POST['block'] == true) {
		// unblock everybody
		foreach($mastodon->blocks($id)['html'] as $blocks) {
				$mastodon->accounts_unblock($blocks['id']);
		}
		echo '<p>✓ Les personnes bloquées ont été débloquées.</p>';
	}
	if($_POST['unmute'] == true) {
		// unmute everybody
		foreach($mastodon->mutes()['html'] as $blocks) {
			$mastodon->accounts_unmute($mute['id']);
		}
		echo '<p>✓ Les muets ont retrouvé la parole !</p>';
	}
	if($_POST['account'] == true) {
		$mastodon->accounts_update_credentials(array('display_name'=>'', 'note'=>'', 'avatar'=>'', 'header'=>''));
		echo '<p>✓ Compte dépersonnalisé !</p>';
	}
}
?>
</body>
</html>

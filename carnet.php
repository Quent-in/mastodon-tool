<?php
// d'après https://mastodon.social/users/clochix/updates/2439847
if(!file_exists('Mastodon_api.php')) {file_put_contents('Mastodon_api.php', file_get_contents('https://raw.githubusercontent.com/yks118/Mastodon-api-php/master/Mastodon_api.php'));} else {include 'Mastodon_api.php';}
define('INSTANCE', 'mastodon.social');
define('EMAIL', 'email@exemple.org');
define('PASSWORD', 'p@ssw0rd');

$mastodon_api = new Mastodon_api();
$mastodon_api->set_url('https://'.INSTANCE.'/');
$create_app = $mastodon_api->create_app('Mon Application');
$mastodon_api->set_client($create_app['html']['client_id'],$create_app['html']['client_secret']);
$login = $mastodon_api->login(EMAIL, PASSWORD);
$mastodon_api->set_token($login['html']['access_token'],$login['html']['token_type']);
$id = $mastodon_api->accounts_verify_credentials()['html']['id'];

$fav = $mastodon_api->favourites()['html'];
// print_r($fav);
foreach($fav as $k => $f) {
	if($f['account']['id'] == $id) {
		echo $k;
		echo date('Y-m-d H:i', strtotime($f['created_at']));
		echo $f['content'];
		echo $f['url'];
		echo $f['account']['display_name'];
		echo $f['uri'];
		if(isset($f['media_attachments'])) {
			foreach($f['media_attachments'] as $m) {
				echo $m['text_url'];
			}
		}

	}
}
foreach($fav as $k => $f) {
	if($f['account']['id'] == $id) {
		if(!file_exists(md5($f['uri']).'.md')) {
			$text = '
---
author: '.$f['account']['display_name'].'
date: '.date('Y-m-d H:i', strtotime($f['created_at'])).'
title: Pouet #'.$k.'
---
'.$f['content'].'
[Message original]('.$f['url'].')
';
			file_put_contents(md5($f['uri']).'.md', $text);
		}
	}
}
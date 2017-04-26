<?php
// convert Atom feed to Mastodon account
if(!file_exists('Mastodon_api.php')) {file_put_contents('Mastodon_api.php', file_get_contents('https://raw.githubusercontent.com/yks118/Mastodon-api-php/master/Mastodon_api.php'));} else {include 'Mastodon_api.php';}
if(!file_exists('config.php')) {file_put_contents('config.php', "<?php
define('INSTANCE', 'mastodon.social');
define('EMAIL', 'john.doe@example.org');
define('PASSWORD', 'mysuperpassword');
define('ATOM_FEED', 'http://example.org/feed.xml');
$ban_tags = array();
");} else {include 'config.php';}
if(!file_exists('lastsend.txt')) {file_put_contents('lastsend.txt', 0);}

$mastodon_api = new Mastodon_api();
$mastodon_api->set_url('https://'.INSTANCE.'/');
$create_app = $mastodon_api->create_app('Mon Application');
$mastodon_api->set_client($create_app['html']['client_id'],$create_app['html']['client_secret']);
$login = $mastodon_api->login(EMAIL, PASSWORD);
$mastodon_api->set_token($login['html']['access_token'],$login['html']['token_type']);

print_r($mastodon_api->timelines_home());

$array = json_decode(json_encode(simplexml_load_string(implode(file(ATOM_FEED)))), true);
foreach ($feed->entry as $item) {
	if(strtotime($item->updated) >= file_get_contents('lastsend.txt')) {
		$mastodon_api->post_statuses(array('status'=>'[BOT] « '.$item->title.' » '.$item->link['href']));
	}
}
foreach($array['entry'] as $item) {
	$hashtag = '';
	foreach($item['category'] as $tags) {
		if(isset($tags['@attributes'])) {
			if(!in_array($tags['@attributes']['label'], $ban_tags)) {
				$hashtag .= '#'.$tags['@attributes']['label'].' ';
			}
		}
		else {
			if(!in_array($tags['label'], $ban_tags)) {
				$hashtag .= '#'.$tags['label'].' ';
			}
		}
	}
	if(strtotime($item['updated']) >= file_get_contents('lastsend.txt')) { 
		$mastodon_api->post_statuses(array('status'=>'« '.$item['title'].' » '.$item['link']['@attributes']['href'].' '.$hashtag));
		echo '« '.$item['title'].' » '.$item['link']['@attributes']['href'].' '.$hashtag.'<br>';
	}
}

file_put_contents('lastsend.txt', time());

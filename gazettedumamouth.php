<?php
define('LOGIN', '');
define('PWD', '');
if(!file_exists('Mastodon_api.php')) {file_put_contents('Mastodon_api.php', file_get_contents('https://raw.githubusercontent.com/yks118/Mastodon-api-php/master/Mastodon_api.php'));} else {include 'Mastodon_api.php';}
$mastodon = new Mastodon_api();
$mastodon->set_url('https://framapiaf.org/');
$create_app = $mastodon->create_app('Gazette');
$mastodon->set_client($create_app['html']['client_id'],$create_app['html']['client_secret']);
$login = $mastodon->login(LOGIN, PWD);
$mastodon->set_token($login['html']['access_token'],$login['html']['token_type']);
echo '<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">

 <title>Timeline Mastodon</title>
 <subtitle>Power of atom</subtitle>
 <link href="http://mastodon.social/"/>
 <updated>'.date(DATE_ATOM, time()).'</updated>
 <id>urn:uuid:60a76c80-d399-11d9-b91C-0003939e0af6</id>';
foreach($mastodon->timelines_home()['html'] as $msg) {
	if(empty($msg['reblog']) OR empty(['in_reply_to_id'])) {
		echo '<entry>
   <title>'.substr(strip_tags($msg['content']), 0, 50).'</title>
   <link href="'.$msg['url'].'"/>
    <author>
    <name>'.$msg['account']['display_name'].'</name>
    <uri>'.$msg['account']['url'].'</uri>
  </author>
   <id>urn:'.$msg['uri'].'</id>
   <updated>'.date(DATE_ATOM, strtotime($msg['created_at'])).'</updated>
   <content type="html" xml:lang="fr"><![CDATA['.$msg['content'].']]></content>
 </entry>';
	}
}
echo '</feed>';
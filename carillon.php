<?php
date_default_timezone_set ('Europe/Paris');
if(!file_exists('Mastodon_api.php')) {file_put_contents('Mastodon_api.php', file_get_contents('https://raw.githubusercontent.com/yks118/Mastodon-api-php/master/Mastodon_api.php'));} else {include 'Mastodon_api.php';}
$mastodon = new Mastodon_api();
$mastodon->set_url('https://framapiaf.org/');
$create_app = $mastodon->create_app('Carillon de Chambéry');
$mastodon->set_client($create_app['html']['client_id'],$create_app['html']['client_secret']);
$login = $mastodon->login('', '');
$mastodon->set_token($login['html']['access_token'],$login['html']['token_type']);

$ding = date('H', time());
if($ding > 12) {
	$ding = $ding - 12;
}
elseif($ding == 12) {
	$ding = 12;
}
else {
	$ding = $ding;
}
for ($i = 1; $i <= $ding; $i++) {
    $post .= 'DING (dong dung) ';
}
echo $post;
$mastodon->post_statuses(array('status'=>$post));
?>
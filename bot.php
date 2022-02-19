<?

$api_token = getenv('API_TOKEN');
$stat_to_train = getenv('STAT_TO_TRAIN');
$attn_to_leave_available = getenv('ATTN_BANK');

function api_call($api_token, $endpoint, $post_data) {
    $url = 'https://apicyber.space'.$endpoint;
    $post_data['api_auth_token'] = $api_token;

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($post_data),
            'ignore_errors' => true
        ),
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    error_log("Api call ".$endpoint." with POST=".json_encode($post_data)." **RESULT**: ".$result);
    return json_decode($result, true);
}

$player_attn_vars = api_call($api_token, '/index.php?act=get_user_attention', array());

$attn_to_spend = $player_attn_vars['player_attention'] - $attn_to_leave_available;

if($attn_to_spend > 0) {
    api_call($api_token, '/train.php?act=submit', array(
        'which_stat' => $stat_to_train, 
        'train_number' => $attn_to_spend
    ));
}


?>
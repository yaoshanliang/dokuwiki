<?php
//基础config
define('CLIENT_ID', 'UC57217ce85409e');//应用ID，需要申请
define('CLIENT_SECRET', 'f1b8c3f7f44580e3b5b3dcf7aadb76d1');//应用密钥，需要申请
define('REDIRECT_URI', 'http://dokuwiki.szjlxh.com/doku.php?do=login');//回调地址
define('UCENTER_HOME', 'http://ucenter.szjlxh.com');//用户中心地址

define('UCENTER_OAUTH', UCENTER_HOME . '/oauth');
define('UCENTER_API', UCENTER_HOME . '/api');

function curl($url, $method = 'GET', $data = array()) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}
function ucenter_oauth() {
    //根据授权码获取access_token
    $url = UCENTER_API . '/oauth/accessToken';
    $data = array('client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'grant_type' => 'authorization_code',
        'redirect_uri' => REDIRECT_URI,
        'code' => $_GET['code']);
    $response = curl($url, 'POST', $data);
    $data = json_decode($response, true);
    if(1 !== $data['code']) {
        exit('授权失败');
    }
    $access_token = $data['data']['access_token'];

    //根据access_token获取用户信息
    $url = UCENTER_API . '/user/?access_token=' . $access_token;
    $data = curl($url);
    $data = json_decode($data, true);
    if(1 !== $data['code']) {
        exit('获取用户信息失败');
    }

    return $data['data']['username'];
}

//生成授权url
function ucenter_oauth_url() {
    return UCENTER_OAUTH . '/authorize?client_id=' . CLIENT_ID . '&response_type=code&redirect_uri=' . urlencode(REDIRECT_URI);
}

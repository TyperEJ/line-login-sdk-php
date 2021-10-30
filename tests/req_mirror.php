<?php

$body = file_get_contents('php://input');

/** responseDelay url paramter define delay of response.(millisecond) */
if (isset($_GET["responseDelay"]) && is_numeric($_GET["responseDelay"])) {
    usleep($_GET["responseDelay"] * 1000);
}

header('Content-Type: application/json');
echo json_encode(array(
    'Body'    => isset($_SERVER['HTTP_CONTENT_TYPE']) && strpos($_SERVER['HTTP_CONTENT_TYPE'], 'image/') === 0 ?
        base64_encode($body) : $body,
    '_SERVER' => $_SERVER,
));

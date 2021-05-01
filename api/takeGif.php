<?php
header('Content-Type: application/json');

require_once('../lib/config.php');

function takeWebm($filename)
{
    global $config;

    if ($config['previewCamTakesPic']) {
        $data = $_POST['webmGif'];
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        file_put_contents($filename, $data);
    }
}

if ($config['file_format_date']) {
    $file = date('Ymd_His').'.webm';
} else {
    $file = md5(time()).'.webm';
}

$filename_tmp = $config['foldersAbs']['images'] . DIRECTORY_SEPARATOR . $file;

if (!isset($_POST['style'])) {
    die(json_encode([
        'error' => 'No style provided'
    ]));
}

if ($_POST['style'] === 'webm') {
    takeWebm($filename_tmp);
} else {
    die(json_encode([
        'error' => 'Invalid photo style provided',
    ]));
}

// send imagename to frontend
echo json_encode([
    'success' => 'image',
    'file' => $file,
]);

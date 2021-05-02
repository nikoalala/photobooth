<?php
header('Content-Type: application/json');

require_once('../lib/config.php');
require_once('../lib/db.php');

function takeWebm($file)
{
    global $config;

    $filename_tmp = $config['foldersAbs']['tmp'] . DIRECTORY_SEPARATOR . $file . '.webm';
    $filename_tmpgif = $config['foldersAbs']['images'] . DIRECTORY_SEPARATOR . $file . '.gif';
    $filename_tmpgif_thumb = $config['foldersAbs']['thumbs'] . DIRECTORY_SEPARATOR . $file . '.gif';

    if ($config['previewCamTakesPic']) {
        $data = $_POST['webmGif'];
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        file_put_contents($filename_tmp, $data);

        // Convert video to gif
        $printimage = shell_exec(
            sprintf(
                $config['convertToGIF']['cmd'],
                $filename_tmp,
                $filename_tmpgif 
            )
        );

        // Convert video to gif thumb
        $printimage = shell_exec(
            sprintf(
                $config['convertToGIFthumb']['cmd'],
                $filename_tmp,
                $filename_tmpgif_thumb 
            )
        );


        $removeTMP = shell_exec('rm ' .  $filename_tmp);
    }
}

if ($config['file_format_date']) {
    $file = date('Ymd_His');
} else {
    $file = md5(time());
}

if (!isset($_POST['style'])) {
    die(json_encode([
        'error' => 'No style provided'
    ]));
}

if ($_POST['style'] === 'webm') {
    takeWebm($file);
    appendImageToDB($file.'.gif');
} else {
    die(json_encode([
        'error' => 'Invalid photo style provided',
    ]));
}

// send imagename to frontend
echo json_encode([
    'success' => 'image',
    'file' => $file.'.gif',
]);

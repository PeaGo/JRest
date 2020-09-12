<?php
function parseBase64($file_post_data)
{
    $file = str_replace('data:image/png;base64,', '', $file_post_data);
    $file = str_replace(' ', '+', $file);
    mb_convert_encoding($file, 'UTF-8', 'UTF-8');
    // $data = base64_decode($file);
    return $file;
}
function saveFile($file_post_data, $path, $extension = 'txt', $name = null)
{
    // empty($path) && $path = 'upload/';
    $name = !empty($name) ? $name : strtotime(date('Y-m-d H:i:s'));
    $file = base64_decode($file_post_data);

    $namefile = $path . '/' . $name . '.' . $extension;
    $success = file_put_contents($namefile, $file);
    // chmod($success, 0664);
    if ($success) {
        return $namefile;
    }
    return false;
}


function saveAvatar($data, $id)
{
    $img_file_extension = '';
    switch (substr($data, 0, 1)) {
        case '/': {
                $img_file_extension = 'jpg';
                break;
            }
        case 'i': {
                $img_file_extension = 'png';
                break;
            }
        case 'R': {
                $img_file_extension = 'gif';
                break;
            }
        default: {
                $img_file_extension = 'jpg';
                break;
            }
    }
    $filename = 'img_' . strtotime(date('Y-m-d H:i:s'));
    $img_file_name = MEDIA_PATH . 'upload/user/'. $id .'/'. $filename . '.' . $img_file_extension;
    $img_file = file_put_contents($img_file_name, base64_decode($data));

    if ($img_file) {
        // $img_url = BASE_URL . $img_file_name;
        return  $filename . '.' . $img_file_extension;
    } else {
        return false;
    }
}


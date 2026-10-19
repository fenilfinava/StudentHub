<?php
// STEP 1: the heart of the practical.
// Checks a poster file and saves it. Returns [file_name, error_message].
function upload_poster($file) {
    // No file chosen -> fine, the poster is optional
    if (empty($file) || $file['error'] == UPLOAD_ERR_NO_FILE) return [null, null];

    // PHP itself rejected the file (too big for php.ini)
    if ($file['error'] == UPLOAD_ERR_INI_SIZE) return [null, "File must be 2 MB or less."];
    if ($file['error'] != UPLOAD_ERR_OK)       return [null, "Upload failed."];

    // 1. Check type (extension)
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        return [null, "Only JPG, PNG and WEBP files are allowed."];
    }

    // 2. Check size (2 MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        return [null, "File must be 2 MB or less."];
    }

    // 3. Check it is really an image
    if (!@getimagesize($file['tmp_name'])) {
        return [null, "This file is not a real image."];
    }

    // 4. Give it a unique name and move it to the uploads folder
    $new_name = time() . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], 'uploads/' . $new_name)) {
        return [null, "Could not save the file."];
    }
    return [$new_name, null];
}

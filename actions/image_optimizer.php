<?php
// ANCHOR: Simple image optimization utility for uploaded images
// This can be called after successful uploads to optimize image file sizes

function optimizeImage($sourcePath, $maxWidth = 1200, $quality = 85) {
    // ANCHOR: Check if GD extension is available
    if (!extension_loaded('gd')) {
        return false;
    }
    
    // ANCHOR: Get image info
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];
    
    // ANCHOR: Skip if image is already small enough
    if ($originalWidth <= $maxWidth) {
        return true;
    }
    
    // ANCHOR: Calculate new dimensions
    $newHeight = ($originalHeight * $maxWidth) / $originalWidth;
    
    // ANCHOR: Create image resource based on type
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // ANCHOR: Create new image with calculated dimensions
    $newImage = imagecreatetruecolor($maxWidth, $newHeight);
    
    // ANCHOR: Preserve transparency for PNG and GIF
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $maxWidth, $newHeight, $transparent);
    }
    
    // ANCHOR: Resize image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $maxWidth, $newHeight, $originalWidth, $originalHeight);
    
    // ANCHOR: Save optimized image
    $result = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $result = imagejpeg($newImage, $sourcePath, $quality);
            break;
        case 'image/png':
            $result = imagepng($newImage, $sourcePath, 9);
            break;
        case 'image/gif':
            $result = imagegif($newImage, $sourcePath);
            break;
        case 'image/webp':
            $result = imagewebp($newImage, $sourcePath, $quality);
            break;
    }
    
    // ANCHOR: Clean up memory
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    return $result;
}

// ANCHOR: Function to create thumbnail
function createThumbnail($sourcePath, $thumbnailPath, $thumbWidth = 200, $thumbHeight = 200) {
    if (!extension_loaded('gd')) {
        return false;
    }
    
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];
    
    // ANCHOR: Create source image
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // ANCHOR: Calculate thumbnail dimensions (maintain aspect ratio)
    $ratio = min($thumbWidth / $originalWidth, $thumbHeight / $originalHeight);
    $newWidth = $originalWidth * $ratio;
    $newHeight = $originalHeight * $ratio;
    
    // ANCHOR: Create thumbnail
    $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
    
    // ANCHOR: Preserve transparency
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefill($thumbnail, 0, 0, $transparent);
    } else {
        $white = imagecolorallocate($thumbnail, 255, 255, 255);
        imagefill($thumbnail, 0, 0, $white);
    }
    
    // ANCHOR: Center the thumbnail
    $x = ($thumbWidth - $newWidth) / 2;
    $y = ($thumbHeight - $newHeight) / 2;
    
    imagecopyresampled($thumbnail, $sourceImage, $x, $y, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // ANCHOR: Save thumbnail
    $result = imagejpeg($thumbnail, $thumbnailPath, 90);
    
    // ANCHOR: Clean up
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);
    
    return $result;
}

// ANCHOR: Function to get file size in human readable format
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}
?>

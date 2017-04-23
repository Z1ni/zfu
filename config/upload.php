<?php

/**
 * Up application specific settings
 */

return [

    // File code length (e.g. 5 -> "123ab", 7 -> "123abcd")
    'file_code_length' => 5,

    // Default visibility status after file upload
    // TODO: Can be overridden in the user settings
    // True: visible, false: invisible
    'file_default_visible' => true,

    // Get image/video/audio metadata such as width/height/length/etc. to display
    // Makes image uploading a bit slower
    'gather_metadata' => true,

    // Files shown per single page
    'files_per_page' => 44,

    // Misc resource images for different situations
    // Storage path: /storage/app/system/
    'misc_resource' => [                        // Return this image when...
        'resource_filetype' => 'image/png',     // Mimetype of following images
        'file_not_found' => 'not_found.png',    // ..the requested file is not found
        'audio_file'     => 'audio.png',        // ..the file is an audio file
        'no_thumbnail'   => 'unknown.png',      // ..the file has no other possible thumbnail image
        'unknown_error'  => 'fail.png',         // ..the file is corrupted or something unknown happened
    ],

    // File optimization settings
    'optimize' => [
        // Optimize PNG files after upload
        'png' => [
            'enabled' => true,
            'optipng_path' => '/usr/bin/optipng',
            'optipng_flags' => '-quiet -o2'
        ],
        // Optimize JPEG files after upload
        'jpeg' => [
            'enabled' => true,
            'jpegoptim_path' => '/usr/bin/jpegoptim',
            'jpegoptim_flags' => '-q'
        ]
    ],

    'extra' => [
        'ffmpeg_path' => '/usr/bin/ffmpeg',
        'ffprobe_path' => '/usr/bin/ffprobe',
    ],

];
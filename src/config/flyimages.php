<?php

return [

    /*
     * Path to the folders where images are stored. Multiple paths can be specified.
     * The script will loop over this list of folder paths and first match will be taken.
     */
    'folder' => [
        '../public/media',
        '../public'
    ],

    /*
     * Cache ttl
     */
    'ttl' => '1440',

    /*
     * Route
     */
    'route' => 'optimize'

];

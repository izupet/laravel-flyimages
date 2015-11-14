<?php

Route::get(sprintf('/%s/{hash}', Config::get('flyimages.route')), function($hash) {
    $flyImage = new \Izupet\FlyImages\FlyImages();

    return $flyImage->optimize($hash);
});

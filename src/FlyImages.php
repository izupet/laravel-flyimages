<?php

namespace Izupet\FlyImages;

use Config;
use Exception;

class FlyImages
{
    public function __construct()
    {
        $this->size     = $this->getSize(isset($_COOKIE['resolution']) ? $_COOKIE['resolution'] : 1200);
        $this->image    = new \Imagick();
    }

    /*
    * Return optimized (cropped or resized) image. If image exists in cache is pulled
    * out, otherwise is generated dynamically and put into cache for next usage.
    *
    * @param string $hash
    *
    * @access public
    * @return object Imagick with proper Content-type header
    */
    public function optimize($hash)
    {
        $folder = $this->getImagePath($hash);

        //Check if image exists. If not, throw exception.
        if (is_null($folder)) {
            throw new Exception('Image does not exists.');
        }

        //Check if any etag is set.
        if(!empty(request()->instance()->getETags())) {

            return response(null)->setNotModified();
        }

        $newHeight = $this->getDimensionValue('h');
        $newWidth  = $this->getDimensionValue('w');

        $this->image->readImage(sprintf('%s/%s', $folder, $hash));

        if (filter_var($newWidth, FILTER_VALIDATE_INT) && filter_var($newHeight, FILTER_VALIDATE_INT)) {
            $this->crop($newWidth, $newHeight);
        } else if (filter_var($newWidth, FILTER_VALIDATE_INT) && $newHeight === 'auto') {
            $this->resize($newWidth, 0);
        } else if (filter_var($newHeight, FILTER_VALIDATE_INT) && $newWidth === 'auto') {
            $this->resize(0, $newHeight);
        }

        return response($this->image)
            ->header('Pragma', 'Public')
            ->header('Content-Type', $this->image->getImageMimeType())
            ->setEtag(md5(sprintf('%s-%s', $hash, $_SERVER['QUERY_STRING'])))
            ->setPublic();
    }

    /*
    * Return resized image according to dimensions. Image ratio is kept.
    *
    * @param int $width
    * @param int $height
    *
    * @access private
    * @return object Imagick
    */
    private function resize($width, $height)
    {
        $this->image->thumbnailImage($width, $height, false);
    }

    /*
    * Return cropped image according to dimensions. Cropped from center.
    *
    * @param int $width
    * @param int $height
    *
    * @access private
    * @return object Imagick
    */
    private function crop($width, $height)
    {
        $this->image->cropThumbnailImage($width, $height);
    }

    /*
    * Get size of the screen according to bootstrap grid
    *
    * @param string $resolution
    *
    * @access private
    * @return string
    */
    private function getSize($resolution)
    {
        switch ($resolution) {
            case ($resolution >= 1200):
                return 'lg';
                break;
            case ($resolution >= 992):
                return 'md';
                break;
            case ($resolution >= 768):
                return 'sm';
                break;
            case ($resolution < 768):
                return 'xs';
                break;
        }
    }

    /*
    * Get size of the screen according to bootstrap grid
    *
    * @param string $queryString
    * @param string $dimension w as width or h as height
    *
    * @access private
    * @return int | null
    */
    private function getDimensionValue($dimension)
    {
        parse_str($_SERVER['QUERY_STRING'], $queryParams);

        if ($this->size === 'lg') {
            if (array_key_exists(sprintf('lg-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('lg-%s', $dimension)];
            }
        } else if ($this->size === 'md') {
            if (array_key_exists(sprintf('md-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('md-%s', $dimension)];
            } else if (array_key_exists(sprintf('lg-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('lg-%s', $dimension)];
            }
        } else if ($this->size === 'sm') {
            if (array_key_exists(sprintf('sm-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('sm-%s', $dimension)];
            } else if (array_key_exists(sprintf('md-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('md-%s', $dimension)];
            } else if (array_key_exists(sprintf('lg-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('lg-%s', $dimension)];
            }
        } else if ($this->size === 'xs') {
            if (array_key_exists(sprintf('xs-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('xs-%s', $dimension)];
            } else if (array_key_exists(sprintf('sm-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('sm-%s', $dimension)];
            } else if (array_key_exists(sprintf('md-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('md-%s', $dimension)];
            } else if (array_key_exists(sprintf('lg-%s', $dimension), $queryParams)) {
                return $queryParams[sprintf('lg-%s', $dimension)];
            }
        }
    }

    /*
    * Find image on the filesystem
    *
    * @param string $hash
    *
    * @access private
    * @return string | null
    */
    private function getImagePath($hash)
    {
        foreach(Config::get('flyimages.folder') as $folder) {
            if (file_exists(sprintf('%s/%s', $folder, $hash))) {

                return $folder;
            }
        }
    }
}

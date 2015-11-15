# laravel-flyimages
Resize/crop image on the fly according to screen resolution (bootstrap grid pattern).

## Prerequisite

Imagick (ImageMagick) PHP extension <br>
PHP version >= 5.5 <br>
Laravel framework 4 and up

## Installation
First you need to add this line to composer.json file:
```json
{
  "require": {
       "izupet/laravel-flyimages" : "^1.0.0"
    }
}
```

Update composer from the terminal:
```bash
$ composer update
```

Add service provider in app.php file:

```php
Izupet\FlyImages\FlyImagesServiceProvider::class
```

After composer finishes its work, run this artisan command to generate config file
```bash
$ php artisan vendor:publish
```

Put this line of JavaScript to the \<head\> tag of your template.

```javascript
<script>document.cookie='resolution='+Math.max(screen.width)+'; path=/';</script>
```

You are done.

## Usage

Customize route in flyimages.php config file. This route will be used as a path to the images in your templates.

Now for every image you want to optimize you should append query string to the path. Possible parameters are:
```
lg-w lg-h md-w md-h sm-w sm-h xs-w xs-m
```

Valid values for this parameters are:

* number (integer) of pixels
* text (string) auto - calculate this dimension automatically according to opposite one (the ratio is kept)

If screen resolution is equal or higher than 1200px the lg prefixed parameters are used.

If screen resolution is equal or higher than 992px and smaller than 1200px the md prefixed parameters are used. If there are none, closest parent's one (lg) are used.   

The same pattern of hierarchy is used also for sm and xs prefixed parameters.

A prerequisite for everything to work is that both width and height must be present for certain prefixed parameter. If there are not both present, original image will be delivered.

####Examples

```html
<img src="/optimize/test.jpg?lg-w=400&lg-h=400&md-w=300&md-h=300"/>
<img src="/optimize/test.jpg?lg-w=auto&lg-h=700"/>
<img src="/optimize/test.jpg?sm-w=400&lg-h=400&md-w=300&xs-h=300"/> Wont work
<div style="background-image:url('/optimize/test.jpg?sm-w=256&sm-h=256');"></div>
```

##Caching

All images are cached automatically. The default cache driver is set to file (you can modify it through cache.php config). You can also set image ttl through config file.
This package also supports browser caching with ETag and Last-Modified headers.

##TODO

Since laravel does not support tags for file cache driver some alternative it will have to be figured out. For now you can use artisan cache:clear command to flush cache. Be aware that this command will flush ALL cache.

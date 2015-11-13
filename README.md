# laravel-flyimage
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
       "izupet/laravel-flyimage" : "^1.0.0"
    }
}
```

Update composer from the terminal:
```bash
$ composer update
```

After composer finishes its work, run this artisan command to generate config file
```bash
$ php artisan vendor:publish
```

Add service provider in app.php file:

```php
Izupet\FlyImagesServiceProvider::class
```

Put this line of JavaScript to the \<head\> tag of your template.

```javascript
<script>document.cookie='resolution='+Math.max(screen.width)+'; path=/';</script>
```

You are done.

## Usage

Create new route in route.php file
```php
Route::get('/optimize/{hash}', function($hash) {
    $flyImage = new \Izupet\FlyImages\FlyImages();

    return $flyImage->optimize($hash);
});
```

Now for every image you want to optimize it you should append query string to the path. Possible parameters are:
```
lg-w lg-h md-w md-h sm-w sm-h xs-w xs-m
```

If screen resolution is equal or higher than 1200px the lg prefixed parameters are used.

If screen resolution is equal or higher than 992px and smaller than 1200px the md prefixed parameters are used. If there are none, closest parent's one (lg) are used.   

The same pattern of hierarchy is used also for sm and xs prefixed parameters.

A prerequisite for everything to work is that both width and height must be present for certain prefixed parameter. If there are not both present, original image will be delivered.

####Examples

```html
<img src="/optimize/test.jpg?lg-w=400&lg-h=400&md-w=300&md-h=300"/>
<img src="/optimize/test.jpg?sm-w=400&lg-h=400&md-w=300&xs-h=300"/> Wont work
<div style="background-image:url('/optimize/test.jpg?sm-w=256&sm-h=256');"></div>
```

### Description
GifGenerator is a PHP Project to create animated GIF from multiple images
This Project helps you to create an animated GIF image: give multiple images and their duration !

### Usage

**1 - Creation:**

```php
// All sorted in order to appear.
$frames = [
            0 => [
                'image' => '1.jpg', // path
                'millisecond' => 20 // time
            ],
            1 => [
                'image' => '2.jpg',
                'millisecond' => 80
            ],
        ];


// Initialize and create the GIF !
$gc = new Generator();
$gc->create($frames, 10);
```
The second parameter of create() method allows you to choose the number of loop of your animated gif before it stops.
In the previous example, I chose 10 loops. Set 0 (zero) to get an infinite loop.

**2 - Get the result:**

You can now get the animated GIF binary:

example : 1
```php
$gc->create($frames,5)->show(true); // with headers for displaing
$gc->create($frames,5)->show();  // for binary output
$gc->create($frame,5)->save($path,$filename); // save to directory
```

example : 2
```php
$gc->save($path,$filename); // save to directory
$gc->show(true); // with headers for displaing
$binary = $gc->show(); // for binary output
```

Then you can show it in the navigator:

```php
header('Content-type: image/gif');
header('Content-Disposition: filename="butterfly.gif"');
echo $binary;
exit;
```

<?php



namespace Royal\helpers;


class imageHelper
{

    public $image;
    public $image_type;
    public $height;
    public $width;
    public $fileName;

    function __construct($filename = null)
    {
        if (!empty($filename)) {
            $this->load($filename);
        }
    }

    public  function load($filename)
    {
        $this->fileName = $filename;
        $image_info = getimagesize($filename);
//        var_dump($image_info);
//        exit;
        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        } else {
            throw new Exception("The file you're trying to open is not supported");
        }

    }

    public function save()
    {
        if ($this->image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $this->fileName);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $this->fileName);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $this->fileName);
        }
//        chmod($this->fileName, '777');

    }

    public function output($image_type = IMAGETYPE_JPEG, $quality = 80)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            header("Content-type: image/jpeg");
            imagejpeg($this->image, null, $quality);
        } elseif ($image_type == IMAGETYPE_GIF) {
            header("Content-type: image/gif");
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            header("Content-type: image/png");
            imagepng($this->image);
        }
    }

    public function getWidth()
    {
        return $this->width = imagesx($this->image);
    }

    public function getHeight()
    {
        return $this->height = imagesy($this->image);
    }

    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        $this->resize($width, $height);
    }

    public function square($size)
    {
        $new_image = imagecreatetruecolor($size, $size);

        if ($this->getWidth() > $this->getHeight()) {
            $this->resizeToHeight($size);

            imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            imagecopy($new_image, $this->image, 0, 0, ($this->getWidth() - $size) / 2, 0, $size, $size);
        } else {
            $this->resizeToWidth($size);

            imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            imagecopy($new_image, $this->image, 0, 0, 0, ($this->getHeight() - $size) / 2, $size, $size);
        }

        $this->image = $new_image;
    }

    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;
        $this->resize($width, $height);
    }

    public function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);

        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);

        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    public function cut($x, $y, $width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);

        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);

        imagecopy($new_image, $this->image, 0, 0, $x, $y, $width, $height);

        $this->image = $new_image;
    }

    public function maxarea($width, $height = null)
    {
        $height = $height ? $height : $width;

        if ($this->getWidth() > $width) {
            $this->resizeToWidth($width);
        }
        if ($this->getHeight() > $height) {
            $this->resizeToheight($height);
        }
    }

    public function cutFromCenter($width, $height)
    {

        if ($width < $this->getWidth() && $width > $height) {
            $this->resizeToWidth($width);
        }
        if ($height < $this->getHeight() && $width < $height) {
            $this->resizeToHeight($height);
        }

        $x = ($this->getWidth() / 2) - ($width / 2);
        $y = ($this->getHeight() / 2) - ($height / 2);

        return $this->cut($x, $y, $width, $height);
    }

    public function maxareafill($width, $height, $red = 0, $green = 0, $blue = 0)
    {
        $this->maxarea($width, $height);
        $new_image = imagecreatetruecolor($width, $height);
        $color_fill = imagecolorallocate($new_image, $red, $green, $blue);
        imagefill($new_image, 0, 0, $color_fill);
        imagecopyresampled($new_image, $this->image, floor(($width - $this->getWidth()) / 2), floor(($height - $this->getHeight()) / 2), 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    public function cropImage($request){
//        imagecolortransparent($this->image, imagecolorallocate($this->image, 0, 0, 0));
//        imagealphablending($this->image, false);
//        imagesavealpha($this->image, true);
        $dst_r =   ImageCreateTrueColor(320,320);
        imagecopyresampled ($dst_r, $this->image, 0, 0, $request['x1'], $request['y1'], 320, 320, $request['w'], $request['h']);


    }


}

// Usage:
// Load the original image
//$image = new SimpleImage('lemon.jpg');
//
//// Resize the image to 600px width and the proportional height
//$image->resizeToWidth(600);
//$image->save('lemon_resized.jpg');
//
//// Create a squared version of the image
//$image->square(200);
//$image->save('lemon_squared.jpg');
//
//// Scales the image to 75%
//$image->scale(75);
//$image->save('lemon_scaled.jpg');
//
//// Resize the image to specific width and height
//$image->resize(80,60);
//$image->save('lemon_resized2.jpg');
//
//// Resize the canvas and fill the empty space with a color of your choice
//$image->maxareafill(600,400, 32, 39, 240);
//$image->save('lemon_filled.jpg');
//
//// Output the image to the browser:
//$image->output();


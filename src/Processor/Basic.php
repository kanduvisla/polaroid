<?php

declare(strict_types=1);

namespace Kanduvisla\Polaroid\Processor;

use Imagick;
use ImagickDraw;
use ImagickPixel;

/**
 * Basic Polaroid Picture Processor
 */
class Basic {
    public function create(string $file, string $message, string $newFilename) {
        $wm = new Imagick(__DIR__ . '/../../resources/polaroid.png');
        $im = new Imagick($file);
        $output = new Imagick();
        $output->newImage($wm->getImageWidth(), $wm->getImageHeight(), new ImagickPixel('white'));
        $output->setImageFormat('jpeg');
        $output->setImageCompressionQuality(90);

        $size = 1418;
        $offset = 118;
        $textMaxHeight = ($wm->getImageHeight() - ($offset + $size));
        $textCenterY = $textMaxHeight / 2;

        $w = $im->getImageWidth();
        $h = $im->getImageHeight();
        $offsetX = 0;
        $offsetY = 0;

        $ratio = $w / $h;
        // if ratio > 1 then the image is landscape
        // if ratio < 1 then the image is portrait
        if ($ratio > 1) {
            $w *= $size/$h;
            $h = $size;
            $offsetY = 0;
            $offsetX = ($w - $size)/2;
        } else {
            $h *= $size/$w;
            $w = $size;
            $offsetX = 0;
            $offsetY = ($h - $size)/2;
        }

        $im->resizeImage((int)$w, (int)$h, Imagick::FILTER_POINT, 1);

        $output->compositeImage($im, Imagick::COMPOSITE_COPY, intval($offset - $offsetX), intval($offset - $offsetY));
        $output->compositeImage($wm, Imagick::COMPOSITE_OVER, 0, 0);

        // Add text:
        $draw = new ImagickDraw();
        $pixel = new ImagickPixel('gray');
        $draw->setFillColor('black');
        $draw->setFont('YummyCupcakes.ttf');
        $draw->setFontSize(140);
        $draw->setGravity(Imagick::GRAVITY_SOUTH);

        $textSize = $output->queryFontMetrics($draw, $message);

        // Sanity check for textsize:
        if ($textSize['textWidth'] > $size) {
            $draw->setFontsize(140 * ($size / $textSize['textWidth']));
        }
        $textSize = $output->queryFontMetrics($draw, $message);
        if ($textSize['textHeight'] > $textMaxHeight) {
            $draw->setFontsize(140 * ($textMaxHeight / $textSize['textHeight']));
        }
        $textSize = $output->queryFontMetrics($draw, $message);

        $output->annotateImage($draw, 0, $textCenterY - ($textSize['textHeight'] / 2.5), rand(-3, 3), $message);
        $output->writeImage($newFilename);
    }
}

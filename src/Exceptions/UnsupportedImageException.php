<?php
/**
 * Created by PhpStorm.
 * User: jedy
 * Date: 12/16/17
 * Time: 2:16 PM
 */

namespace Jedy\GifGenerator\Exceptions;



use Exception;
use Throwable;

class UnsupportedImageException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
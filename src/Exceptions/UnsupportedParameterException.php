<?php
/**
 * Created by PhpStorm.
 * User: jedy
 * Date: 12/16/17
 * Time: 11:13 AM
 */

namespace Jedy\GifGenerator\Exceptions;


use Throwable;

class UnsupportedParameterException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
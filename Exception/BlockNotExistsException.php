<?php

namespace Awaresoft\DynamicBlockBundle\Exception;

/**
 * Class TemplateNotFoundException
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class BlockNotExistsException extends \Exception
{
    const MESSAGE = "Block does not exists";

    public function __construct($message = null, $code = 500, \Exception $previous = null)
    {
        $message = $message ? $message : self::MESSAGE;

        parent::__construct($message, $code, $previous);
    }
}
<?php

namespace Awaresoft\DynamicBlockBundle\Exception;

/**
 * Class TemplateNotFoundException
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class TemplateNotFoundException extends \Exception
{
    const MESSAGE = "Template not found. Please correct this issue in admin panel";

    public function __construct($message = null, $code = 500, \Exception $previous = null)
    {
        $message = $message ? $message : self::MESSAGE;

        parent::__construct($message, $code, $previous);
    }
}
<?php

namespace Blog\CoreBundle\Service;

class TemplateService
{
    const CURRENT_TEMPLATE = "ESF";
    const CURRENT_TEMPLATE_ADMIN = "Smart";

    function __construct()
    {

    }

    public static function getTemplate()
    {
        return self::CURRENT_TEMPLATE;
    }

    public static function getTemplateAdmin()
    {
        return self::CURRENT_TEMPLATE_ADMIN;
    }
}
<?php

declare(strict_types=1);

namespace KetPHP\Utils\Common;

enum Cast: string
{
    case INT = 'int';
    case FLOAT = 'float';
    case STRING = 'string';
    case BOOLEAN = 'bool';
    case ARRAY = 'array';
    case OBJECT = 'object';
}

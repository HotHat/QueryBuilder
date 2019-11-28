<?php declare(strict_types=1);


namespace SqlBuilder\scheme;


class Limit extends Column
{
    protected $tag = 'LIMIT';
    protected $escape = false;

}
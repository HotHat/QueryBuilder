<?php declare(strict_types=1);


namespace SqlBuilder\scheme;


class Select extends Column
{
    protected $tag = 'SELECT';


    public function compile() : string
    {
        if (empty($this->contain)) {
            return 'SELECT *';
        }

        return parent::compile();
    }

}
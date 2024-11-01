<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 12:01 PM
 */

namespace App\Templates;


interface TemplateInterface
{
    public function getHeader($data = null);

    public function getColumns();

}
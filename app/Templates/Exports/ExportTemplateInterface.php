<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/31/2019
 * Time: 3:04 PM
 */

namespace App\Templates\Exports;


use App\Templates\TemplateInterface;

interface ExportTemplateInterface extends TemplateInterface
{
    public function getLoop();

}
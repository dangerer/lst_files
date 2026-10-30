<?php
/**
 * Created by PhpStorm.
 * User: sigi
 * Date: 8/19/2019
 * Time: 4:07 PM
 */

namespace GTN\GtnSurvey\Backend;



class TCAConfig
{
    public function importJs()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        echo "<script>console.log('Hello World');</script>";
    }
}





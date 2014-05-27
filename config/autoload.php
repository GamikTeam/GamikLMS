<?php

set_include_path(
        get_include_path()
        .PATH_SEPARATOR."./libs/"
        .PATH_SEPARATOR."./libs/db"
        .PATH_SEPARATOR."./libs/models"
        .PATH_SEPARATOR."./libs/db/contract"
);

function loader($className)
{
   require_once($className.'.php');
}

?>

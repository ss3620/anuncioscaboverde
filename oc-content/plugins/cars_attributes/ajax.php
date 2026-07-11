<?php
require_once 'ModelCars.php' ;

if( Params::getParam("makeId") != '' ) {
  $models = ModelCars::newInstance()->getCarModels( Params::getParam("makeId") );
  echo json_encode($models);
}
?>
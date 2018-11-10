<?php
require_once("model/image.php");
class Home {
    function index() {
        global $data;
        $data = new stdClass();
        $data->content='homeView.php';
        $data->menu['Home']='index.php';
        $date->menu['A propos']="aPropos.php";
		$data->menu['Voir photos']="viewPhoto.php";
        require_once("view/mainView.php");
    }
    function aPropos() {
        global $data;
        $data = new stdClass();
        $data->content='aproposView.php';
        require_once("view/mainView.php");
    }
}
?>
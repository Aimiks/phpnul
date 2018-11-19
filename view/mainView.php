<?php
global $data, $imageId;
include($data->content);
$bool = $data->content != "homeView.php" && $data->content != "aproposView.php" && $data->content != "view/homeView.php" && $data->content != "view/aproposView.php";
if($bool)  {
    print ('
    <div id="form">
    <form action="index.php">
        <select name="category" id="category">
        <option value="all">Toutes</option>');
            foreach ($data->categories as $c) {
                if($data->currentCategory == $c) 
                {
                    print "<option value=\"".$c."\" selected>".$c."</option>\n";
                }
                else 
                {
                    print "<option value=\"".$c."\">".$c."</option>\n";
                }
            }
        print('
        </select> 
        <input type="submit"/>
        <input type="hidden" name="controller" value="photo">
    </form>
</div>');
}
        ?>

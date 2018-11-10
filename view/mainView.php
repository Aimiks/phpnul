<?php
global $data, $imageId;
include($data->content);
?>
<div id="form">
        <?php 
        ?>
            <form action="index.php">
				<select name="category" id="category">
                <option value="all">Toutes</option>
                <?php 
                
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
                ?>
                </select> 
                <input type="submit"/>
                <input type="hidden" name="controller" value="photo">
            </form>
        </div>
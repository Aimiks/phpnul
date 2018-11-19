<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="fr" >
	<head>
		<title>Site SIL3</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" media="screen" title="Normal" />
		</head>
	<body>
		<div id="entete">
			<h1>Site SIL3</h1>
			</div>
		<div id="menu">		
			<h3>Menu</h3>
			<ul>
				<?php 
					foreach ($data->menu as $item => $act) {
						print "<li><a href=\"$act\">$item</a></li>\n";
					}
					?>
				</ul>
			</div>
		
		<div id="corps">
			<?php # mise en place de la vue partielle : le contenu central de la page  
				# Mise en place des deux boutons
				print "<p>\n";
				print "<a href=\"".$data->prevURL."\">Prev</a> ";
				print "<a href=\"".$data->nextURL."\">Next</a>\n";
				print "</p>\n";
				# Affiche l'image avec une reaction au click
				print "<a href=\"index.php?controller=photo&action=zoomIn&imageId=".$imageId."\">\n";
				// Réalise l'affichage de l'image
				print "<img src=\"$data->imageURL\" width=\"$data->size\">\n";
				print "</a>\n";
				print "<br>";
				print "Commentaire : ".$data->actualComment;
				?>		
			</div>
		
		<div id="pied_de_page">
		</div>	   	   	
	</body>
</html>





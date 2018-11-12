<?php
require_once "image.php";
# Le 'Data Access Object' d'un ensemble images
class ImageDAO
{

    # !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    # A MODIFIER EN FONCTION DE VOTRE INSTALLATION
    # !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    # Chemin LOCAL où se trouvent les images
    private $path = "model/IMG";
    # Chemin URL où se trouvent les images
    private $urlPath = "http://iut.local/image/model/IMG/";

    # Tableau pour stocker tous les chemins des images
    private $imgEntry;

    # Lecture récursive d'un répertoire d'images
    # Ce ne sont pas des objets qui sont stockes mais juste
    # des chemins vers les images.
    private function readDir($dir)
    {
        # build the full path using location of the image base
        $fdir = $this->path . $dir;
        if (is_dir($fdir)) {
            $d = opendir($fdir);
            while (($file = readdir($d)) !== false) {
                if (is_dir($fdir . "/" . $file)) {
                    # This entry is a directory, just have to avoid . and .. or anything starts with '.'
                    if (($file[0] != '.')) {
                        # a recursive call
                        $this->readDir($dir . "/" . $file);
                    }
                } else {
                    # a simple file, store it in the file list
                    if (($file[0] != '.')) {
                        $this->imgEntry[] = "$dir/$file";
                    }
                }
            }
        }
    }

    public function __construct()
    {
        $dsn = 'pgsql:host=localhost dbname=phpbase1'; // Data source name
        $user = 'admin'; // Utilisateur
        $pass = 'admin'; // Mot de passe
        try {
            $this->db = new PDO($dsn, $user, $pass); //$db est un attribut privé d'ImageDAO
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    # Retourne le nombre d'images référencées dans le DAO
    public function size()
    {
        return $this->db->query('SELECT count(*) FROM image')->fetchAll(PDO::FETCH_ASSOC)[0]["count"];
    }

    # Retourne un objet image correspondant à l'identifiant
    public function getImage($id)
    {
        $s = $this->db->query('SELECT * FROM image WHERE id=' . $id);
        if ($s) {
            $result = $s->fetchAll(PDO::FETCH_ASSOC)[0];
            return new Image($this->urlPath . $result["path"], $result["id"], $result["category"]);
        } else {
            print "Error in getImage. id=" . $id . "<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }
    }

    # Retourne une image au hazard
    public function getRandomImage()
    {
        $img = $this->getImage(rand(1, $this->size()));
        return $img;
    }

    public function getRandomImageOfCategory($cat)
    {
        if($cat=="all") {
            return $this->getRandomImage();
        }
        $list = $this->getAllImagesIDsOfCategory($cat);
        $img = $this->getImage($list[rand(0, count($list))]);
        return $img;
    }

    # Retourne l'objet de la premiere image
    public function getFirstImage()
    {
        return $this->getImage(1);
    }

    public function getFirstImageOfCategory($cat)
    {
        if($cat!="all") {
            $tmp = $this->db->query('SELECT * FROM image WHERE category = \''.$cat.'\' GROUP BY id ORDER BY id ASC LIMIT 1; ')->fetchAll(PDO::FETCH_ASSOC);
            if(isset($tmp[0])) {
                $result = $tmp[0];
                $img =  new Image($this->urlPath . $result["path"], $result["id"], $result["category"]);
            }
        } else {
            $img = $this->getFirstImage();
        }
        return $img;
    }

    # Retourne l'image suivante d'une image
    public function getNextImage(image $img)
    {
        $id = $img->getId();
        if ($id < $this->size()) {
            $img = $this->getImage($id + 1);
        }
        return $img;
    }

    public function getNextImageOfCategory(image $img, $cat)
    {
        if($cat!="all") {
            $tmp = $this->db->query('SELECT * FROM image WHERE id > '.$img->getId().' and category = \''.$cat.'\' GROUP BY id ORDER BY id ASC LIMIT 1; ')->fetchAll(PDO::FETCH_ASSOC);
            if(isset($tmp[0])) {
                $result = $tmp[0];
                $img =  new Image($this->urlPath . $result["path"], $result["id"], $result["category"]);
            }
        } else {
            $img = $this->getNextImage($img);
        }
        return $img;
    }

    # Retourne l'image précédente d'une image
    public function getPrevImage(image $img)
    {
        $id = $img->getId();
        if ($id > 1) {
            $img = $this->getImage($id - 1);
        }
        return $img;
    }
    public function getPrevImageOfCategory(image $img, $cat)
    {
        if($cat!="all") {
            $tmp = $this->db->query('SELECT * FROM image WHERE id < '.$img->getId().' and category = \''.$cat.'\' GROUP BY id ORDER BY id DESC LIMIT 1; ')->fetchAll(PDO::FETCH_ASSOC);
            if(isset($tmp[0])) {
                $result = $tmp[0];
                $img =  new Image($this->urlPath . $result["path"], $result["id"], $result["category"]);
            }
        } else {
            $img = $this->getPrevImage($img);
        }
        return $img;
    }


    # saute en avant ou en arrière de $nb images
    # Retourne la nouvelle image
    public function jumpToImage(image $img, $nb)
    {
        $id = $img->getId();
        if ($id + $nb < $this->size() && $id + $nb >= 1) {
            $img = $this->getImage($id + $nb);
        }

        return $img;
    }

    # Retourne la liste des images consécutives à partir d'une image
    public function getImageList(image $img, $nb)
    {
        # Verifie que le nombre d'image est non nul
        if (!$nb > 0) {
            debug_print_backtrace();
            trigger_error("Erreur dans ImageDAO.getImageList: nombre d'images nul");
        }
        $id = $img->getId();
        $max = $id + $nb;
        while ($id < $this->size() && $id < $max) {
            $res[] = $this->getImage($id);
            $id++;
        }
        return $res;
    }

    public function getAllImagesOfCategory($cat)
    {
        $tmp = $this->db->query('SELECT id FROM image WHERE category=\''.$cat.'\'')->fetchAll();
        $list = array();
        foreach ($tmp as $t) {
            $list[] = $this->getImage($t['id']);
        }
        return $list;
    }
    public function getAllImagesIDsOfCategory($cat)
    {
        $tmp = $this->db->query('SELECT id FROM image WHERE category=\''.$cat.'\'')->fetchAll();
        $list = array();
        foreach ($tmp as $t) {
            $list[] = $t['id'];
        }
        return $list;
    }

    public function getCategories()
    {
        $tmp = $this->db->query('SELECT distinct category FROM image')->fetchAll();
        $list = array();
        foreach ($tmp as $t) {
            $list[] = $t['category'];
        }
        return $list;
    }
}

# Test unitaire
# Appeler le code PHP depuis le navigateur avec la variable test
# Exemple : http://localhost/image/model/imageDAO.php?test
if (isset($_GET["test"])) {
    echo "<H1>Test de la classe ImageDAO</H1>";
    $imgDAO = new ImageDAO();
    echo "<p>Creation de l'objet ImageDAO.</p>\n";
    echo "<p>La base contient " . $imgDAO->size() . " images.</p>\n";
    $img = $imgDAO->getFirstImage("");
    echo "La premiere image est : " . $img->getURL() . "</p>\n";
    # Affiche l'image
    echo "<img src=\"" . $img->getURL() . "\"/>\n";
}

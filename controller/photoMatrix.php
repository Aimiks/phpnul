<?php
require_once "model/image.php";
require_once "model/imageDAO.php";
class PhotoMatrix
{
    protected $img;

    public function __construct()
    {
        $this->imgDAO = new imageDAO();
    }

    public function getParam()
    {
        // Recupère le numero de l'image courante
        global $imageId, $size, $zoom, $nbImg, $category;
        if (isset($_GET["imageId"])) {
            $imageId = $_GET["imageId"];
        } else {
            $imageId = 1;
        }
        // Recupere la taille courante
        if (isset($_GET["size"])) {
            $size = $_GET["size"];
        } else {
            $size = 480;
        }

        // Recupere la taille courante
        if (isset($_GET["zoom"])) {
            $zoom = $_GET["zoom"];
        } else {
            $zoom = 1.0;
        }
        // Recupere le nombre d'img courant
        if (isset($_GET["nbImg"])) {
            $nbImg = $_GET["nbImg"];
        } else {
            $nbImg = 1;
        }

        //Recuere la catégorie courante
        if (isset($_GET["category"])) 
        {
            $category = $_GET["category"];
        } 
        else 
        {
            $category = "all";
        }
    }
    # Calcule les éléments du menu
    private function setMenuView()
    {
        global $imageId, $size, $zoom, $data, $nbImg, $category;
        $data->menu['Home'] = "index.php";
        $data->menu['First'] = "index.php?controller=photoMatrix&action=first&imageId=$imageId&size=$size&nbImg=$nbImg&category=$category";
        $data->menu['Random'] = "index.php?controller=photoMatrix&action=random&imageId=$imageId&size=$size&nbImg=$nbImg&category=$category";
        # Pour afficher plus d'image passe à un autre controleur
        $data->menu['More'] = "index.php?controller=photoMatrix&action=more&imageId=$imageId&nbImg=$nbImg&size=$size&category=$category";
        $data->menu['Less'] = "index.php?controller=photoMatrix&action=less&imageId=$imageId&nbImg=$nbImg&size=$size&category=$category";

        $data->menu["Zoom +"] = "index.php?controller=photoMatrix&action=zoom&imageId=$imageId&size=$size&zoom=1.25&nbImg=$nbImg&category=$category";
        # Place la même action sur l'image
        //$zoomm=$zoom*0.8;
        $data->menu["Zoom -"] = "index.php?controller=photoMatrix&action=zoom&imageId=$imageId&size=$size&zoom=0.8&nbImg=$nbImg&category=$category";
    }



    # Place les parametres de la vue en fonction des paramètres
    private function setContentView()
    {
        global $imageId, $size, $zoom, $data, $nbImg, $list, $categories, $category;
        # Choisit la vue partielle en image simple
        $data->content = "view/photoMatrixView.php";
        # Trouve l'image courante affichée
        $img = $this->imgDAO->getImage($imageId);
        # Calcul de l'URL de l'image a afficher
        $data->imageURL = $img->getURL();
        
        $data->categories = $this->imgDAO->getCategories();
        # Si une taille est connue dans l'état, la passe cette valeur à la vue
        # Renseigne la vue avec l'URL des boutons 'suivant' et 'précédent'  
        $data->prevURL = "index.php?controller=photoMatrix&action=prev&imageId=$imageId&size=$size&nbImg=$nbImg&category=$category";
        $data->nextURL = "index.php?controller=photoMatrix&action=next&imageId=$imageId&size=$size&nbImg=$nbImg&category=$category";
        $list = $this->imgDAO->getImageListOfCategory($img,$nbImg,$category);
        $data->size = $size / sqrt(count($list));

        foreach ($list as $i) {
            # l'identifiant de cette image $i
            $id=$i->getId();
            $data->imgMatrixURL[] = array($i->getURL());
        }
    }
    // Place les données de la vue
    private function setLoadMainView()
    {
        global $data;
        $data = new StdClass();
        # Calcule le menu
        $this->setMenuView();
        # Calcule le contenu de la vue
        $this->setContentView();
    }
    public function index()
    {
        $this->first();
    }
    public function first()
    {
        # Construit et affiche la vue
        global $imageId, $category;
        $this->getParam();
        $imageId = $this->imgDAO->getFirstImageOfCategory($category)->getId();
        $this->setLoadMainView();
        # charge la vue pour l'afficher
        require_once "view/mainView.php";
    }
    public function next()
    {
        global $imageId, $nbImg, $category;
        $this->getParam();
        # Trouve l'image courante affichée
        $img = $this->imgDAO->getImage($imageId);
        $img = $this->imgDAO->jumpToImageOfCategory($img,$nbImg, $category);
        $imageId = $img->getId();
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";

    }
    public function prev()
    {
        global $imageId, $nbImg, $category;
        $this->getParam();
        # Trouve l'image courante affichée
        $img = $this->imgDAO->getImage($imageId);
        $img = $this->imgDAO->jumpToImageOfCategory($img,-$nbImg, $category);
        $imageId = $img->getId();
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }
    public function more()
    {
        global $imageId, $nbImg, $category;
        $this->getParam();
        # Trouve l'image courante affichée
        $nbImg*=2;
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";

    }
    public function less()
    {
        global $imageId, $nbImg, $category;
        $this->getParam();
        # Trouve l'image courante affichée
        $nbImg/=2;
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }
    public function random()
    {
        global $imageId, $category;
        $this->getParam();
        $img = $this->imgDAO->getRandomImageOfCategory($category);
        $imageId = $img->getId();
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }
    public function zoom()
    {
        global $imageId, $size, $zoom, $category;;
        $this->getParam();
        $size*=$zoom;
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }
}

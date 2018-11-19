<?php
require_once "model/image.php";
require_once "model/imageDAO.php";
class Photo
{
    protected $img;

    public function __construct()
    {
        $this->imgDAO = new imageDAO();
    }

    /**
     * Get the get parameters in the URL
     */
    public function getParam()
    {
        // Recupère le numero de l'image courante
        global $imageId, $size, $zoom, $category, $order;
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

        //Recuere la catégorie courante
        if (isset($_GET["category"])) 
        {
            $category = $_GET["category"];
        } 
        else 
        {
            $category = "all";
        }

        //Recuere la catégorie courante
        if (isset($_GET["order"])) 
        {
            $order = $_GET["order"];
        } 
        else 
        {
            $order = "normal";
        }        
    }
    # Calcule les éléments du menu
    private function setMenuView()
    {
        global $imageId, $size, $zoom, $data, $category, $order;
        $data->menu['Home'] = "index.php";
        $data->menu['First'] = "index.php?controller=photo&action=first&imageId=$imageId&size=$size&category=$category&order=$order";
        $data->menu['Random'] = "index.php?controller=photo&action=random&imageId=$imageId&size=$size&category=$category&order=$order";
        # Pour afficher plus d'image passe à un autre controleur
        $data->menu['More'] = "index.php?controller=photoMatrix&action=more&imageId=$imageId&nbImg=1&category=$category";
        //$zoomp=$zoom*1.25;
        $data->menu["Zoom +"] = "index.php?controller=photo&action=zoom&imageId=$imageId&size=$size&zoom=1.25&category=$category&order=$order";
        # Place la même action sur l'image
        //$zoomm=$zoom*0.8;
        $data->menu["Zoom -"] = "index.php?controller=photo&action=zoom&imageId=$imageId&size=$size&zoom=0.8&category=$category&order=$order";
        $data->menu["J'aime"] = "index.php?controller=photo&action=like&imageId=$imageId&size=$size&category=$category&order=$order";
        $data->menu["Je n'aime pas"] = "index.php?controller=photo&action=dislike&imageId=$imageId&size=$size&category=$category&order=$order";

        $data->menu["Ordre par popularité"] = "index.php?controller=photo&action=first&imageId=$imageId&size=$size&category=$category&order=pop";
        $data->menu["Ordre normal"] = "index.php?controller=photo&action=first&imageId=$imageId&size=$size&category=$category&order=normal";


    }

    # Place les parametres de la vue en fonction des paramètres
    private function setContentView()
    {
        global $imageId, $size, $zoom, $data, $category, $order;
        # Choisit la vue partielle en image simple
        $data->content = "view/photoView.php";
        # Trouve l'image courante affichée
        $img = $this->imgDAO->getImage($imageId);
        # Calcul de l'URL de l'image a afficher
        $data->imageURL = $img->getURL();
        # Si une taille est connue dans l'état, la passe cette valeur à la vue
        $data->size = $size;
        $data->categories = $this->imgDAO->getCategories();
        $data->currentCategory = $category;
        $data->actualComment = $img->getComment();
        $data->likeScore = $img->getNote();
        # Renseigne la vue avec l'URL des boutons 'suivant' et 'précédent'
        $data->prevURL = "index.php?controller=photo&action=prev&imageId=$imageId&size=$size&category=$category&order=$order";
        $data->nextURL = "index.php?controller=photo&action=next&imageId=$imageId&size=$size&category=$category&order=$order";
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
        global $imageId, $category, $order;
        $this->getParam();
        if($order=="pop" && $category=="all") {
            $imageId = $this->imgDAO->getFirstImageOrderByNote()->getId();
        } else {
            $imageId = $this->imgDAO->getFirstImageOfCategory($category,$order)->getId();
        }
        $this->setLoadMainView();
        # charge la vue pour l'afficher
        require_once "view/mainView.php";
    }
    /**
     * Display the next img
     */
    public function next()
    {
        global $imageId, $category, $order;
        $this->getParam();
        # Trouve l'image courante affichée
        $img = $this->imgDAO->getImage($imageId);
        # On passe simplement à l'image suivante    
        if($order=="pop" && $category=="all") {
            $img = $this->imgDAO->getNextImageOrderedByNote($img);
        } else {
            $img = $this->imgDAO->getNextImageOfCategory($img, $category, $order);

        }
        # Positionne l'etat pour indiquer le No de la première image visible
        $imageId = $img->getId();
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }

    public function prev()
    {
        global $imageId, $category, $order;
        $this->getParam();
        # Trouve l'image courante affichée
        $img = $this->imgDAO->getImage($imageId);
        # On passe simplement à l'image suivante
        if($order=="pop" && $category=="all") {
            $img = $this->imgDAO->getPrevImageOrderedByNote($img);
        } else {
            $img = $this->imgDAO->getPrevImageOfCategory($img, $category, $order);
        }
        # Positionne l'etat pour indiquer le No de la première image visible
        $imageId = $img->getId();
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";

    }
    public function random()
    {
        global $imageId, $category;
        $this->getParam();
        //$img = $this->imgDAO->getRandomImage();
        $img = $this->imgDAO->getRandomImageOfCategory($category);
        $imageId = $img->getId();
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }
    public function zoom()
    {
        global $imageId, $size, $zoom;
        $this->getParam();
        $size*=$zoom;
        # Trouve l'image courante affichée
        $img = $this->imgDAO->getImage($imageId);
        $imageId = $img->getId();
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }
    public function zoomIn()
    {
        global $imageId, $size, $zoom;
        $this->getParam();
        $size*=1.25;
        # Trouve l'image courante affichée
        $img = $this->imgDAO->getImage($imageId);
        $imageId = $img->getId();
        # Construit et affiche la vue
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }

    public function like() 
    {
        global $imageId, $size, $zoom;
        $this->getParam();
        $this->imgDAO->like($imageId);
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }
    public function dislike()
    {
        global $imageId, $size, $zoom;
        $this->getParam();
        $this->imgDAO->dislike($imageId);
        $this->setLoadMainView();
        require_once "view/mainView.php";
    }
}

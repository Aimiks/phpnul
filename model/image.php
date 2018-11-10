<?php
  
  # Notion d'image
  class Image {
    private $url=""; 
    private $id=0;
    private $category="none";
    
    function __construct($u,$id, $category) {
      $this->url = $u;
      $this->id = $id;
      $this->category = $category;
    }
    
    # Retourne l'URL de cette image
    function getURL() {
		return $this->url;
    }
    function getId() {
      return $this->id;
    }
    function getCategory() {
      return $this->category;
    }

  }
  
  
?>
<?php
  
  # Notion d'image
  class Image {
    private $url=""; 
    private $id=0;
    private $category="none";
    private $comment="";
    
    function __construct($u,$id, $category, $comment) {
      $this->url = $u;
      $this->id = $id;
      $this->category = $category;
      $this->comment = $comment;
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
    function getCommentary()
    {
        return $this->comment;
    }

  }
  
  
?>
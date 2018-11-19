<?php
  
  # Notion d'image
  class Image {
    private $url=""; 
    private $id=0;
    private $category="none";
    
    function __construct($u,$id, $category, $note, $comment) {
      $this->url = $u;
      $this->id = $id;
      $this->category = $category;
      $this->note = $note;
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

    function getNote() {
      return $this->note;
    }

    function getComment() {
      return $this->comment;
    }

  }
  
  
?>
<?php

class Content extends Model implements ContentFields {
    private $idContent;
    private $content;
    private $langId;
    
    function __construct($content, $langId, $idContent = null) {
        $this->idContent = $idContent;
        $this->content = $content;
        $this->langId = $langId;
    }

    public function getIdContent() {
        return $this->idContent;
    }

    public function getContent() {
        return $this->content;
    }

    public function getLangId() {
        return $this->langId;
    }

    public function setIdContent($idContent) {
        $this->idContent = $idContent;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setLangId($langId) {
        $this->langId = $langId;
    }


}

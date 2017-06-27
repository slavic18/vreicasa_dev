<?php
class CustomTimberPost extends TimberPost {
    public function isFavoritePost(){
        $isFavorite = false;
        if (isset($_COOKIE["favoritePosts"])) {
            $favoritePosts = explode(',', $_COOKIE["favoritePosts"]);
            $isFavorite = in_array($this->id, $favoritePosts);
        }
        return $isFavorite;
    }
}
?>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';

// récupération des catégories de contenu principales
$contentCl = new Env_ContentCategory(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
$contentCl->getCategories();
$contentCl->getContents(); 

// pour récupérer les contenus d'une seule catégorie
$child = $contentCl->getChild(10000);
 
print_r($categories);
 

?>

<select name="categories">
<option value="<?php echo $contentCl->contents[0][0]['code'];?>"><?php echo $contentCl->contents[0][0]['label'];?></option>
<?php foreach($contentCl->categories as $c => $category) { ?>
  <optgroup label="<?php echo $category['label'];?>">
    <?php foreach($contentCl->contents[$category['code']] as $ch => $child) { ?>
      <option value="<?php echo $child['code'];?>"><?php echo $child['label'];?></option>
    <?php } ?>
  </optgroup>
<?php } ?>
</select>
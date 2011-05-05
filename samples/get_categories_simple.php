<?php 
/*  Ce document a pour but d'exploiter des différentes méthodes de récupération des catégories et des contenus (sous-catégories). 
 *  Vous pouvez ainsi télécharger uniquement les catégories, lister les sous-catégories pour une seule ou pour toutes les catégories. 
 */
require_once('../utils/header.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE); 
require_once('../utils/autoload.php');
$categoriesStyle = 'style="font-weight:bolder;"';
// Initialisation de la classe chargée de récupérer les catégories 
$contentCl = new Env_ContentCategory(array("user" => "bartosz", "pass" => "bartOOOSw", "key" => "xx00xxYY__AEZRS"));
// Cette méthode permet de récupérer la liste des catégories
$contentCl->getCategories();
// Celle-ci charge la liste des catégories de contenus (sous-catégories)
$contentCl->getContents(); 
// Grâce à cette méthode vous pouvez récupérer les sous-catégories d'une seule catégorie
$child = $contentCl->getChild(10000);
?>
<p><b>Exemple d'utilisation sur votre site</b></p>
<p><label id="categories" for="categories">Sélectionnez votre catégorie</label>
<select name="categories">
<option value="<?php echo $contentCl->contents[0][0]['code'];?>"><?php echo $contentCl->contents[0][0]['label'];?></option>
<?php foreach($contentCl->categories as $c => $category) { ?>
  <optgroup label="<?php echo $category['label'];?>">
    <?php foreach($contentCl->contents[$category['code']] as $ch => $child) { ?>
      <option value="<?php echo $child['code'];?>"><?php echo $child['label'];?></option>
    <?php } ?>
  </optgroup>
<?php } ?>
</select></p>
<?php require_once('../utils/footer.php');?> 
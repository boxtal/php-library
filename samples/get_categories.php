<?php 
/* Example of use for EnvListPoints class  
 * Make an international order, the difference with a "normal" make order is in the proforma
 */
$folder = '../';
require_once('../utils/header.php');
require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/ContentCategory.php');

// Prepare and execute the request
$env = 'test';
$locale = 'en-US'; // you can change this to 'fr-FR' or 'es-ES' for instance
$lib = new EnvContentCategory($credentials[$env]);
$lib->setEnv($env);
$lib->setLocale($locale);
$lib->getCategories(); // load all content categories
$lib->getContents(); // load all content types
?>
<label>List of contents</label>
<select>
    <option value="<?php echo $lib->contents[0][0]['code'];?>"><?php echo $lib->contents[0][0]['label'];?></option>
    <?php foreach($lib->categories as $c => $category) { ?>
            <optgroup label="<?php echo $category['label'];?>">
                <?php foreach($lib->contents[$category['code']] as $ch => $child) { ?>
                    <option value="<?php echo $child['code'];?>"><?php echo $child['label'];?></option>
                <?php } ?>
            </optgroup>
    <?php } ?>
</select>
<?php 

handle_errors($lib);
require_once('../utils/footer.php');
?> 
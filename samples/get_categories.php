<?php 
/* Example of use for Env_ListPoints class  
 * Make an international order, the difference with a "normal" make order is in the proforma
 */

require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/ContentCategory.php');


// Prepare and execute the request
$env = 'test';
$lib = new Env_ContentCategory($credentials[$env]);
$lib->setEnv($env);
$lib->getCategories();  // load all content categories
$lib->getContents(); 	// load all contents type
?>
<p><label>List of contents</label>
<select>
<option value="<?php echo $lib->contents[0][0]['code'];?>"><?php echo $lib->contents[0][0]['label'];?></option>
<?php foreach($lib->categories as $c => $category) { ?>
		<optgroup label="<?php echo $category['label'];?>">
<?php foreach($lib->contents[$category['code']] as $ch => $child) { ?>
				<option value="<?php echo $child['code'];?>"><?php echo $child['label'];?></option>
<?php } ?>
		</optgroup>
<?php } ?>
</select></p>
<?php 

handle_errors($lib);
?> 
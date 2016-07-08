<?php
use \Emc\ContentCategory;

/* Example of use for ListPoints class
 * Make an international order, the difference with a "normal" make order is in the proforma
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');

// Prepare and execute the request
$lib = new ContentCategory();
$lib->getCategories(); // load all content categories
$lib->getContents(); // load all content types

if (!$lib->curl_error && !$lib->resp_error) {
?>
<h3>API ContentCategory :</h3>
<div class="row well">
    <form class="form-horizontal" role="form">
      <div class="form-group">
        <label class="col-sm-4 control-label">List of categories</label>
        <div class="col-sm-8">
            <select class="form-control">
                <?php
                foreach ($lib->categories as $c => $category) { ?>
                            <option value="<?php echo $category['code'];?>"><?php echo $category['label'];?></option>
                <?php
                } ?>
            </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">List of contents</label>
        <div class="col-sm-8">
            <select class="form-control">
                <option value="<?php echo $lib->contents[0][0]['code'];?>"><?php echo $lib->contents[0][0]['label'];?></option>
                <?php
                foreach ($lib->categories as $c => $category) { ?>
                    <optgroup label="<?php echo $category['label'];?>">
                        <?php
                        foreach ($lib->contents[$category['code']] as $ch => $child) { ?>
                            <option value="<?php echo $child['code'];?>"><?php echo $child['label'];?></option>
                        <?php
                        }  ?>
                    </optgroup>
                <?php
                } ?>
            </select>
        </div>
      </div>
    </form>
</div>
<?php
} else {
    echo '<div class="alert alert-danger">';
    handle_errors($lib);
    echo'</div>';
}
?>
<div class="well well-sm">
    <button type="button" class="btn btn-xs btn-default" id="toogleDebug">
        Toggle Debug
    </button>
    <pre id="debug" style="display: none">
        <?php print_r(array_merge($lib->getApiParam(), array('API response categories :' => $lib->categories , 'API response contents :' => $lib->contents))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');

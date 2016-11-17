<?php
use \Emc\Country;

/* Example of use for Country class
 * Load all available countries or destinations from a country
 */

require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');

// Prepare and execute the request
$lib = new Country();
$lib->getCountries();

if (!$lib->curl_error && !$lib->resp_error) {
?>
<h3>API Country</h3>
<div class="row">
    <form class="form-horizontal well well-sm" role="form">
      <div class="form-group">
        <label class="col-sm-3 control-label">Countries list :</label>
        <div class="col-sm-9">
            <select class="form-control">
                <?php
                foreach ($lib->countries as $c => $country) { ?>
                    <option value="<?php echo $country->code;?>"><?php echo $country->label;?></option>
                <?php
                } ?>
            </select>
        </div>
      </div>
<?php
// Get a country from iso code (Spain)
/* Countries relations by ISO codes.
   For example it contains the relation between the Canary Islands and Spain which haven't the same
   Possible values : 'NL', 'PT', 'DE', 'IT', 'ES', 'VI', 'GR'
*/
$lib->getCountry("ES");
?>
    <div class="form-group">
        <label class="col-sm-3 control-label">Destinations to Spain : <span class="glyphicon glyphicon-question-sign"
        data-container="body" data-toggle="popover" data-placement="bottom" data-content="Countries relations by ISO codes.<br/>
                            For example it contains the relation between the Canary Islands and Spain which haven't the same ISO code<br/>
                            Possible values : 'NL', 'PT', 'DE', 'IT', 'ES', 'VI', 'GR'"></span></label>
        <div class="col-sm-3">
            <ul class="list-group">
            <?php
            foreach ($lib->country as $c => $country) { ?>
                <li class="list-group-item"><?php echo $country->label;?></li>
            <?php
            } ?>
            </ul>
        </div>
    </div>
<?php
$us = $lib->countries['US'];
?>
  <div class="form-group">
      <label class="col-sm-3 control-label">States from United States : <span class="glyphicon glyphicon-question-sign"
      data-container="body" data-toggle="popover" data-placement="bottom" data-content="List of states by country."></span></label>
      <div class="col-sm-9">
          <select class="form-control">
          <?php
          foreach ($us->states as $s => $state) { ?>
              <option value="<?php echo $state->code;?>"><?php echo $state->label;?></option>
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
        <?php print_r(array_merge($lib->getApiParam(), array('API response getCountry("ES") :' =>$lib->country, 'API response getCountries :' =>$lib->countries))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');

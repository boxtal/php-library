<div class="row well well-sm">
    <form class="form-horizontal" role="form">
    <div class="col-xs-3">
        <h4 class="text-center">Shipper : </h4>
        <?php
        foreach ($lib->getParams() as $key => $value) {
            if (strpos($key, 'shipper') !== false) {
            ?>
                <div class="form-group form-group-xs">
                <label for="inputEmail3" class="col-xs-4 control-label"><?php echo $key?></label>
                <div class="col-xs-8">
                  <input type="email" class="form-control input-xs" value="<?php echo $value;?>" >
                </div>
              </div>
            <?php

            }
        }
        ?>
    </div>
    <div class="col-xs-3">
        <h4 class="text-center">Recipient : </h4>
        <?php
        foreach ($lib->getParams() as $key => $value) {
            if (strpos($key, 'recipient')  !== false) {
            ?>
                <div class="form-group form-group-xs">
                <label for="inputEmail3" class="col-xs-4 control-label"><?php echo $key?></label>
                <div class="col-xs-8">
                  <input type="email" class="form-control input-xs" value="<?php echo $value;?>" >
                </div>
              </div>
            <?php

            }
        }
        ?>
    </div>
    <div class="col-xs-3">
        <h4 class="text-center">Parcel : </h4>
        <?php
        foreach ($lib->getParams() as $key => $value) {
            if (strpos($key, 'colis')  !== false) {
            ?>
                <div class="form-group form-group-xs">
                <label for="inputEmail3" class="col-xs-4 control-label"><?php echo $key?></label>
                <div class="col-xs-8">
                  <input type="email" class="form-control input-xs" value="<?php echo $value;?>" >
                </div>
              </div>
            <?php

            }
        }
        ?>
    </div>
    <div class="col-xs-3">
        <h4 class="text-center">Others : </h4>
        <?php
        foreach ($lib->getParams() as $key => $value) {
            if (strpos($key, 'recipient')  === false && strpos($key, 'shipper')  === false && strpos($key, 'colis')  === false
                && strpos($key, 'platform')  === false && strpos($key, 'version')  === false) {
                ?>
                <div class="form-group form-group-xs">
                <label for="inputEmail3" class="col-xs-4 control-label"><?php echo $key?></label>
                <div class="col-xs-8">
                  <input type="email" class="form-control input-xs" value="<?php echo $value;?>" >
                </div>
              </div>
            <?php
            }
        }
        ?>
    </div>
    </form>
</div>
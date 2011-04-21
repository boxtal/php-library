<?php
/** Simple test page to make sure that you can use EnvoiMoinsCher API library. 
 *  Firstly we list needed extensions. After that we check if they are presented in loaded 
 *  extensions.
 */
$needed = array("curl", "libxml");
$extensions = get_loaded_extensions();
$notLoaded = array();
$n = 0;
foreach($needed as $e => $extension) {
  if(!in_array($extension, $extensions)) {
    $notLoaded[$n] = $extension;
    $n++;
  }
}

if(count($notLoaded) > 0) { 
?>
<p>Your PHP configuration misses some extensions : 
  <?php echo implode('<br />', $notLoaded);?>
</p>
<?php
} else {
?>
<p>All extensions were correctly loaded.</p>
<?php
}
?>
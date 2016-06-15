<?php
/** Simple test page to make sure that you can use EnvoiMoinsCher API library.
 *  Firstly we list needed extensions. After that we check if they are presented in loaded
 *  extensions.
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');

$needed = array("curl", "libxml");
$extensions = get_loaded_extensions();
$notLoaded = array();
$n = 0;
foreach ($needed as $e => $extension) {
    if (!in_array($extension, $extensions)) {
        $notLoaded[$n] = $extension;
        $n++;
    }
}

if (count($notLoaded) > 0) {
?>
<p>Vous manquez des extensions suivantes : 
    <?php echo implode('<br />', $notLoaded);?>
</p>
<?php
} else {
?>
<p>Toutes les extensions sont correctement installées.</p>
<a href="test_api.php">Tester les modules.</a>
<?php
}
?>

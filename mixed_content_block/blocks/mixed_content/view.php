<?php
defined('C5_EXECUTE') or die("Access Denied.");
$content = $controller->getContent();
$leftImage = \File::getById($controller->getLeftImage());
$rightImage = \File::getById($controller->getRightImage());

?>


<?php echo $content; ?>
<?php if($leftImage) echo $leftImage->getRelativePath(); ?>
<?php if($rightImage) echo $leftImage->getRelativePath(); ?>
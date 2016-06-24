<?php
/**
 * ------------------------------------------------------------------------
 * JA Events II template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
?>

<?php if ($this->checkSpotlight('spotlight-2', 'position-5, position-6, position-7, position-8')) : ?>
	<!-- SPOTLIGHT 2 -->
	<div class="container t3-sl t3-sl-2">
		<?php $this->spotlight('spotlight-2', 'position-5, position-6, position-7, position-8') ?>
	</div>
	<!-- //SPOTLIGHT 2 -->
<?php endif ?>
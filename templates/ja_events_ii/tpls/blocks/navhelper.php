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

<?php if ($this->countModules('navhelper')) : ?>
	<!-- NAV HELPER -->
	<nav class="wrap t3-navhelper <?php $this->_c('navhelper') ?>">
		<div class="container">
			<jdoc:include type="modules" name="<?php $this->_p('navhelper') ?>" />
		</div>
	</nav>
	<!-- //NAV HELPER -->
<?php endif ?>

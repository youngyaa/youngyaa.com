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
<div id="t3-section" class="t3-section-wrap wrap">
	<?php if ($this->countModules('section')) : ?>
		<jdoc:include type="modules" name="<?php $this->_p('section') ?>" style="T3Section" />
	<?php endif ?>
</div>

<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * marker_class: Class based on the selection of text, none, or icons
 */
?>
<ul class="address-detail dl-horizontal row" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
	<?php if ($this->contact->telephone && $this->params->get('show_telephone')) : ?>
		<li class="col-xs-12 col-sm-4">
			<div>
				<i class="fa fa-phone"></i>
				<span class="contact-telephone" itemprop="telephone">
					<strong>Call Us</strong><?php echo nl2br($this->contact->telephone); ?>
				</span>
			</div>
		</li>
	<?php endif; ?>
	
	<?php if ($this->contact->email_to && $this->params->get('show_email')) : ?>
		<li class="col-xs-12 col-sm-4">
			<div class="highlight">
				<i class="fa fa-envelope-o"></i>
				<div class="contact-emailto">
					<strong>Mail Us</strong><?php echo $this->contact->email_to; ?>
				</div>
			</div>
		</li>
	<?php endif; ?>

	<?php if ($this->contact->fax && $this->params->get('show_fax')) : ?>
		<li class="col-xs-12 col-sm-4">
			<div>
				<i class="fa fa-print"></i>
				<span class="contact-fax" itemprop="faxNumber">
					<strong>Fax</strong><?php echo nl2br($this->contact->fax); ?>
				</span>
			</div>
		</li>
	<?php endif; ?>
	<?php if ($this->contact->mobile && $this->params->get('show_mobile')) :?>
		<li class="col-xs-12 col-sm-4">
			<div>
				<i class="fa fa-phone-square"></i>
				<span class="contact-mobile" itemprop="telephone">
					<strong>Call Us</strong><?php echo nl2br($this->contact->mobile); ?>
				</span>
			</div>
		</li>
	<?php endif; ?>
	<?php if ($this->contact->webpage && $this->params->get('show_webpage')) : ?>
		<li class="col-xs-12 col-sm-4">
			<div>
				<i class="fa fa-globe"></i>
				<span class="contact-webpage">
					<strong>Website</strong><a href="<?php echo $this->contact->webpage; ?>" target="_blank" itemprop="url">
					<?php echo $this->contact->webpage; ?></a>
				</span>
			</div>
		</li>
	<?php endif; ?>
	
</ul>

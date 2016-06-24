REPLACE INTO `#__osmembership_fields` (`id`, `plan_id`, `name`, `title`, `description`, `field_type`, `required`, `values`, `default_values`, `rows`, `cols`, `size`, `css_class`, `extra`, `ordering`, `published`, `datatype_validation`, `field_mapping`, `is_core`, `fee_field`, `fee_values`, `fee_formula`, `profile_field_mapping`, `depend_on_field_id`, `depend_on_options`, `max_length`, `place_holder`, `multiple`, `validation_rules`, `validation_error_message`, `fieldtype`) VALUES
(1, 0, 'first_name', 'First Name', '', 0, 1, '', '', 0, 0, 25, '', '', 1, 1, 0, 'firstname', 1, 0, '', '', NULL, 0, NULL, 0, '', 0, 'validate[required]', '', 'Text'),
(2, 0, 'last_name', 'Last Name', '', 0, 1, '', '', 0, 0, 0, '', '', 2, 1, 0, 'last_name', 1, 0, '', '', NULL, 0, NULL, 0, '', 0, 'validate[required]', '', 'Text'),
(3, 0, 'organization', 'Organization', '', 0, 0, '', '', 0, 0, 0, '', '', 3, 1, 0, 'organization', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, '', '', 'Text'),
(4, 0, 'address', 'Address', '', 0, 1, '', '', 0, 0, 50, 'input-xlarge', '', 4, 1, 0, 'address', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, 'validate[required]', '', 'Text'),
(5, 0, 'address2', 'Address2', '', 0, 0, '', '', 0, 0, 0, 'input-xlarge', '', 5, 1, 0, 'address2', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, '', '', 'Text'),
(6, 0, 'city', 'City', '', 0, 1, '', '', 0, 0, 0, '', '', 6, 1, 0, 'city', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, 'validate[required]', '', 'Text'),
(7, 0, 'state', 'State', '', 0, 1, '', '', 0, 0, 0, '', '', 9, 1, 0, 'state', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, 'validate[required]', '', 'State'),
(8, 0, 'zip', 'Zip', '', 0, 1, '', '', 0, 0, 0, '', '', 7, 1, 0, 'zip', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, 'validate[required]', '', 'Text'),
(9, 0, 'country', 'Country', '', 2, 1, '', '', 0, 0, 0, '', '', 8, 1, 0, 'country', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, 'validate[required]', '', 'Countries'),
(10, 0, 'phone', 'Phone', '', 0, 0, '', '', 0, 0, 0, '', '', 10, 1, 0, 'phone', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, '', '', 'Text'),
(11, 0, 'fax', 'Fax', '', 0, 0, '', '', 0, 0, 0, '', '', 11, 1, 0, 'fax', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, '', '', 'Text'),
(12, 0, 'email', 'Email', '', 0, 1, '', '', 0, 0, 35, '', '', 12, 1, 3, '', 1, 0, '', '', NULL, 0, NULL, 0, '', 0, 'validate[required,custom[email],ajax[ajaxEmailCall]]', '', 'Text'),
(13, 0, 'comment', 'Comment', '', 1, 0, '', '', 5, 40, 0, '', '', 14, 1, 0, 'comment', 1, 0, '', NULL, NULL, 0, NULL, 0, '', 0, '', '', 'Textarea');
DROP TABLE IF EXISTS `#__stn_events`;

DROP TABLE IF EXISTS `#__stn_events_dates`;

DROP TABLE IF EXISTS `#__stn_events_grabers`;

DROP TABLE IF EXISTS `#__stn_events_timeslotes`;

DROP TABLE IF EXISTS `#__stn_event_setting`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_stn_events.%');
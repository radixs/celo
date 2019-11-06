CREATE TABLE `frame_list` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `state` int(1) unsigned NOT NULL DEFAULT '1',
  `number` int(11) NOT NULL,
  `session_started` datetime NOT NULL,
  `created` datetime NOT NULL,
  `ended` datetime DEFAULT NULL,
  `time_taken` int(10) DEFAULT NULL,
  `memory_used` int(15) DEFAULT NULL,
  `session_peak_memory` int(15) DEFAULT NULL
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parameter_name` varchar(100) NOT NULL,
  `parameter_value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `settings` (`id`, `parameter_name`, `parameter_value`) VALUES
(1, 'interval_microseconds', '1000000'),
(2, 'forget_previous_session', '1'),
(3, 'shutdown_flag', '0'),
(4, 'report_last_x_frames', '100'),
(5, 'hold_last_x_frames', '100'),
(6, 'polling_frequency', '1000');



CREATE TABLE `statistics_averages` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `statistics_name` varchar(100) NOT NULL,
  `statistics_value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `statistics_averages` (`id`, `statistics_name`, `statistics_value`) VALUES
(1, 'average_memory_usage', NULL),
(2, 'peak_memory_usage', NULL),
(3, 'frames_per_second', NULL),
(4, 'last_reported_frame_number', NULL);


CREATE TABLE `console_input_history` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `text` varchar(255) NOT NULL,
  `sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
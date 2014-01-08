<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'TYPO3.' . $_EXTKEY,
	'Youtubeapi',
	array(
		'Youtube' => 'list',
		
	),
	// non-cacheable actions
	array(
		'Youtube' => '',
		
	)
);

?>
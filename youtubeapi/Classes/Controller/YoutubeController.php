<?php
namespace TYPO3\Youtubeapi\Controller;
//use TYPO3\Youtubeapi\Lib;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('youtubeapi', 'Classes/Lib/Google_Client.php'));
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('youtubeapi', 'Classes/Lib/Google_YouTubeService.php'));

/**
 *
 *
 * @package youtubeapi
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class YoutubeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
//		$accesstoken = $this->settings['accesstoken'];
		session_start();

		$OAUTH2_CLIENT_ID = '745554625683-2o6prgpova2enrjs0kfed45ed4pfjf3p.apps.googleusercontent.com';
		$OAUTH2_CLIENT_SECRET = 'rfm7HiEQZCriJ24dQ9MEDXcx';

		$client = new \Google_Client();
		$client->setClientId($OAUTH2_CLIENT_ID);
		$client->setClientSecret($OAUTH2_CLIENT_SECRET);
		$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
				FILTER_SANITIZE_URL);
		$client->setRedirectUri($redirect);
		$client->setAccessToken('{"access_token":"ya29.1.AADtN_VGalfgVPSBpjCzaFpnl4KwJGfVL7uHD245kJ0Lb5GyNMIijW62jW1Q7Rph3frYeAI","token_type":"Bearer","expires_in":3600,"refresh_token":"1\/vjRZjO4eRaZhBxX3rh_CaQTMqHxSE-atH2vulYAGIkM","created":1388115498}');
		$youtube = new \Google_YoutubeService($client);
		$result = '';


		if ($client->getAccessToken()) {
			try {
				$channelsResponse = $youtube->channels->listChannels('contentDetails', array(
						'mine' => 'true',
				));

				foreach ($channelsResponse['items'] as $channel) {
					$uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

					$result = $youtube->playlistItems->listPlaylistItems('snippet', array(
							'playlistId' => $uploadsListId,
							'maxResults' => 50
					));
				}
			} catch (Google_ServiceException $e) {
				$result = sprintf('<p>A service error occurred: <code>%s</code></p>',
						htmlspecialchars($e->getMessage()));
			} catch (Google_Exception $e) {
				$result = sprintf('<p>An client error occurred: <code>%s</code></p>',
						htmlspecialchars($e->getMessage()));
			}

		} else {
			$state = mt_rand();
			$client->setState($state);
			$_SESSION['state'] = $state;

			$authUrl = $client->createAuthUrl();
			$result = <<<END
<h3>Authorization Required</h3>
<p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
		}
		$this->view->assign('result', $result);
	}
}
?>

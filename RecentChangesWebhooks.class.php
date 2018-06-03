<?php

class RecentChangesWebhooksHooks {

	public static function onChange( &$change ) {
		global $wgRCWHookUrls;
		foreach ($wgRCWHookUrls as $url) {
			$deeta = json_encode($change->getAttributes());
			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => $url,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $deeta,
				CURLOPT_HTTPHEADER => [
					'Content-Type' => 'application/json'
				],
				CURLOPT_TIMEOUT => 1,
				CURLOPT_RETURNTRANSFER => false,
			]);
			curl_exec($ch);
			curl_close($ch);
		}
	}

}

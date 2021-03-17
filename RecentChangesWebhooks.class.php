<?php
class RecentChangesWebhooksHooks {	
	static function invokeWebhooks(string $jsonEncodedChange) {
		global $wgRCWHookUrls;
		
		foreach ($wgRCWHookUrls as $url) {						
			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => $url,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $jsonEncodedChange,
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

	public static function onChange( &$change ) {
		global $wgRCWHookType;
		
		switch ($wgRCWHookType) {
			case 'job':
				JobQueueGroup::singleton()->push(RecentChangesWebhooksJob::of($change)); break;
			case 'realtime':
				self::invokeWebhooks(json_encode($change)); break;
		}
	}

}

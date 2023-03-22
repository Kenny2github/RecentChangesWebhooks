<?php
use MediaWiki\Hook\RecentChange_saveHook;

class RecentChangesWebhooksHooks implements RecentChange_saveHook {
	private JobQueueGroup $jobQueueGroup;
	
	function __construct(JobQueueGroup $jobQueueGroup) {
		$this->jobQueueGroup = $jobQueueGroup;
	}

	public static function invokeWebhooks(string $jsonEncodedChange) {
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

	public function onRecentChange_save( $recentChange ) {
		global $wgRCWHookType;
				
		switch ($wgRCWHookType) {
			case 'job':
				$this->jobQueueGroup->push(RecentChangesWebhooksJob::of($recentChange)); break;
			case 'realtime':
				self::invokeWebhooks(json_encode($recentChange->getAttributes())); break;
		}
	}
}

<?php
class RecentChangesWebhooksJob extends Job {
	static function Of($change) {
		return new RecentChangesWebhooksJob('', ['change' => json_encode($change->getAttributes())]);
	}
	
	public function __construct($title, $attributes) {
		parent::__construct( 'recentChangesWebhooks', $attributes);
	}
	
	public function run() {
		RecentChangesWebhooksHooks::invokeWebhooks($this->params['change']);
	}
}
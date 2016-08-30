running.$inject = ['$rootScope', '$templateCache', '$location', '$state'];

export default function running($rootScope, $templateCache, $location, $state) {
	$rootScope.$on('$stateChangeStart', function(event, next, current) {
		// テンプレートキャッシュクリア
		$templateCache.remove(current.templateUrl);
	});
	$rootScope.$on('$stateChangeSuccess', function() {
		$templateCache.removeAll();
	});
	$rootScope.$on('$viewContentLoaded', function() {
		$templateCache.removeAll();
	});
}

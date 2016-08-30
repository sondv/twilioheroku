// import footer from './footer';
import sentiment from './sentiment';

export default angular.module('common.directives', [])
	// .controller('FooterCtrl', footer)
	.controller('SentimentCtrl', sentiment)
	// .directive('footer', function() {
	// 	return {
	// 		restrict: 'E',
	// 		scope: {
	// 			value: '=',
	// 			size: '=',
	// 			layout: '@'
	// 		},
	// 		template: require('./templates/footer.html'),
	// 		controller: 'FooterCtrl',
	// 		controllerAs: 'ctrl'
	// 	}
	// })
	.directive('sentiment', function() {
		return {
			restrict: 'E',
			scope: {
				value: '=',
				size: '=',
				layout: '@',
				avater: '@'
			},
			template: require('./templates/sentiment.html'),
			controller: 'SentimentCtrl',
			controllerAs: 'ctrl'
		}
	})
	.name;

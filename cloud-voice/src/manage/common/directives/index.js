import sidenav from './sidenav';
import toolbar from './toolbar';
import twilio from './twilio';

export default angular.module('manage.common.directives', [])
	.controller('ToolbarCtrl', toolbar)
	.controller('SideNavCtrl', sidenav)
	.controller('TwilioCtrl', twilio)
	.directive('manageToolbar', function() {
		return {
			restrict: 'E',
			scope: {
				breadcrumbs: '=',
				showSearch: '=?'
			},
			template: require('./templates/toolbar.html'),
			controller: 'ToolbarCtrl',
			controllerAs: 'ctrl'
		}
	})
	.directive('manageSideNav', function() {
		return {
			restrict: 'E',
			scope: {
				name: '='
			},
			template: require('./templates/sidenav.html'),
			controller: 'SideNavCtrl',
			controllerAs: 'ctrl'
		}
	})
	.directive('manageTwilio', function() {
		return {
			restrict: 'E',
			scope: {
				customerId: '@',
				phones: '='
			},
			template: require('./templates/twilio.html'),
			controller: 'TwilioCtrl',
			controllerAs: 'ctrl'
		}
	})
	.name;

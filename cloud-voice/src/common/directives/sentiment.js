/**
 * 顔アイコンナビ
 */
export default class SentimentCtrl {
	static $inject = ['$scope', 'constant'];

	/**
	 * コンストラクタ
	 */
	constructor($scope, constant) {
		this.$scope = $scope;
		this.constant = constant;

		let self = this;

		// 値
		this.$scope.$watch('value', function(newValue) {
			if (newValue === undefined) {
				return;
			}
			self.value = newValue;
		});

		// サイズ
		this.size = this.$scope.size;
		if (this.size === undefined) {
			this.size = 128;
		}
		this.$scope.sizeIndex = (this.size <= 64) ? 's' : 'l';

		// レイアウト
		this.layout = this.$scope.layout;
		if (this.layout === undefined) {
			this.layout = 'column';
		}

		// md-avaterクラス
		this.avater = this.$scope.avater;
		if (this.avater === undefined) {
			this.avater = false;
		}
	}
}

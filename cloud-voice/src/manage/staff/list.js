export default class StaffListCtrl {
    static $inject = ['$http', '$mdDialog', 'systemService'];

    /**
     * コンストラクタ
     *
     * @param $http
     * @param $mdDialog
     * @param systemService
     */
    constructor($http, $mdDialog, systemService) {
        this.$http = $http;
        this.$mdDialog = $mdDialog;
        this.systemService = systemService;

        this.staffs = [];
        this.current_page = 0;
        this.total_pages = 0;

        this.find();
    }

    /**
     * 検索
     */
    find() {
        let self = this;
        this.$http.get('api/v1/staff/find.json')
            .success(function(result) {
                if (result.staffs) {
                    self.staffs = result.staffs;
                    self.current_page = result.current_page;
                    self.total_pages = result.total_pages;
                }
            })
            .error(function() {});
    }

    /**
     * さらに読込
     */
    more() {
        let self = this;
        this.$http.get('api/v1/staff/find.json', {
                params: {
                    page: self.current_page + 1
                }
            })
            .success(function(result) {
                if (result.staffs) {
                    self.staffs = self.staffs.concat(result.staffs);
                    self.current_page = result.current_page;
                    self.total_pages = result.total_pages;
                }
            })
            .error(function() {});
    }

    /**
     * 詳細ダイアログ表示
     *
     * @param $event
     * @param id
     */
    showInfo($event, id) {
        let self = this;
        this.$mdDialog.show({
                template: require('./templates/info.html'),
                controller: 'StaffInfoCtrl',
                controllerAs: 'ctrl',
                targetEvent: $event,
                locals: {
                    id: id
                },
    			clickOutsideToClose: true
            })
            .then(function(next) {
				if (next === 'edit') {
					self.showForm($event, id);
				} else if (next === 'delete') {
					self.showDelete($event, id);
				} else {
					self.find();
				}
			}, function() {});
    }

    /**
     * 登録・編集ダイアログ表示
     *
     * @param $event
     * @param id
     */
    showForm($event, id) {
        let self = this;
        this.$mdDialog.show({
                template: require('./templates/form.html'),
                controller: 'StaffFormCtrl',
                controllerAs: 'ctrl',
                targetEvent: $event,
                locals: {
                    id: id
                },
    			clickOutsideToClose: true
            })
            .then(function() {
                self.find();
            }, function() {});
    }

    /**
     * 削除ダイアログ表示
     *
     * @param $event
     * @param id
     */
    showDelete($event, id) {
        let self = this;
        var confirm = this.$mdDialog.confirm()
            .textContent('削除します。よろしいですか?')
            .targetEvent($event)
            .ok('はい')
            .cancel('いいえ');

        this.$mdDialog.show(confirm)
            .then(function() {
                self.delete(id);
            }, function() {});
    }

    /**
     * 削除
     *
     * @param id
     */
    delete(id) {
        let self = this;
        this.$http.post('api/v1/staff/delete.json', {
                id: id
            })
            .success(function(result) {
                if (result === null || result.result === 'ng') {
                    self.error = result.error;
                    return;
                }
                angular.forEach(self.staffs, function(staff, index) {
                    if (staff.id === id.toString()) {
                        self.staffs.splice(index, 1);
                        return false;
                    }
                });
            })
    }

}

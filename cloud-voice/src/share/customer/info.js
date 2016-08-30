/**
 * 利用者詳細
 */
export default class CustomerInfoCtrl {
    static $inject = ['$http', '$stateParams', 'constant', 'systemService', 'moment', 'shareServicesAuth'];

    /**
     * コンストラクタ
     *
     * @param $http
     * @param $stateParams
     * @param constant
     * @param systemService
     * @param moment
	 * @param shareServicesAuth
     */
    constructor($http, $stateParams, constant, systemService, moment, shareServicesAuth) {
        this.$http = $http;
		this.$stateParams = $stateParams;
        this.constant = constant;
        this.systemService = systemService;
        this.moment = moment;
		this.shareServicesAuth = shareServicesAuth;

        this.customer = {};
        this.relations = [];
        // this.history = {
        //     data: [],
        //     date: this.moment(this.moment().format('YYYY-MM-01')).toDate(),
        //     isLoading: false
        // };
        // this.hearing = {
        //     data: [],
        //     date: this.moment(this.moment().format('YYYY-MM-01')).toDate(),
        //     isLoading: false
        // };

		// URL受け入れ確認
		if (this.shareServicesAuth.isAccept(this.$stateParams.hash)) {
			this.loadCustomer();
	        // this.loadRelations();
	        // this.loadHistories();
	        // this.loadHearings();
		}
    }

    /**
     * 利用者の読み込み
     */
    loadCustomer() {
		if (this.$stateParams.hash === undefined) {
			return;
		}

        let self = this;
		this.$http.get('api/v1/share/customer.json', {
				params: {
					hash: this.$stateParams.hash,
				}
			})
			.success(function(result) {
				if (result === null || result.result === 'ng') {
					return;
				}

				self.customer = result.customer;
			})
			.error(function() {});
    }

    /**
     * 関係者の読み込み
     */
    loadRelations() {
        let self = this;
        this.$http.get('api/v1/relation/find_emergency_relations.json', {
                params: {
                    customer_id: self.customerId
                }
            })
            .success(function(result) {
                if (result === null || result.result === 'ng') {
                    return;
                }
                self.relations = result.relations;
            })
            .error(function() {});
    }

    /**
     * 通話履歴の読み込み
     */
    loadHistories() {
        this.history.isLoading = true;

        let self = this;
        self.$http.get('api/v1/twilio/one_month.json', {
                params: {
                    customer_id: self.customerId,
                    date: self.moment(self.history.date).format('YYYY-MM-DD')
                }
            })
            .success(function(result) {
                self.history.isLoading = false;

                if (result === null || result.result === 'ng') {
                    return;
                }
                self.history.data = result.histories;
            })
            .error(function() {
                self.history.isLoading = false;
            });
    }

    /**
     * 先月へ（通話履歴）
     */
    prevMonthHistories() {
        this.history.date = this.moment(this.history.date).add(-1, 'M');
        this.loadHistories();
    }

    /**
     * 次月へ（通話履歴）
     */
    nextMonthHistories() {
        this.history.date = this.moment(this.history.date).add(+1, 'M');
        this.loadHistories();
    }

    /**
     * 聞き取り情報の読み込み
     */
    loadHearings() {
        this.hearing.isLoading = true;

        let self = this;
        self.$http.get('api/v1/hearing/one_month.json', {
                params: {
                    customer_id: self.customerId,
                    date: self.moment(self.hearing.date).format('YYYY-MM-DD')
                }
            })
            .success(function(result) {
                self.hearing.isLoading = false;

                if (result === null || result.result === 'ng') {
                    return;
                }
                self.hearing.data = result.hearings;
            })
            .error(function() {
                self.hearing.isLoading = false;
            });
    }

    /**
     * 先月へ（聞き取り情報）
     */
    prevMonthHearings() {
        this.hearing.date = this.moment(this.hearing.date).add(-1, 'M');
        this.loadHearings();
    }

    /**
     * 次月へ（聞き取り情報）
     */
    nextMonthHearings() {
        this.hearing.date = this.moment(this.hearing.date).add(+1, 'M');
        this.loadHearings();
    }

}

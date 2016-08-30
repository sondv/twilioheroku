/**
 * チャートサービス
 */
export default class ChartService {
	static $inject = [];

	/**
	 * コンストラクタ
	 */
	constructor() {
		this.obj = [];
	}

	/**
	 * チャートを描画：バー＆ライン
	 *
	 * @param elementId
	 * @param data
	 * @returns {*}
	 */
	drawComboBarLine(elementId, data = {}) {
		if (this.obj[elementId]) {
			this.obj[elementId].destroy();
		}

		var ctx = document.getElementById(elementId).getContext('2d');
		var obj = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: data.labels,
				datasets: [
					this.generateDataSet1(data.datasets[0].label, data.datasets[0].data),
					this.generateDataSet2(data.datasets[1].label, data.datasets[1].data)
				]
			},
			options: {
				responsive: true,
				legend: this.generateOptionLegend(),
				scales: {
					xAxes: [{
						barPercentage: 0.5
					}],
					yAxes: [
						this.generateOptionScaleYAxe1(data.datasets[0].label, data.datasets[0].data),
						this.generateOptionScaleYAxe2(data.datasets[1].label, data.datasets[1].data)
					]
				}
			}
		});

		this.obj[elementId] = obj;
		return obj;
	}

	/**
	 * チャートを描画：積立バー
	 *
	 * @param elementId
	 * @param data
	 * @returns {*}
	 */
	drawFeverChart(elementId, data = {}) {
		if (this.obj[elementId]) {
			this.obj[elementId].destroy();
		}

		var ctx = document.getElementById(elementId).getContext('2d');
		var obj = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: data.labels,
				datasets: [
					{
						type: 'line',
						label: data.datasets[0].label,
						yAxisID: 'y-axis-2',
						data: data.datasets[0].data,
						backgroundColor: 'rgba(255, 99, 132, 0.5)',
						borderColor: 'rgba(255, 99, 132, 0.8)',
						borderWidth: 3,
						fill: false
					},
					{
						type: 'line',
						label: data.datasets[1].label,
						yAxisID: 'y-axis-2',
						data: data.datasets[1].data,
						backgroundColor: 'rgba(255, 99, 132, 0.5)',
						borderColor: 'rgba(255, 99, 132, 0.8)',
						borderWidth: 3,
						fill: false
					},
					{
						type: 'line',
						label: data.datasets[2].label,
						data: data.datasets[2].data,
						backgroundColor: 'rgba(54, 162, 235, 0.5)',
						borderColor: 'rgba(54, 162, 235, 0.8)',
						borderWidth: 3,
						fill: false
					},
				]
			},
			options: {
				responsive: true,
				legend: this.generateOptionLegend(),
				scales: {
					xAxes: [{
						stacked: true,
						barPercentage: 0.5
					}],
					yAxes: [{
						id: 'y-axis-1',
						type: 'linear',
						display: true,
						position: 'left',
						scaleLabel: {
							display: true,
							fontSize: 16,
							fontStyle: 'normal',
							labelString: '体温',
						},
						ticks: {
							min: 34.0,
							max: 41.0,
							fontSize: 16
						}
                    },
					{
						id: 'y-axis-2',
						type: 'linear',
						display: true,
						position: 'right',
						scaleLabel: {
							display: true,
							fontSize: 16,
							fontStyle: 'normal',
							labelString: '血圧',
						},
						ticks: {
							min: 40,
							max: 180,
							fontSize: 16
						},
						// stacked: true,
                    }]
				}
			}
		});

		this.obj[elementId] = obj;
		return obj;
	}

	/**
	 * チャート：データ１を生成する
	 *
	 * @param label
	 * @param data
	 * @returns {{type: string, label: *, yAxisID: string, data: *, backgroundColor: string, borderColor: string, borderWidth: number, fill: boolean}}
	 */
	generateDataSet1(label, data) {
		return {
			type: 'line',
			label: label,
			lineTension: 0.1,
			yAxisID: 'y-axis-1',
			data: data,
			backgroundColor: 'rgba(54, 162, 235, 0.5)',
			borderColor: 'rgba(54, 162, 235, 0.8)',
			borderWidth: 3,
			fill: false
		};
	}

	/**
	 * チャート：データ２を生成する
	 *
	 * @param label
	 * @param data
	 * @returns {{type: string, label: *, yAxisID: string, data: *, backgroundColor: string, borderColor: string, borderWidth: number}}
	 */
	generateDataSet2(label, data) {
		return {
			type: 'bar',
			label: label,
			yAxisID: 'y-axis-2',
			data: data,
			backgroundColor: 'rgba(255, 99, 132, 0.5)',
			borderColor: 'rgba(255, 99, 132, 0.8)',
			borderWidth: 3
		};
	}

	/**
	 * チャート：スケール.データ１を生成
	 *
	 * @param label
	 * @param data
	 * @returns {{id: string, type: string, display: boolean, position: string, scaleLabel: {display: boolean, fontSize: number, fontStyle: string, labelString: *}, ticks: {suggestedMin: *, suggestedMax: *}}}
	 */
	generateOptionScaleYAxe1(label, data) {
		return {
			id: 'y-axis-1',
			type: 'linear',
			display: true,
			position: 'left',
			scaleLabel: {
				display: true,
				fontSize: 16,
				fontStyle: 'normal',
				labelString: label,
			},
			ticks: this.generateTicks(data, 1, 10)
		};
	}

	/**
	 * チャート：スケール.データ２を生成
	 *
	 * @param label
	 * @param data
	 * @returns {{id: string, type: string, display: boolean, position: string, scaleLabel: {display: boolean, fontSize: number, fontStyle: string, labelString: *}, ticks: {suggestedMin: *, suggestedMax: *}, gridLines: {drawOnChartArea: boolean}}}
	 */
	generateOptionScaleYAxe2(label, data) {
		return {
			id: 'y-axis-2',
			type: 'linear',
			display: true,
			position: 'right',
			scaleLabel: {
				display: true,
				fontSize: 16,
				fontStyle: 'normal',
				labelString: label,
			},
			ticks: this.generateTicks(data, 20, 28),
			gridLines: {
				drawOnChartArea: false
			}
		};
	}

	/**
	 * チャート：タイトルを生成
	 *
	 * @param title
	 * @returns {{display: boolean, text: *, fontSize: number, fontStyle: string}}
	 */
	generateOptionTitle(title) {
		return {
			display: true,
			text: title,
			fontSize: 24,
			fontStyle: 'normal'
		};
	}

	/**
	 * チャート：凡例を生成
	 *
	 * @returns {{position: string}}
	 */
	generateOptionLegend() {
		return {
			position: 'bottom'
		};
	}

	/**
	 * スケールのメモリ設定を生成
	 *
	 * @param dataset
	 * @param min
	 * @param max
	 * @returns {{suggestedMin: *, suggestedMax: *}}
	 */
	generateTicks(dataset, min, max) {
		if (max === undefined && min === undefined) {
			dataset = dataset.filter(function(v) {
				return v !== null;
			});
			var max = Math.max.apply(null, dataset);
			var min = Math.min.apply(null, dataset);
			min = Math.round(min) - 2;
			max = Math.round(max) + 2;
		}
		if (min < 0) {
			min = 0;
		}

		var ticks = {
			min: min,
			max: max,
			fontSize: 16
		};
		if (max - min <= 5) {
			ticks.stepSize = 1;
		}
		return ticks;
	}
}

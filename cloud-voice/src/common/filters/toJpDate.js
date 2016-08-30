toJpDate.$inject = ['moment'];

/**
 * YYYY-MM-DD形式をYYYY年M月D日形式へ変換
 *
 * @returns {Function}
 */
export default function toJpDate(moment) {
    return function(input) {
        if (input === undefined || input === null || input === '') {
			return null;
		}

        moment.updateLocale('ja', {weekdays: ['日','月','火','水','木','金','土']});
		return moment(input).format('YYYY年 M月 D日 (dddd)');
    };
}

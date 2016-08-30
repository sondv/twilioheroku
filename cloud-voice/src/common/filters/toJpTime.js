toJpTime.$inject = ['moment'];

/**
 * hh:ss形式へ変換
 *
 * @returns {Function}
 */
export default function toJpTime(moment) {
    return function(input) {
        if (input === undefined || input === null || input === '') {
			return null;
		}

		return moment(input).format('HH時mm分');
    };
}

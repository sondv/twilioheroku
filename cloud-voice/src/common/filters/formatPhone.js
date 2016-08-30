formatPhone.$inject = ['systemService'];

/**
 * 電話番号を整形して表示
 *
 * @returns {Function}
 */
export default function formatPhone(systemService) {
    return function(value) {
		return systemService.formatPhone(value);
    };
}

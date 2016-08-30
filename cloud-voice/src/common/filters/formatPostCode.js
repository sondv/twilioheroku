formatPostCode.$inject = ['systemService'];

/**
 * 郵便番号を整形して表示
 *
 * @returns {Function}
 */
export default function formatPostCode(systemService) {
    return function(value) {
		return systemService.formatPostCode(value);
    };
}

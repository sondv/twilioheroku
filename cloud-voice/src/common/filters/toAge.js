toAge.$inject = ['moment'];

/**
 * 日付を年齢へ変換
 *
 * @returns {Function}
 */
export default function toAge(moment) {
    return function(input) {
        if (input === undefined || input === null || input === '') {
            return null;
        }

        var ago = moment(input, "YYYY-MM-DD").month(0).from(moment().month(0));
        return ago.replace('年前', '歳');
    };
}
